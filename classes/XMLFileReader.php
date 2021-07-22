<?php

/**
 * Class for reading xml file
 * This can be optimized for huge files to read file by parts, for this need more time
 * Class XMLFileReader
 */
class XMLFileReader
{
    protected SimpleXMLElement $file;

    public function __construct(string $fileName)
    {
        $this->file = simplexml_load_file($fileName);
    }

    public function toArray()
    {
        $return = [];
        $i = 0;
        foreach ($this->file->children() as $silence) {
            foreach ($silence->attributes() as $name => $value) {
                $return[$i][$name] = (string) $value;
            }
            $i++;
        }
        return $return;
    }
}