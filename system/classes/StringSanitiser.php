<?php

class StringSanitiser {
    /**
     * Converts input data to encoded entities for safe display to screen
     * 
     * @param ?string $data
     * @return string
     */
    public static function sanitise(?string $string, ?int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401): string
    {
        if (!$string) {
            return '';
        }
        return htmlspecialchars(string: $string, flags: $flags, double_encode: false);
    }

    /**
     * Strips default Quill HTML tags from the input string
     * 
     * @param ?string $string
     * @param ?array $tags
     * @return string
     */
    public static function stripTags(?string $string, ?array $tags = []): string
    {
        if (!$string) {
            return '';
        }

        if (empty($tags)) {
            $tags = (new \Html\Cmfive\QuillEditor())->tag_allow_list;
        }

        return strip_tags($string, $tags);
    }

    /**
     * Strips anything that is not alphanumeric from the input string
     * 
     * @param ?string $string
     * @return string
     */
    public static function stripNonAlphaNumeric(?string $string): string
    {
        if (!$string) {
            return '';
        }
        return preg_replace('/[^a-zA-Z0-9]/', '', $string) ?? '';
    }

    /**
     * Escapes quotes of the input string
     * 
     * @param ?string $string
     * @return string
     */
    public static function escapeQuotes(?string $string): string
    {
        if (!$string) {
            return '';
        }
        return addslashes($string);
    }
}