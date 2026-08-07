<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Splits rendered post HTML into two chunks at roughly the given
 * percentage of text length, breaking only at a top-level element
 * boundary (never inside a paragraph/tag) so the markup stays valid.
 * Used to slot the "keep reading" carousel in partway through a chapter
 * instead of only at the very end.
 *
 * Returns array( $before_html, $after_html ). $after_html is '' when the
 * content is too short to split meaningfully (e.g. a one-paragraph story
 * intro) -- callers should treat an empty second half as "don't show the
 * mid-content carousel here".
 */
function nvl_split_content_at_percent( $html, $percent = 0.3 ) {
    $html = trim( $html );
    if ( $html === '' ) return array( '', '' );

    $dom = new DOMDocument();
    libxml_use_internal_errors( true );
    $dom->loadHTML(
        '<?xml encoding="utf-8" ?><div id="nvl-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $root = $dom->getElementById( 'nvl-root' );
    if ( ! $root || ! $root->hasChildNodes() ) return array( $html, '' );

    $children = iterator_to_array( $root->childNodes );
    if ( count( $children ) < 3 ) return array( $html, '' ); // too short to split usefully

    $lengths = array();
    $total   = 0;
    foreach ( $children as $child ) {
        $len = mb_strlen( trim( $child->textContent ) );
        $lengths[] = $len;
        $total += $len;
    }
    if ( $total === 0 ) return array( $html, '' );

    $target  = $total * $percent;
    $running = 0;
    $split_at = count( $children ) - 1;
    foreach ( $lengths as $i => $len ) {
        $running += $len;
        if ( $running >= $target ) { $split_at = $i + 1; break; }
    }
    $split_at = max( 1, min( $split_at, count( $children ) - 1 ) );

    $before = '';
    $after  = '';
    foreach ( $children as $i => $child ) {
        $out = $dom->saveHTML( $child );
        if ( $i < $split_at ) { $before .= $out; } else { $after .= $out; }
    }
    return array( $before, $after );
}
