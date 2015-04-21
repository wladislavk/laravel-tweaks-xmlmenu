<?php

namespace App\Services\XMLMenu;

use Illuminate\Support\Facades\Request;

class XMLMenu {

    protected $menu;
    protected $request;
    protected $xpaths;

    /**
     * @param $xml_filename
     * @param string $xml_schemaname
     * @throws \Exception
     */
    public function __construct($xml_filename, $xml_schemaname='')
    {
        $this->request = $_SERVER['SCRIPT_URI'];
        $this->xpaths = [];
        $this->menu = $this->getMenu($xml_filename, $xml_schemaname);
    }

    /**
     * @param bool $to_array
     * @return bool|mixed|string
     */
    public function setMenu($to_array=true)
    {
        $this->setCurrentItem();
        if ($to_array) {
            return $this->xmlToArray();
        }
        return $this->menu;
    }

    /**
     * @param $xml_string
     * @return mixed
     */
    protected function xmlToArray()
    {
        return  json_decode(json_encode((array)simplexml_load_string($this->menu)), 1);
    }

    /**
     * @param $xml_filename
     * @param $xml_schemaname
     * @return string
     * @throws \Exception
     */
    protected function getMenu($xml_filename, $xml_schemaname)
    {
        if (strlen($xml_schemaname)) {
            if (false === $this->isValidXML($xml_filename, $xml_schemaname)) {
                throw new \Exception('XML file did not validate with provided schema');
            }
        }
        $xml_menu = file_get_contents($xml_filename);
        return $xml_menu;
    }

    /**
     * @param $file
     * @param $schema
     * @return bool
     */
    protected function isValidXML($file, $schema)
    {
        $xml = new \DOMDocument();
        $xml->load($file);
        if (true === $xml->schemaValidate($schema)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $active
     * @param string $actval
     * @return bool
     */
    protected function setCurrentItem($active='active', $actval='1')
    {
        if (!sizeof($this->xpaths)) {
            return false;
        }
        $xml = new \SimpleXMLElement($this->menu);
        foreach ($this->xpaths as $xpath) {
            $xml = $this->addAttrFromXPath($xml, $xpath, $active, $actval);
        }
        $this->menu = $xml->asXML();
        return true;
    }

    /**
     * @param \SimpleXMLElement $xml_obj
     * @param $xpath
     * @param $attr_name
     * @param $attr_value
     * @return \SimpleXMLElement
     */
    protected function addAttrFromXPath(\SimpleXMLElement $xml_obj, $xpath, $attr_name, $attr_value)
    {
        $result = $xml_obj->xpath($xpath);
        if (sizeof($result) && is_object($result[0])) {
            $result[0]->addAttribute($attr_name, $attr_value);
        }
        return $xml_obj;
    }

    /**
     * @param array $exclusions
     * @return mixed
     */
    protected function stripRequest(Array $exclusions=[])
    {
        $stripped_request = $this->request;
        if (sizeof($exclusions)) {
            foreach ($exclusions as $exclusion) {
                $stripped_request = str_replace($exclusion, '', $stripped_request);
            }
        } else {
            $stripped_request = str_replace(url('/'), '', $stripped_request);
        }
        // strip trailing slashes
        $stripped_request = preg_replace('/^(.+)\\/$/', '$1', $stripped_request);
        return $stripped_request;
    }
}