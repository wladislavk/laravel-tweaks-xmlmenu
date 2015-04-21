<?php
/**
 * Created by PhpStorm.
 * User: vkr
 * Date: 18/4/2558
 * Time: 14:29
 */

namespace App\Services\XMLMenu;

class XMLAdminMenu extends XMLMenu{

    const CONFIG_FILE = 'config/xml/admin.xml';
    const SCHEMA_FILE = 'config/xml/admin.xsd';
    protected $admin_dir_path;

    /**
     * @param $admin_dir
     */
    public function __construct($admin_dir)
    {
        $xml_filename = base_path() . '/' . self::CONFIG_FILE;
        $xml_schemaname = base_path() . '/' . self::SCHEMA_FILE;
        parent::__construct($xml_filename, $xml_schemaname);
        $this->admin_dir_path = $admin_dir;
    }

    /**
     * @param bool $to_array
     * @return bool|mixed|string
     */
    public function setMenu($to_array=true)
    {
        $this->changeMenuXML();
        return parent::setMenu($to_array);
    }

    /**
     * @return bool
     */
    protected function changeMenuXML()
    {
        $exclusions = [
            $this->admin_dir_path . '/',
        ];
        $specific_request = $this->stripRequest($exclusions);
        if (! strlen($specific_request)) {
            return false;
        }
        // Search for any 1st level <item> that has Nth level descendant called <link> with text that matches $request
        $this->xpaths[] = '/menu/section/item[.//link[text()=\''.$specific_request.'\']]';
        // Search for any 2nd level <item> that has Nth level descendant called <link> with text that matches $request
        $this->xpaths[] = '//submenu/item[.//link[text()=\''.$specific_request.'\']]';
        return true;
    }

}