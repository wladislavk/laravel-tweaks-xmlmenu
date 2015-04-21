# laravel-tweaks-xmlmenu
Parser of custom XML menus under Laravel

This is a pretty generalized class that should (by design) go to your app/Services folder. It does three things.
1) Reads a custom XML file.
2) Marks currently active menu item by changing XML on the fly using xPath query.
3) Returns the menu as either associative array or XML string.
It also supports infinite menu item nesting and XML validation using XSD schema.

It is useful in the cases when:
1) The menu is not user-generated but a part of system configuration.
2) There is some non-trivial searching through the menu such as determining the parent of currently active sub-menu item.

This script is usable as is, however, it is meant to be extended by a child class that would form xPath queries etc. The sample of such child class is provided in XMLAdminMenu.php.

The XML file for the menu is completely custom, no constraints are implied on which tags and attributes you use. Therefore, I chose not to provide any sample XML files.
