<?php

namespace Pandao\Common\Utils;

/**
 * Class UrlUtils
 * - checkURI
 * - err404
 * - getUrl
 * - checkReferer
 * - isXhr
 * - getFromTemplate
 */

class UrlUtils
{
    /**
     * Ensures the current URI matches the expected value, exiting if not.
     *
     * @param string $uri The expected URI.
     * @return void
     */
    public static function checkURI($uri)
    {
        if ($_SERVER['REQUEST_URI'] != $uri) {
            exit;
        }
    }

    /**
     * Triggers a 404 error and redirects to a custom 404 page.
     *
     * @param string $message The error message to log.
     * @param string|null $url The URL to redirect to (optional).
     * @return void
     */
    public static function err404($message = '', $url = null)
    {
        if (!$url) $url = DOCBASE . (PMS_LANG_ENABLED ? PMS_LANG_TAG . '/' : '') . '404';
        http_response_code(404);
        header('HTTP/1.0 404 Not Found');
        error_log($message);
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Returns the current URL, optionally including the request URI.
     *
     * @param bool $host_only Whether to return only the host or the full URL.
     * @return string The current URL.
     */
    public static function getUrl($host_only = false)
    {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '' && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
        $url = $protocol . '://' . $_SERVER['HTTP_HOST'];
        if ($host_only === false) $url .= $_SERVER['REQUEST_URI'];
        return $url;
    }

    /**
     * Validates if the request's referer matches the current host.
     *
     * @return bool True if the referer matches the host, otherwise false.
     */
    public static function checkReferer()
    {
        return (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false);
    }

    /**
     * Retrieves the path to a template file, considering fallbacks.
     *
     * @param string $path The relative path of the template.
     * @param bool $docbase Whether to include the document base in the path.
     * @return string The full path to the template file.
     */
    public static function getFromTemplate($path, $docbase = true)
    {
        $base = $docbase ? DOCBASE : SYSBASE;
        $default_path = 'templates/default/'.$path;
        if (PMS_TEMPLATE == 'default')
            return $base.$default_path;
        else {
            $template_path = 'templates/'.PMS_TEMPLATE . '/' . $path;
            if (is_file(SYSBASE . $template_path))
                return $base.$template_path;
            else {
                if (is_file(SYSBASE . $default_path))
                    return $base.$default_path;
                else
                    return 'File not found: '.$base.$template_path;
            }
        }
    }

    /**
     * Determines if the request is an AJAX (XHR) request.
     *
     * @return bool True if the request is an AJAX request, otherwise false.
     */
    public static function isXhr()
    {
        return ((isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] === 'cors') ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
    }
}
