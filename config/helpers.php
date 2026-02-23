<?php
/**
 * Text normalization helpers for uniform data (capitalization, trimming).
 */

/**
 * Trim and collapse multiple spaces to one.
 */
function normalizeSpaces($str) {
    if ($str === null || $str === '') return $str;
    return trim(preg_replace('/\s+/', ' ', $str));
}

/**
 * Title case: first letter of each word uppercase, rest lowercase.
 * Use for: item names, categories, user names.
 */
function normalizeTitleCase($str) {
    if ($str === null || $str === '') return $str;
    $str = normalizeSpaces($str);
    return ucwords(mb_strtolower($str, 'UTF-8'));
}

/**
 * Sentence case: first letter of string uppercase, rest unchanged (optional lowercase).
 * Use for: descriptions, longer text.
 */
function normalizeSentenceCase($str) {
    if ($str === null || $str === '') return $str;
    $str = normalizeSpaces($str);
    if ($str === '') return $str;
    return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($str, 1, null, 'UTF-8');
}

/**
 * Lowercase for consistent codes (e.g. unit of measure: "Set" -> "set").
 */
function normalizeLowerCase($str) {
    if ($str === null || $str === '') return $str;
    return strtolower(trim($str));
}
