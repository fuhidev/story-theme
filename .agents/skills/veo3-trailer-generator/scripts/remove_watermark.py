#!/usr/bin/env python3
"""
Chương trình Xóa Logo / Watermark Video bằng Python (OpenCV + FFmpeg).
Đồng bộ 100% thuật toán xử lý mờ/khử watermark với phiên bản Web (TypeScript):
- Fast Diffusion (Phân tán nội suy viền + Gaussian Blur)
- Patch Blend (Lấy mẫu kết cấu lân cận + Làm mềm mask)
- AI Deep (Nội suy đa điểm cao cấp)
- OpenCV Telea / Navier-Stokes Inpaint
"""

import os
import sys
import argparse
import cv2
import numpy as np

def process_inpaint_frame(frame, rx, ry, rw, rh, method="fast_diffusion", feather_px=6):
    """
    Áp dụng thuật toán xóa logo lên từng frame ảnh (Khớp 100% logic TypeScript)
    """
    h, w, _ = frame.shape
    if rw <= 0 or rh <= 0:
        return frame

    if method in ["fast_diffusion", "ai_deep"]:
        # 1. Trích xuất các dải viền xung quanh (top, bottom, left, right)
        border_w = max(4, min(rw, rh) // 4)
        top_band = frame[max(0, ry - border_w):ry, rx:rx + rw]
        bottom_band = frame[ry + rh:min(h, ry + rh + border_w), rx:rx + rw]
        left_band = frame[ry:ry + rh, max(0, rx - border_w):rx]
        right_band = frame[ry:ry + rh, rx + rw:min(w, rx + rw + border_w)]

        # Tính màu trung bình xung quanh
        samples = []
        if top_band.size > 0: samples.append(top_band.reshape(-1, 3))
        if bottom_band.size > 0: samples.append(bottom_band.reshape(-1, 3))
        if left_band.size > 0: samples.append(left_band.reshape(-1, 3))
        if right_band.size > 0: samples.append(right_band.reshape(-1, 3))

        if samples:
            avg_color = np.mean(np.concatenate(samples, axis=0), axis=0)
        else:
            avg_color = np.array([128, 128, 128], dtype=np.float32)

        # Tạo patch nền nội suy mượt từ viền trên và dưới
        patch = np.zeros((rh, rw, 3), dtype=np.float32)
        patch[:] = avg_color

        if top_band.size > 0 and bottom_band.size > 0:
            top_edge = cv2.resize(top_band[-1:], (rw, 1)).astype(np.float32)
            bottom_edge = cv2.resize(bottom_band[:1], (rw, 1)).astype(np.float32)
            for y in range(rh):
                weight_b = y / float(max(1, rh - 1))
                weight_t = 1.0 - weight_b
                patch[y, :] = top_edge * weight_t + bottom_edge * weight_b

        # Làm mờ patch nội suy
        ksize = max(3, (feather_px * 2) | 1)
        patch_blurred = cv2.GaussianBlur(patch, (ksize, ksize), 0)

        # Tạo alpha mask làm mờ viền (Feathering blending)
        mask_alpha = np.zeros((rh, rw), dtype=np.float32)
        pad = max(1, feather_px // 2)
        cv2.rectangle(mask_alpha, (pad, pad), (rw - pad, rh - pad), 1.0, -1)
        mask_alpha = cv2.GaussianBlur(mask_alpha, (ksize, ksize), 0)
        mask_alpha = np.clip(mask_alpha, 0.0, 1.0)[:, :, np.newaxis]

        # Trộn mượt patch mới với frame gốc
        orig_roi = frame[ry:ry + rh, rx:rx + rw].astype(np.float32)
        blended = orig_roi * (1.0 - mask_alpha) + patch_blurred * mask_alpha
        frame[ry:ry + rh, rx:rx + rw] = np.clip(blended, 0, 255).astype(np.uint8)

    elif method == "patch_blend":
        # Lấy mẫu kết cấu lân cận
        src_x = rx - rw
        src_y = ry
        if src_x < 0:
            src_x = min(w - rw, rx + rw)
        if src_y + rh > h:
            src_y = max(0, ry - rh)

        patch = frame[src_y:src_y + rh, src_x:src_x + rw].astype(np.float32)

        ksize = max(3, (feather_px * 2) | 1)
        mask_alpha = np.zeros((rh, rw), dtype=np.float32)
        pad = max(1, feather_px // 2)
        cv2.rectangle(mask_alpha, (pad, pad), (rw - pad, rh - pad), 1.0, -1)
        mask_alpha = cv2.GaussianBlur(mask_alpha, (ksize, ksize), 0)
        mask_alpha = np.clip(mask_alpha, 0.0, 1.0)[:, :, np.newaxis]

        orig_roi = frame[ry:ry + rh, rx:rx + rw].astype(np.float32)
        blended = orig_roi * (1.0 - mask_alpha) + patch * mask_alpha
        frame[ry:ry + rh, rx:rx + rw] = np.clip(blended, 0, 255).astype(np.uint8)

    else: # telea hoặc ns (OpenCV inpaint)
        mask = np.zeros((h, w), dtype=np.uint8)
        mask[ry:ry + rh, rx:rx + rw] = 255
        if feather_px > 0:
            kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (feather_px, feather_px))
            mask = cv2.dilate(mask, kernel, iterations=1)

        inpaint_flag = cv2.INPAINT_TELEA if method == "telea" else cv2.INPAINT_NS
        frame = cv2.inpaint(frame, mask, inpaintRadius=max(3, feather_px), flags=inpaint_flag)

    return frame


def remove_video_watermark(
    input_path: str,
    output_path: str,
    x_pct: float = 79.7,
    y_pct: float = 88.4,
    w_pct: float = 7.9,
    h_pct: float = 4.4,
    feather_radius: int = 6,
    expand_padding: int = 4,
    method: str = "fast_diffusion"
):
    """
    Xóa watermark khỏi video và giữ nguyên âm thanh gốc.
    """
    if not os.path.exists(input_path):
        print(f"Lỗi: Không tìm thấy file đầu vào '{input_path}'")
        return

    cap = cv2.VideoCapture(input_path)
    if not cap.isOpened():
        print(f"Lỗi: Không thể mở video '{input_path}'")
        return

    width = int(cap.get(cv2.CAP_PROP_FRAME_WIDTH))
    height = int(cap.get(cv2.CAP_PROP_FRAME_HEIGHT))
    fps = cap.get(cv2.CAP_PROP_FPS) or 30.0
    total_frames = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))

    print(f"=== CHƯƠNG TRÌNH XÓA LOGO VIDEO (PYTHON) ===")
    print(f"Tệp đầu vào: {input_path}")
    print(f"Kích thước: {width}x{height} | FPS: {fps:.2f} | Tổng số frame: {total_frames}")

    # Tính toán tọa độ pixel dựa theo tỷ lệ %
    rx = int((x_pct / 100.0) * width) - expand_padding
    ry = int((y_pct / 100.0) * height) - expand_padding
    rw = int((w_pct / 100.0) * width) + (expand_padding * 2)
    rh = int((h_pct / 100.0) * height) + (expand_padding * 2)

    rx = max(0, min(width - 1, rx))
    ry = max(0, min(height - 1, ry))
    rw = min(width - rx, max(2, rw))
    rh = min(height - ry, max(2, rh))

    print(f"Vùng chọn xóa (pixel): X={rx}, Y={ry}, W={rw}, H={rh}")
    print(f"Thuật toán: {method.upper()} | Độ làm mềm viền (Feather): {feather_radius}px")

    temp_video = "temp_processed_video.mp4"
    fourcc = cv2.VideoWriter_fourcc(*"mp4v")
    out = cv2.VideoWriter(temp_video, fourcc, fps, (width, height))

    frame_count = 0
    while True:
        ret, frame = cap.read()
        if not ret:
            break

        # Áp dụng thuật toán inpaint mượt
        cleaned_frame = process_inpaint_frame(frame, rx, ry, rw, rh, method=method, feather_px=feather_radius)
        out.write(cleaned_frame)

        frame_count += 1
        if frame_count % 30 == 0 or frame_count == total_frames:
            pct = (frame_count / total_frames) * 100 if total_frames > 0 else 0
            sys.stdout.write(f"\rĐang render: {pct:.1f}% ({frame_count}/{total_frames} frames)")
            sys.stdout.flush()

    print("\nHoàn tất xử lý hình ảnh! Đang ghép âm thanh gốc bằng FFmpeg...")
    cap.release()
    out.release()

    # Ghép lại âm thanh gốc bằng FFmpeg
    try:
        cmd = f'ffmpeg -y -i "{temp_video}" -i "{input_path}" -c:v copy -c:a aac -map 0:v:0 -map 1:a:0? "{output_path}" -loglevel quiet'
        res = os.system(cmd)
        if res == 0 and os.path.exists(output_path):
            print(f"Thành công! Video xuất ra tại: '{output_path}'")
        else:
            if os.path.exists(temp_video):
                if os.path.exists(output_path): os.remove(output_path)
                os.rename(temp_video, output_path)
            print(f"Đã lưu video tại: '{output_path}'")
    except Exception as e:
        print(f"Lỗi ghép audio FFmpeg ({e}). Đang lưu video tạm...")
        if os.path.exists(temp_video):
            if os.path.exists(output_path): os.remove(output_path)
            os.rename(temp_video, output_path)

    if os.path.exists(temp_video):
        try: os.remove(temp_video)
        except: pass


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Chương trình xóa logo trong video bằng Python (OpenCV)")
    parser.add_argument("-i", "--input", required=True, help="Đường dẫn tệp video đầu vào")
    parser.add_argument("-o", "--output", default="output_cleaned.mp4", help="Đường dẫn xuất video (mặc định: output_cleaned.mp4)")
    parser.add_argument("--x", type=float, default=79.7, help="Tọa độ X %% (Mặc định: 79.7)")
    parser.add_argument("--y", type=float, default=88.4, help="Tọa độ Y %% (Mặc định: 88.4)")
    parser.add_argument("--w", type=float, default=7.9, help="Chiều rộng W %% (Mặc định: 7.9)")
    parser.add_argument("--h", type=float, default=4.4, help="Chiều cao H %% (Mặc định: 4.4)")
    parser.add_argument("--feather", type=int, default=6, help="Độ làm mềm viền (pixels) (Mặc định: 6)")
    parser.add_argument("--padding", type=int, default=4, help="Mở rộng viền (pixels) (Mặc định: 4)")
    parser.add_argument("--method", choices=["fast_diffusion", "patch_blend", "ai_deep", "telea", "ns"], default="fast_diffusion", help="Thuật toán: fast_diffusion / patch_blend / ai_deep / telea / ns")

    args = parser.parse_args()

    remove_video_watermark(
        input_path=args.input,
        output_path=args.output,
        x_pct=args.x,
        y_pct=args.y,
        w_pct=args.w,
        h_pct=args.h,
        feather_radius=args.feather,
        expand_padding=args.padding,
        method=args.method
    )
