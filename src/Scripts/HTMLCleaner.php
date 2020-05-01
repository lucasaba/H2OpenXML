<?php


namespace H2OpenXml\Scripts;


class HTMLCleaner
{
    /**
     * @var HTMLCleaner
     */
    private static $_instance;

    private function __construct()
    {
    }

    /**
     * @return HTMLCleaner
     */
    public static function getInstance() {
        if (is_null(self::$_instance)){
            self::$_instance = new HTMLCleaner();
        }
        return self::$_instance;
    }

    /**
     *	Clean up the HTML before process it.
     *
     *	@param string $htmlCode The HTML string
     *
     *	@return string The result string.
     */
    public function cleanUpHTML(string $htmlCode): string
    {
        $cleanHtmlCode = html_entity_decode($htmlCode);
        $cleanHtmlCode = $this->cleanFirstDivIfAny($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanUpFontTagsIfAny($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanUpSpanTagsIfAny($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanUpParagraphTagsIfAny($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanUpEmTagsIfAny($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanUpHeadTagsIfAny($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanUpEmptyTags($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanUpZeroWidthSpaceCodes($cleanHtmlCode);
        $cleanHtmlCode = $this->cleanBRTagsAtTheEndOfListItemsIfAny($cleanHtmlCode);

        return $cleanHtmlCode;
    }

    /**
     *	The WYSIWYG can pack all his code surrounded by div container. They need to be remove
     *	because a word wrap will be inserted.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanFirstDivIfAny(string $input): string
    {
        $output = $input;
        if(strpos($output, "<div") === 0) {
            $closeCharPos = strpos($output, ">");
            $output = substr_replace($output, "", 0, $closeCharPos);
            $output = substr_replace($output, "", strlen($output)-strlen("</div>"));
        }

        return $output;
    }

    /**
     *	The WYSIWYG can add a <br> tag at the end of list items (<li>). They need to be remove
     *	because a word wrap will be inserted and an empty item will be created in the doc file.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanBRTagsAtTheEndOfListItemsIfAny(string $input): string
    {
        return preg_replace("/<br><\/li>/mi", "</li>", $input);
    }

    /**
     *	The WYSIWYG can generate <font> tags. They need to clean up them.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanUpFontTagsIfAny(string $input): string
    {
        $output = preg_replace("/(<font[a-zA-Z0-9_.=,:;#'\"\- \(\)]*>)/mi", "", $input);
        $output = preg_replace("/(<\/font>)/mi", "", $output);
        return $output;
    }

    /**
     *	The WYSIWYG can generate <span> tags. They need to clean up them.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanUpSpanTagsIfAny(string $input): string
    {
        $output = preg_replace("/(<span[a-zA-Z0-9_.=,:;#'\"\- \(\)]*>)/mi", "", $input);
        $output = preg_replace("/(<\/span>)/mi", "", $output);

        return $output;
    }

    /**
     *	The WYSIWYG can generate <p> tags. They need to clean up them.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanUpParagraphTagsIfAny(string $input): string
    {
        $output = preg_replace("/(<p[a-zA-Z0-9_.=,:;#'\"\- \(\)]*>)/mi", "", $input);
        $output = preg_replace("/(<\/p>)/mi", "<br>", $output);

        return $output;
    }

    /**
     *	The WYSIWYG can generate <em> tags. They need to clean up them.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanUpEmTagsIfAny(string $input): string
    {
        $output = preg_replace("/(<em[a-zA-Z0-9_.=,:;#'\"\- \(\)]*>)/mi", "<i>", $input);
        $output = preg_replace("/(<\/em>)/mi", "</i>", $output);

        return $output;
    }

    /**
     *	The WYSIWYG can generate <h> tags. They need to clean up them.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanUpHeadTagsIfAny(string $input): string
    {
        $output = preg_replace("/(<h[a-zA-Z0-9_.=,:;#'\"\- \(\)]*>)/mi", "", $input);
        $output = preg_replace("/(<\/h[a-zA-Z0-9_.=,:;#'\"\- \(\)]>)/mi", "", $output);
        return $output;
    }

    /**
     *	The WYSIWYG can generate zero-width spaces(&#8203;). They need to clean up them.
     *
     *	@param string $input The HTML string
     *	@return string The result string.
     */
    private function cleanUpZeroWidthSpaceCodes(string $input): string
    {
        return preg_replace("/&#8203;/mi", "", $input);
    }

    /**
     *	Cleans up the HTML empty tag like <p></p> inserted by the WYSIWYG tool.
     *
     *	@param string $input The HTML string
     *	@return string The clean string.
     */
    private function cleanUpEmptyTags(string $input): string
    {
        $output = preg_replace("/(<p[a-zA-Z0-9_.=,:;#'\"\- \(\)]*><\/p>)/mi", "", $input);
        $output = preg_replace("/<div[a-zA-Z0-9_.=,:;#'\"\- \(\)]*><\/div>/mi", "", $output);
        $output = preg_replace("/<span[a-zA-Z0-9_.=,:;#'\"\- \(\)]*><\/span>/mi", "", $output);
        $output = preg_replace("/<u><\/u>/mi", "", $output);
        $output = preg_replace("/<i><\/i>/mi", "", $output);
        $output = preg_replace("/<b[a-zA-Z0-9_.=,:;#'\"\- \(\)]*><\/b>/mi", "", $output);

        return $output;
    }
}