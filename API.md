# Noirvella Stories REST API

Custom REST endpoints for creating **Stories** and **Chapters** in bulk on a Noirvella Stories WordPress site, instead of one `wp_insert_post` round-trip per chapter through the block editor.

Source: [`plugin/noirvella-stories/includes/rest-api.php`](plugin/noirvella-stories/includes/rest-api.php)

Registered under the WordPress REST API namespace **`noirvella/v1`**.

## Data model

Stories and Chapters share a single custom post type, `nvl_story`:

- A **Story** is a root post (`post_parent = 0`).
- A **Chapter** is a post whose `post_parent` points at a Story root. Chapters cannot themselves have a parent that is another chapter — only one level of nesting is allowed.
- Chapter ordering within a story is the post's `menu_order` (ascending).

## Base URL

```
https://<your-site>/wp-json/noirvella/v1
```

Example used throughout this document: `https://story.icestech.info/wp-json/noirvella/v1`.

## Authentication

Both endpoints require an authenticated WordPress user — there is no public/anonymous access. Use [WordPress Application Passwords](https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/) over HTTP Basic Auth:

```
Authorization: Basic base64(username:application_password)
```

With `curl`, the `-u` flag handles the encoding for you:

```bash
curl -u 'username:xxxx xxxx xxxx xxxx xxxx xxxx' https://your-site/wp-json/noirvella/v1/stories
```

Generate an application password under **wp-admin → Users → your profile → Application Passwords**. The password is shown once, with spaces for readability — `curl -u` accepts it with or without the spaces.

Requests without valid credentials receive `401 Unauthorized`. Requests from an authenticated user lacking the required capability (see below) receive `403 Forbidden`.

## Permissions

| Endpoint | Required capability |
|---|---|
| `POST /stories` | `publish_posts` |
| `POST /stories/{id}/chapters` | `publish_posts`, and `edit_post` on the target story (when it exists) |

In practice this means the authenticated user needs at least the **Author** role (Contributors have `edit_posts` but not `publish_posts` and will be rejected). Editors and Administrators also qualify.

---

## `POST /stories`

Creates a new Story, and optionally its Chapters, in a single request.

### Request body

| Field | Type | Required | Default | Notes |
|---|---|---|---|---|
| `title` | string | **Yes** | — | Story title. Empty/whitespace-only is rejected. |
| `content` | string | No | `""` | Story root content (the intro/synopsis page). Sanitized with `wp_kses_post`. |
| `excerpt` | string | No | `""` | Story excerpt/teaser. Sanitized with `sanitize_textarea_field`. |
| `status` | string | No | `"draft"` | One of `publish`, `draft`, `pending`, `private`, `future`. Any other value silently falls back to `draft`. |
| `chapters` | array of objects | No | `[]` | See [Chapter object](#chapter-object) below. Chapters are inserted in array order; the first chapter gets `menu_order = 1`, the second `2`, etc., **unless** the object provides its own `menu_order`. |

#### Chapter object

Each entry in `chapters` accepts the same fields as the [add-chapter endpoint](#post-storiesidchapters) body:

| Field | Type | Required | Default |
|---|---|---|---|
| `title` | string | **Yes** | — |
| `content` | string | No | `""` |
| `excerpt` | string | No | `""` |
| `status` | string | No | `"draft"` |
| `menu_order` | integer | No | its 1-based position in the `chapters` array |

> ⚠️ **Chapter status does not inherit from the story.** If you publish the story (`"status": "publish"`) but omit `status` on a chapter object, that chapter is created as a `draft`. Set `status` explicitly on every chapter object if you want the whole story live in one call.

### Response

**`201 Created`** on success:

```json
{
  "story_id": 35,
  "title": "Check Your Closet, Sir",
  "link": "https://story.icestech.info/blog/check-your-closet-sir/",
  "chapters": [
    {
      "chapter_id": 36,
      "title": "Check Your Closet, Sir",
      "link": "https://story.icestech.info/blog/check-your-closet-sir/check-your-closet-sir/",
      "menu_order": 1
    },
    {
      "chapter_id": 37,
      "title": "The Cedar Door",
      "link": "https://story.icestech.info/blog/check-your-closet-sir/the-cedar-door/",
      "menu_order": 2
    }
  ]
}
```

> ⚠️ **`link` is unreliable for non-published posts.** WordPress does not assign a URL slug (`post_name`) to a post until it's published, so the `link` returned for a `draft`/`pending` story or chapter will be a broken URL with an empty slug segment (e.g. `.../blog//`). This is native WordPress behavior, not specific to this endpoint — re-fetch the post (or check `link` again) after publishing to get the real permalink.

### Error responses

| HTTP status | `code` | When |
|---|---|---|
| `400` | `nvl_missing_title` | `title` is empty/whitespace, on the story or on any chapter object. |
| `400` | `rest_missing_callback_param` | `title` was omitted from the request body entirely (fails WP's built-in required-arg check before the callback runs). |
| `400` | `nvl_chapter_failed` | The story was created successfully, but a chapter in the `chapters` array failed to insert. The response `data` includes `story_id` and `created_chapters` (the chapters that *did* succeed before the failure) so the caller can decide whether to retry just the missing chapter(s) via the [add-chapter endpoint](#post-storiesidchapters) instead of re-creating the whole story. |
| `401` | `rest_forbidden` | No/invalid credentials. |
| `403` | `rest_forbidden` | Authenticated, but the user lacks `publish_posts`. |
| `500` | *(WordPress error code)* | `wp_insert_post` failed for a reason other than validation (rare — surfaced as-is from WordPress). |

### Examples

Create a story with two chapters, everything published immediately:

```bash
curl -u 'fortool:xxxx xxxx xxxx xxxx xxxx xxxx' \
  -X POST https://story.icestech.info/wp-json/noirvella/v1/stories \
  -H "Content-Type: application/json" \
  -d '{
    "title": "API Published Story",
    "content": "Intro content for the story.",
    "excerpt": "A short teaser.",
    "status": "publish",
    "chapters": [
      { "title": "Chapter One", "content": "Chapter one body.", "status": "publish" },
      { "title": "Chapter Two", "content": "Chapter two body.", "status": "publish" }
    ]
  }'
```

```json
{
  "story_id": 33,
  "title": "API Published Story",
  "link": "https://story.icestech.info/blog/api-published-story/",
  "chapters": [
    {
      "chapter_id": 34,
      "title": "Chapter One",
      "link": "https://story.icestech.info/blog/api-published-story/chapter-one/",
      "menu_order": 1
    }
  ]
}
```

Missing title (validation error, request never reaches the callback):

```bash
curl -u 'fortool:xxxx xxxx xxxx xxxx xxxx xxxx' \
  -X POST https://story.icestech.info/wp-json/noirvella/v1/stories \
  -H "Content-Type: application/json" \
  -d '{"content":"no title here"}'
```

```json
{
  "code": "rest_missing_callback_param",
  "message": "Missing parameter(s): title",
  "data": { "status": 400, "params": ["title"] }
}
```

Unauthenticated request:

```bash
curl -X POST https://story.icestech.info/wp-json/noirvella/v1/stories \
  -H "Content-Type: application/json" \
  -d '{"title":"anon"}'
```

```json
{ "code": "rest_forbidden", "message": "Sorry, you are not allowed to do that.", "data": { "status": 401 } }
```

---

## `POST /stories/{id}/chapters`

Adds a single Chapter under an existing Story.

### Path parameters

| Parameter | Type | Description |
|---|---|---|
| `id` | integer | Post ID of an existing **Story root** (`post_parent = 0`, `post_type = nvl_story`). |

### Request body

| Field | Type | Required | Default | Notes |
|---|---|---|---|---|
| `title` | string | **Yes** | — | Chapter title. |
| `content` | string | No | `""` | Sanitized with `wp_kses_post`. |
| `excerpt` | string | No | `""` | Sanitized with `sanitize_textarea_field`. |
| `status` | string | No | `"draft"` | One of `publish`, `draft`, `pending`, `private`, `future`; invalid values fall back to `draft`. |
| `menu_order` | integer | No | `0` | Sort position within the story. |

### Response

**`201 Created`**:

```json
{
  "chapter_id": 32,
  "title": "Chapter Three",
  "link": "https://story.icestech.info/blog/api-test-story/chapter-three/",
  "menu_order": 0
}
```

### Error responses

| HTTP status | `code` | When |
|---|---|---|
| `400` | `nvl_missing_title` | `title` is empty/whitespace. |
| `400` | `nvl_not_a_story` | `id` refers to a post that is itself a chapter (has a non-zero `post_parent`) — chapters cannot have chapters. |
| `404` | `nvl_story_not_found` | `id` does not correspond to any `nvl_story` post. |
| `401` | `rest_forbidden` | No/invalid credentials. |
| `403` | `rest_forbidden` | Authenticated, but the user lacks `publish_posts`, or lacks `edit_post` on an *existing, valid* target story. |

> Note on `403` vs `404`: the permission check only evaluates capability against a target that actually exists and is a Story; if `id` doesn't resolve to a valid story at all, the request is allowed through to the callback so it can return the more specific `404 nvl_story_not_found` instead of a generic `403`.

### Examples

Add a chapter to story `29`:

```bash
curl -u 'fortool:xxxx xxxx xxxx xxxx xxxx xxxx' \
  -X POST https://story.icestech.info/wp-json/noirvella/v1/stories/29/chapters \
  -H "Content-Type: application/json" \
  -d '{"title":"Chapter Three","content":"Chapter three body.","status":"draft"}'
```

Attempt to target a chapter instead of a story (rejected):

```bash
curl -u 'fortool:xxxx xxxx xxxx xxxx xxxx xxxx' \
  -X POST https://story.icestech.info/wp-json/noirvella/v1/stories/30/chapters \
  -H "Content-Type: application/json" \
  -d '{"title":"Should fail"}'
```

```json
{
  "code": "nvl_not_a_story",
  "message": "Target post is a chapter, not a Story — chapters cannot have chapters.",
  "data": { "status": 400 }
}
```

Nonexistent story:

```bash
curl -u 'fortool:xxxx xxxx xxxx xxxx xxxx xxxx' \
  -X POST https://story.icestech.info/wp-json/noirvella/v1/stories/99999/chapters \
  -H "Content-Type: application/json" \
  -d '{"title":"orphan"}'
```

```json
{
  "code": "nvl_story_not_found",
  "message": "Story not found.",
  "data": { "status": 404 }
}
```

---

## Quick reference: error codes

| `code` | HTTP status | Meaning |
|---|---|---|
| `nvl_missing_title` | 400 | A story or chapter `title` was empty. |
| `nvl_not_a_story` | 400 | The `{id}` in `/stories/{id}/chapters` points at a chapter, not a story root. |
| `nvl_chapter_failed` | 400 | Bulk story creation succeeded but one of the `chapters` entries failed to insert. |
| `nvl_story_not_found` | 404 | The `{id}` in `/stories/{id}/chapters` doesn't exist as an `nvl_story` post. |
| `rest_missing_callback_param` | 400 | A required field (`title`) was omitted from the JSON body entirely. |
| `rest_forbidden` | 401 / 403 | Missing/invalid auth (401), or authenticated user lacks the required capability (403). |

## Related read-only endpoints

This plugin only adds write endpoints for stories/chapters. To read story/chapter data, use WordPress's built-in REST controller for the `nvl_story` post type (enabled via `show_in_rest`), e.g.:

```
GET /wp-json/wp/v2/nvl_story/{id}
GET /wp-json/wp/v2/nvl_story?parent={story_id}   # chapters of a story
```
