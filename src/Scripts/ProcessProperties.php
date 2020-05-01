<?php


namespace H2OpenXml\Scripts;


class ProcessProperties
{
    /**
     * @var ProcessProperties
     */
    private static $_instance;

    private function __construct()
    {
    }

    /**
     * @return ProcessProperties
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)){
            self::$_instance = new ProcessProperties();
        }
        return self::$_instance;
    }

    /**
     * @ Main Function to convert style : processPropertiesStyle
     *
     * This function check all of the html text for simple style
     * If it find bold, italic or other style it will add it to an other
     * function which will create a new run section with the new property
     *
     * @param string $input
     * @param int $nbre
     * @return string
     */
    public function processPropertiesStyle(string $input, int $nbre): string
    {
        $properties = array();
        $html = $input;
        $i0 = $nbre;
        for ($i=$i0; $i < strlen($html); $i++){
            if ($html[$i] == "<")
            {
                switch ($html[$i+1]) {
                    case "i":
                        list ($properties, $writeProperties) = $this->addProperty("italic", $properties); // change properties and write new text ppt
                        $html = substr_replace($html, $writeProperties, $i , 3); // remove <i> and write properties
                        break;
                    case "b":
                        list ($properties, $writeProperties) = $this->addProperty("bold", $properties);
                        $html = substr_replace($html, $writeProperties, $i , 3); // remove <b> and write properties
                        break;
                    case "u":
                        list ($properties, $writeProperties) = $this->addProperty("underlined", $properties);
                        if ($html[$i+2] == ">"){
                            $html = substr_replace($html, $writeProperties, $i , 3);
                            break;
                        } else break;
                    case "w":
                        break;
                    case "/":
                        list ($html, $properties) = $this->removeEndBracket($html, $properties, $i);
                        break;
                    default:
                        $html = $this->suppressStyle($i, $html);
                        $i = $i-1;
                        break;
                }
            }
        }

        return $html;
    }

    /**
     * Second part : remove & replace </ > tags
     *
     * @param string $html
     * @param array $properties
     * @param int $i
     * @return array
     */
    private function removeEndBracket(string $html, array $properties, int $i): array
    {
        switch ($html[$i+2]) {
            case "i":
                list ($properties, $writeProperties) = $this->removeProperty("italic", $properties);
                $html = substr_replace($html, $writeProperties, $i, 4); // remove </i> and write properties
                break;
            case "b":
                list ($properties, $writeProperties) = $this->removeProperty("bold", $properties);
                $html = substr_replace($html, $writeProperties, $i, 4); // remove </b> and write properties
                break;
            case "u":
                list ($properties, $writeProperties) = $this->removeProperty("underlined", $properties);
                $html = substr_replace($html, $writeProperties, $i, 4); // remove </u> and write properties
                break;
            case "w":
                break;
            default:
                $html = $this->suppressStyle($i, $html);
                break;
        }

        return array(
            $html,
            $properties
        );
    }

    /**
     * Add a new property to the properties'list
     *
     * @param string $property
     * @param array $properties
     * @return array
     */
    private function addProperty(string $property, array $properties): array
    {
        array_push($properties, $property);
        return array(
            $properties,
            $this->setProperty($properties),
        );
    }

    /**
     * Remove a property of the properties'list
     *
     * @param string $property
     * @param array $properties
     * @return array
     */
    private function removeProperty(string $property, array $properties): array
    {
        foreach ($properties as $key => $value) {
            if ($value == $property){
                unset($properties[$key]);
                $properties = array_values($properties);
            }
        }

        return array(
            $properties,
            $this->setProperty($properties),
        );
    }

    /**
     * Set the properties for the next run section of text
     *
     * @param array $properties
     * @return string
     */
    private function setProperty(array $properties):string
    {
        $propertiesList = "</w:t></w:r><w:r><w:rPr>";
        foreach ($properties as $value) {
            switch ($value) {
                case "italic":
                    $propertiesList = $propertiesList."<w:i/>";
                    break;
                case "bold":
                    $propertiesList = $propertiesList."<w:b/>";
                    break;
                case "underlined":
                    $propertiesList = $propertiesList."<w:u w:val='single'/>";
                    break;
                default:
                    break;
            }
        }
        $propertiesList = $propertiesList."</w:rPr><w:t>";

        return $propertiesList;
    }

    /**
     * Suppress style not yet cleaned from the text
     *
     * @param int $nb
     * @param string $html
     * @return string
     */
    private function suppressStyle(int $nb, string $html): string
    {
        $j = false;
        $i = 0;
        while($j == false){
            if($html[($nb+$i)]==">"){
                $j = true;
            }
            else{
                $i = $i+1;
            }
        }
        $html = substr_replace($html, "", $nb , $i+1);

        return $html;
    }
}