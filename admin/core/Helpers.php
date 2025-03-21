<?php

namespace Pandao\Admin\Core;

class Helpers
{
    /**
     * Replace placeholders in the string with values from the dictionary.
     *
     * @param string $entry The string with placeholders.
     * @param array $dictionary The array of replacements.
     * 
     * @return string The translated string.
     */
    public static function getTranslation($entry, $dictionary)
    {
        $value = $entry;
        if (strpos($entry, '[') !== false) {
            if (preg_match_all('/\[(\w+)\]/', $entry, $matches) > 0) {
                $matches = $matches[1];
                foreach ($matches as $entry) {
                    if (isset($dictionary[$entry])) $value = str_replace('['.$entry.']', $dictionary[$entry], $value);
                }
            }
        }
        return $value;
    }

    /**
     * Send a 404 error response and redirect to a custom error page.
     *
     * @param string $message Optional error message.
     * @param string $url Optional custom URL for the 404 page.
     */
    public static function err404($message = '', $url = '404.html')
    {
        http_response_code(404);
        header('HTTP/1.0 404 Not Found');
        error_log('Admin err 404: ' . $message);
        header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/' . $url);
        exit;
    }

    /**
     * Recursively delete a directory and its contents.
     *
     * @param string $dirname Directory path.
     * @param bool $contentOnly If true, only delete the content, not the directory.
     * @param bool $followLinks If false, skip symbolic links.
     * 
     * @return bool True on success.
     * @throws \Exception If directory is not writable.
     */
    public static function recursiveRmdir($dirname, $contentOnly = false, $followLinks = false)
    {
        if (is_dir($dirname) && !is_link($dirname)) {
            if (!is_writable($dirname)) {
                throw new \Exception('You do not have renaming permissions!');
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dirname),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            while ($iterator->valid()) {
                if (!$iterator->isDot()) {
                    if ($iterator->isLink() && false === (boolean) $followLinks) $iterator->next();
                    if ($iterator->isFile()) unlink($iterator->getPathName());
                    elseif ($iterator->isDir()) Helpers::recursiveRmdir($iterator->getPathName());
                }
                $iterator->next();
            }
            unset($iterator);

            return $contentOnly ? true : rmdir($dirname);
        }
    }
}
