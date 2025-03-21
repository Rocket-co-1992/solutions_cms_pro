<?php

namespace Pandao\Common\Utils;

/**
 * Class StrUtils
 * - cleanAccent
 * - mbSubstrReplace
 * - highlight
 * - textFormat
 * - formatString
 * - formatSearch
 * - strTrunc
 * - br2nl
 * - ripTags
 * - closeHtmlTags
 * - wrapSentence
 * - htmlAccents
 * - getAltText
 * - getInitials
 * - formatPrice
 */

class StrUtils
{
    /**
     * Closes any unclosed HTML tags in a string.
     *
     * @param string $text The HTML string.
     * @return string The string with closed HTML tags.
     */
    public static function closeHtmlTags($text)
    {
        preg_match_all('/<[^>]*>/', $text, $tags);
        $list = [];
        foreach ($tags[0] as $tag) {
            if ($tag[1] != '/') {
                preg_match('/<([a-z]+[0-9]*)/i', $tag, $type);
                if (isset($type[1])) $list[] = $type[1];
            } else {
                preg_match('/<\/([a-z]+[0-9]*)/i', $tag, $type);
                for ($i = count($list) - 1; $i >= 0; $i--) {
                    if ($list[$i] == $type[1]) $list[$i] = '';
                }
            }
        }
        $closed_tags = '';
        for ($i = count($list) - 1; $i >= 0; $i--) {
            if ($list[$i] != '' && $list[$i] != 'br') {
                $closed_tags .= '</' . $list[$i] . '>';
            }
        }
        return ($text . $closed_tags);
    }

    /**
     * Truncates a string to the specified length, preserving HTML tags.
     *
     * @param string $text The string to truncate.
     * @param int $length The maximum length of the truncated string.
     * @param bool $html Whether the string contains HTML.
     * @param string $ending The string to append after truncation.
     * @param bool $exact Whether to truncate at an exact length or by word.
     * @return string The truncated string.
     */
    public static function strTrunc($text, $length, $html = true, $ending = '...', $exact = false)
    {
        $text = preg_replace('/\s/', ' ', $text);
        $text = preg_replace('/\s\s+/', ' ', $text);

        if (mb_strlen(preg_replace('/<.*?>/is', '', $text)) <= $length) return $text;

        if ($html) {
            preg_match_all('/(<.+?>)?([^<>]*)/is', $text, $matches, PREG_SET_ORDER);

            $matches_length = 0;
            $content_text = '';
            $tags = [];

            foreach ($matches as $match) {
                if (!empty($match[0])) {
                    if (strlen($content_text) < $length) {
                        $content_text .= $match[2];
                        if (!empty($match[1])) $tags[strpos($match[0], $match[1]) + $matches_length] = $match[1];
                        $matches_length += strlen($match[0]);
                    } else {
                        break;
                    }
                }
            }
        } else {
            $content_text = self::ripTags($text);
        }

        $result = substr($content_text, 0, $length);

        if (!$exact) {
            $spacepos = strrpos($result, ' ');
            if ($spacepos !== false) {
                $result = substr($result, 0, $spacepos);
            }
        }
        if ($html) {
            foreach ($tags as $tag_pos => $tag) {
                $str_start = substr($result, 0, $tag_pos);
                $str_end = substr($result, $tag_pos, strlen($result) - $tag_pos);
                $result = $str_start . $tag . $str_end;
            }
            $result = self::closeHtmlTags($result);
            $result = preg_replace('/<([a-z]+[0-9]*)([^>]*)><\/([a-z]+[0-9]*)>/is', '', $result);
        }
        return $result . $ending;
    }

    /**
     * Removes accents from a string.
     *
     * @param string $str The input string.
     * @return string The string with accents removed.
     */
    public static function cleanAccent($str)
    {
        $unwanted_array = [
            'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 's', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ğ' => 'G', 'İ' => 'I', 'Ş' => 'S', 'ğ' => 'g', 'ı' => 'i', 'ş' => 's', 'ü' => 'u'
        ];
        return strtr($str, $unwanted_array);
    }

    /**
     * Multibyte-safe version of substr_replace for strings and arrays.
     *
     * @param string|array $string The input string or array.
     * @param string|array $replacement The replacement string or array.
     * @param int|array $start The starting position.
     * @param int|array|null $length The length to replace (optional).
     * @return string|array The modified string or array.
     */
    public static function mbSubstrReplace($string, $replacement, $start, $length = null)
    {
        if (is_array($string)) {
            $num = count($string);
            $replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad([$replacement], $num, $replacement);

            if (is_array($start)) {
                $start = array_slice($start, 0, $num);
                foreach ($start as $key => $value) {
                    $start[$key] = is_int($value) ? $value : 0;
                }
            } else {
                $start = array_pad([$start], $num, $start);
            }

            if (!isset($length)) $length = array_fill(0, $num, 0);
            elseif (is_array($length)) {
                $length = array_slice($length, 0, $num);
                foreach ($length as $key => $value) {
                    $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
                }
            } else {
                $length = array_pad([$length], $num, $length);
            }

            return array_map([__CLASS__, __FUNCTION__], $string, $replacement, $start, $length);
        }

        preg_match_all('/./us', (string)$string, $smatches);
        preg_match_all('/./us', (string)$replacement, $rmatches);
        if ($length === null) $length = mb_strlen($string);
        array_splice($smatches[0], $start, $length, $rmatches[0]);
        return join($smatches[0]);
    }

    /**
     * Highlights all occurrences of a needle in the haystack string.
     *
     * @param string $haystack The string to search within.
     * @param string $needle The string to search for.
     * @param string $startTag The opening tag for the highlight (optional).
     * @param string $endTag The closing tag for the highlight (optional).
     * @return string The modified string with highlights.
     */
    public static function highlight($haystack, $needle, $startTag = '<b>', $endTag = '</b>')
    {
        $needles = explode(' ', $needle);
        $haystack_format = self::formatString($haystack);
        $startTagLen = mb_strlen($startTag);
        $endTagLen = mb_strlen($endTag);

        foreach ($needles as $needle) {
            $needle = mb_strtoupper(self::cleanAccent($needle));
            $offset = 0;
            $len = mb_strlen($needle);
            while (($pos = mb_strpos($haystack_format, $needle, $offset)) !== false) {
                $offset = $pos + $startTagLen + $len;
                $haystack = self::mbSubstrReplace($haystack, $startTag, $pos, 0);
                $haystack = self::mbSubstrReplace($haystack, $endTag, $pos + $startTagLen + $len, 0);

                $haystack_format = self::mbSubstrReplace($haystack_format, $startTag, $pos, 0);
                $haystack_format = self::mbSubstrReplace($haystack_format, $endTag, $pos + $startTagLen + $len, 0);
                $offset += $endTagLen;
            }
        }
        return $haystack;
    }

    /**
     * Formats a string, replacing accents, applying lower/upper case, and removing non-alphanumeric characters.
     *
     * @param string $str The input string.
     * @param bool $tolower Whether to convert the string to lowercase (optional).
     * @param string $sep The separator to replace non-alphanumeric characters (optional).
     * @return string The formatted string.
     */
    public static function textFormat($str, $tolower = true, $sep = '-')
    {
        $str = self::cleanAccent($str);
        $str = preg_replace('/([^a-z0-9]+)/i', $sep, $str);
        $str = preg_replace('/' . $sep . '[' . $sep . ']+/', $sep, $str);
        $str = trim($str, $sep);
        if ($tolower) {
            $str = strtolower($str);
        }
        $str = mb_convert_encoding($str, 'UTF-8');
        return $str;
    }

    /**
     * Formats a string by optionally removing accents and converting it to uppercase.
     *
     * @param string $str The input string.
     * @param bool $accents Whether to remove accents (optional).
     * @param bool $alpha Whether to remove non-alphanumeric characters (optional).
     * @return string The formatted string.
     */
    public static function formatString($str, $accents = true, $alpha = false)
    {
        if ($accents) {
            $str = self::cleanAccent($str);
        }
        if ($alpha) {
            $str = preg_replace('/([^a-z0-9]+)/i', ' ', $str);
        }
        $str = mb_strtoupper($str, 'UTF-8');
        $str = preg_replace('/\s\s+/', ' ', $str);
        return $str;
    }

    /**
     * Prepares and formats a search query, ensuring it meets a minimum length.
     *
     * @param string $needle The search query.
     * @param int $len_min The minimum length of each word in the query (optional).
     * @return array An array containing the formatted query and its individual words.
     */
    public static function formatSearch($needle, $len_min = 3)
    {
        $needle = mb_strtoupper(self::cleanAccent($needle), 'UTF-8');
        $needle = preg_replace('/([^[:alnum:]_\-\']+)/ui', ' ', $needle);

        $needles = preg_split('/\s+/', $needle);

        $needle .= ' ' . preg_replace('/[_\-\']/', ' ', $needle);
        $needle = trim(preg_replace('/\s\s+/', ' ', $needle));

        $needles += preg_split('/\s+/', $needle);

        foreach ($needles as $i => $ndl) {
            if (mb_strlen($ndl) < $len_min) {
                $needles[$i] = '';
            }
        }

        $needles = array_values(array_filter(array_unique($needles)));

        $needle = implode(' ', $needles);

        return [$needle, $needles];
    }

    /**
     * Replaces all <br> tags in a string with newlines.
     *
     * @param string $str The input string.
     * @return string The string with <br> tags replaced by newlines.
     */
    public static function br2nl($str)
    {
        return preg_replace('/\<br\s*\/?\>/i', "\n", $str);
    }

    /**
     * Strips HTML tags from a string and decodes HTML entities.
     *
     * @param string $str The input string.
     * @return string The cleaned string.
     */
    public static function ripTags($str)
    {
        $str = preg_replace('/<[^>]*>/', '', self::br2nl(html_entity_decode($str, ENT_COMPAT, 'UTF-8')));
        $str = preg_replace('/\s/', ' ', $str);
        $str = preg_replace('/\s\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Wraps sentences containing the needle in the haystack string, with context.
     *
     * @param string $haystack The text to search in.
     * @param string $needle The search query.
     * @param int $numWords The number of words around the match to include (optional).
     * @param int $numOccur The maximum number of occurrences to wrap (optional).
     * @return string|bool The wrapped text or false if no match.
     */
    public static function wrapSentence($haystack, $needle, $numWords = 5, $numOccur = 3)
    {
        $search = self::formatSearch($needle);
        $needle = $search[0];
        $needles = $search[1];

        $haystack = self::ripTags($haystack);
        $words = preg_split('/\s+/', $haystack);
        $words_format = preg_split('/\s+/', self::formatString($haystack));

        $found_words = array();
        foreach ($needles as $i => $ndl) {
            $found_words += preg_grep('/^.*\'' . $ndl . '.*|^' . $ndl . '.*/', $words_format);
        }

        $found_pos = array_keys($found_words);
        $found_count = count($found_pos);
        $count_words = count($words);

        if ($found_count > 0) {
            if ($found_count < $numOccur) $numOccur = $found_count;
            $out = '';
            $start_next = null;
            $pre_start_next = null;
            for ($i = 0; $i < $numOccur; $i++) {
                $pos = $found_pos[$i];
                $length = null;
                $post_end = null;
                $pre_start = null;

                if (is_null($start_next)) $start = ($pos - $numWords > 0) ? $pos - $numWords : 0;
                else $start = $start_next;

                if (is_null($pre_start_next)) $pre_start = ($start > 0) ? ' ... ' : '';
                else $pre_start = $pre_start_next;

                $start_next = null;
                $pre_start_next = null;

                if (($i + 1) < $numOccur) {
                    $pos_next = $found_pos[$i + 1];
                    if (($pos_next - $pos - 1) <= $numWords) {
                        $length = $pos - $start + 1;
                        $start_next = $pos + 1;
                        $post_end = '';
                        $pre_start_next = ' ';
                    } elseif (($pos_next - $pos - 1) < ($numWords * 2)) {
                        $length = ($pos_next - $numWords > 0) ? $pos_next - $numWords - $pos : 0;
                        $post_end = '';
                        $pre_start_next = ' ';
                    } elseif (($pos_next - $pos - 1) == ($numWords * 2)) {
                        $post_end = '';
                        $pre_start_next = ' ';
                    } else {
                        $post_end = ' ... ';
                        $pre_start_next = '';
                    }
                }

                if (is_null($length)) $length = (($pos + ($numWords + 1) < $count_words) ? $pos + ($numWords + 1) : $count_words) - $start;
                $slice = array_slice($words, $start, $length);

                if (is_null($post_end)) $post_end = ($pos + ($numWords + 1) < $count_words) ? ' ... ' : '';

                $out .= $pre_start . implode(' ', $slice) . $post_end;
            }
            return self::highlight($out, $needle);
        } else {
            return false;
        }
    }

    /**
     * Converts special HTML characters to their accented equivalents.
     *
     * @param string $text The input text.
     * @param int $flags The HTML entity decode flags (optional).
     * @param string $charset The character set to use (optional).
     * @return string The decoded text.
     */
    public static function htmlAccents($text, $flags = ENT_NOQUOTES, $charset = 'UTF-8')
    {
        $text = htmlentities($text, $flags, $charset);
        $text = htmlspecialchars_decode($text);
        return $text;
    }

    /**
     * Formats a price according to the specified currency.
     *
     * @param float $price The price value.
     * @param string $currency The currency sign (optional).
     * @return string The formatted price.
     */
    public static function formatPrice($price, $currency = PMS_CURRENCY_SIGN)
    {
        if (function_exists('numfmt_format_currency')) {
            $fmt = new \NumberFormatter(PMS_LOCALE, \NumberFormatter::CURRENCY);
            return $fmt->formatCurrency($price, PMS_CURRENCY_CODE);
        } else {
            $formattedPrice = str_replace('.00', '', number_format($price, 2, '.', ','));
            if (defined('PMS_CURRENCY_POS') && PMS_CURRENCY_POS === 'after') {
                return $formattedPrice . $currency;
            } else {
                return $currency . $formattedPrice;
            }
        }
    }

    /**
     * Retrieves the appropriate singular or plural string based on the value.
     *
     * @param string $singular The singular form of the string.
     * @param string $plural The plural form of the string.
     * @param int $value The value to check.
     * @param string $mode The case mode ('lc' for lowercase, 'uc' for uppercase).
     * @return string The appropriate singular or plural string.
     */
    public static function getAltText($singular, $plural, $value, $mode = 'lc')
    {
        $mode = ($mode == 'lc') ? MB_CASE_LOWER : MB_CASE_TITLE;
        $string = ($value > 1) ? $plural : $singular;
        return mb_convert_case($string, $mode, 'UTF-8');
    }

    /**
     * Retrieves the initials from a string, up to a certain limit.
     *
     * @param string $input The input string.
     * @param int $limit The maximum number of initials to return.
     * @param string $mode The case mode ('uc' for uppercase, 'lc' for lowercase).
     * @return string The initials of the input string.
     */
    public static function getInitials($input, $limit = 3, $mode = 'uc')
    {
        $wds = explode(' ', $input);
        $nb_wds = count($wds);
        $initials = '';
        foreach ($wds as $i => $wd) {
            if ($i + 1 > $limit) break;
            $initials .= substr($wd, 0, 1);
        }
        $mode = ($mode == 'uc') ? MB_CASE_UPPER : MB_CASE_LOWER;
        return mb_convert_case($initials, $mode, 'UTF-8');
    }

    /**
     * Encode HTML entities if HTML tags are detected, handle non-string inputs
     *
     * @param mixed $input The input to be checked (string, array, null, etc.)
     * @return mixed The processed string or the input value as is if not a string
     */
    public static function encodeIfHtml($input)
    {
        if (is_string($input)) {
            if ($input !== strip_tags($input)) {
                return htmlentities($input, ENT_QUOTES, 'UTF-8');
            }
            return $input;
        }
        return $input;
    }
}
