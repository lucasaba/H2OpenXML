<?php

namespace H2OpenXml;

use H2OpenXml\Scripts\HTMLCleaner;
use H2OpenXml\Scripts\ProcessProperties;

class HTMLtoOpenXML
{
    /**
     * The only one instance of this class.
     * @var HTMLtoOpenXML
     */
    private static $_instance;

    /**
     * Private constructor of singleton.
     */
    private function __construct()
    {
    }

    /**
     * Return the singleton instance. Creates one if no one.
     */
    public static function getInstance(): HTMLtoOpenXML
    {
        if (is_null(self::$_instance)){
            self::$_instance = new HTMLtoOpenXML();
        }

        return self::$_instance;
    }

    /**
     * Converts HTML to RTF.
     *
     * @param string $htmlCode the HTML formated input string
     * @return string The converted string.
     */
    public function fromHTML(string $htmlCode): string
    {
        $start = 0;
        $openXml = HTMLCleaner::getInstance()->cleanUpHTML($htmlCode);
        $openXml = $this->getOpenXML($openXml);
        $openXml = $this->processBreaks($openXml);
        $openXml = $this->processListStyle($openXml);
        $openXml = ProcessProperties::getInstance()->processPropertiesStyle($openXml, $start);
        $openXml = $this->processSpaces($openXml);
        $openXml = $this->processStyle($openXml);

        return $openXml;
    }

    /**
     * @param string $text
     * @return string
     */
    private function getOpenXML(string $text): string
    {
        $text = "<w:p><w:r><w:t>$text</w:t></w:r></w:p>";

        return $text;
    }

    /**
     * @param string $input
     * @return string
     */
    private function processListStyle(string $input): string
    {
        $output = preg_replace("/(<ul>)/mi", '</w:t></w:r></w:p><w:p><w:r><w:t>', $input);
        $output = preg_replace("/(<\/ul>)/mi", '</w:t></w:r></w:p><w:p><w:r><w:t>', $output);
        $output = preg_replace("/(<ol>)/mi", '</w:t></w:r></w:p><w:p><w:r><w:t>', $output);
        $output = preg_replace("/(<\/ol>)/mi", '</w:t></w:r></w:p><w:p><w:r><w:t>', $output);
        $output = preg_replace("/(<li>)/mi", "</w:t></w:r><w:p startliste><w:r><w:t>", $output);
        $output = preg_replace("/(<\/li>)/mi", "", $output);

        return $output;
    }

    /**
     * @param string $input
     * @return string
     */
    private function processBreaks(string $input): string
    {
        $output = preg_replace("/(<\/p>)/mi", "</w:t></w:r></w:p><w:p><w:r><w:t>", $input);
        $output = preg_replace("/(<br>)/mi", "</w:t></w:r></w:p><w:p><w:r><w:t>", $output);

        return $output;
    }

    /**
     * @param string $input
     * @return string
     */
    private function processSpaces(string $input): string
    {
        $output = preg_replace("/(&nbsp;)/mi", " ", $input);
        $output = preg_replace("/(<w:t>)/mi", "<w:t xml:space='preserve'>", $output);

        return $output;
    }

    /**
     * @param string $input
     * @return string
     */
    private function processStyle(string $input): string
    {
        $output = preg_replace("/(<w:p>)/mi", "<w:p><w:pPr><w:pStyle w:val='OurStyle2'/></w:pPr>", $input);
        $output = preg_replace("/(<w:p startliste>)/mi", "</w:p><w:p><w:pPr><w:pStyle w:val='BulletStyle'/></w:pPr>", $output);

        return $output;
    }
}