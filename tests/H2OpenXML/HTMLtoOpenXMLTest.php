<?php


use H2OpenXml\HTMLtoOpenXML;
use PHPUnit\Framework\TestCase;

class HTMLtoOpenXMLTest extends TestCase
{
    /**
     * @dataProvider textsProvider
     * @param string $html
     * @param string $converted
     */
    public function testBasicFunctionalities(string $html, string $converted)
    {
        $this->assertEquals(HTMLtoOpenXML::getInstance()->fromHTML($html), $converted);
    }

    /**
     * @return string[][]
     */
    public function textsProvider()
    {
        return [
            ["<p><b>This</b> is a test</p>", "<w:p><w:pPr><w:pStyle w:val='OurStyle2'/></w:pPr><w:r><w:t xml:space='preserve'></w:t></w:r><w:r><w:rPr><w:b/></w:rPr><w:t xml:space='preserve'>This</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space='preserve'> is a test</w:t></w:r></w:p><w:p><w:pPr><w:pStyle w:val='OurStyle2'/></w:pPr><w:r><w:t xml:space='preserve'></w:t></w:r></w:p>"]
        ];
    }
}
