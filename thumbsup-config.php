<?php
/**
 * thumbsup-config.php
 *
 * Example ThumbsUp configuration file.
 *
 * @created   4/2/14 11:52 AM
 * @author    Damian Taggart
 * @copyright Copyright (c) 2006-2023
 * @link      https://github.com/attackant/thumbsup
 *
 */

// Max sizes
if (!defined('MAX_WIDTH'))
    define("MAX_WIDTH", 3600);
if (!defined('MAX_HEIGHT'))
    define('MAX_HEIGHT', 3600);
if (!defined('MAX_FILE_SIZE'))
    define('MAX_FILE_SIZE', 20971520);

/*
 *  External Sites
 */
global $ALLOWED_SITES;
$ALLOWED_SITES = [
    'flickr.com',
    'staticflickr.com',
    'img.youtube.com',
    'upload.wikimedia.org',
    'imgur.com',
];

// The rest of the code in this config only applies to Apache mod_userdir  (URIs like /~username)

if (thumbsup_in_url('~')) {
    $_SERVER['DOCUMENT_ROOT'] = thumbsup_find_wp_root();
}

/**
 *  We need to set DOCUMENT_ROOT in cases where /~username URLs are being used.
 *  In a default WordPress install this should result in the same value as ABSPATH
 *  but ABSPATH and all WP functions are not accessible in the current scope.
 *
 *  This code should work in 99% of cases.
 *
 * @param int $levels
 *
 * @return bool|string
 */
function thumbsup_find_wp_root($levels = 9): bool|string
{

    $dir_name = dirname(__FILE__) . '/';

    for ($i = 0; $i <= $levels; $i++) {
        $path = realpath($dir_name . str_repeat('../', $i));
        if (file_exists($path . '/wp-config.php')) {
            return $path;
        }
    }

    return FALSE;
}

/**
 *
 * Gets the current URL.
 *
 * @return string
 */
function thumbsup_get_url(): string
{
    if (($_SERVER["HTTPS"] == "on")) {
        $s = empty($_SERVER["HTTPS"]) ? '' : "s";
    } else {
        $s = empty($_SERVER["HTTPS"]) ? '' : "";
    }
    $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), offset: 0, length: strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), needle: "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);

    return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

/**
 *
 * Checks to see if $text is in the current URL.
 *
 * @param $text
 *
 * @return bool
 */
function thumbsup_in_url($text): bool
{
    return (bool)stristr(thumbsup_get_url(), $text);
}
