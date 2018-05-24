<?php
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

require_once(ROOTDIR."/modules/addons/digiid/core/digiid.php");
require_once(ROOTDIR."/modules/addons/digiid/core/digiidcallback.php");
use DigiByte\digiid\core\DigiID;
use DigiByte\digiid\core\DigiIDCallback;
use WHMCS\View\Menu\Item as MenuItem;

add_hook('ClientAreaPageLogin', 1, function($vars) {
    $callback = new DigiIDCallback();
    $digiid = $callback->get_digiid_callback('login');
    
    $extraVariables = [
        'DigiIDURL' => $digiid,
    ];
    return $extraVariables;
});


add_hook('ClientLoginShare', 1, function($vars) {
    $session_id = session_id();
    mysql_query("DELETE FROM `mod_digiid_nonce` WHERE `expiry` < '".time()."'");
    $query = mysql_query("SELECT * FROM `mod_digiid_nonce` WHERE `nonce_action` = 'login' AND `nonce` = '".mysql_real_escape_string($vars['password'])."'");
    $nonce_row = mysql_fetch_array($query);
   
    if($nonce_row['userid']>="1") {
        return array(
            'id' => $nonce_row['userid'],
        );
    }
    return false;
});

add_hook('ClientAreaHeadOutput', 1, function($vars) {
    $template = $vars['template'];
    return '<link href="/templates/'.$vars['template'].'/css/digiid.css" rel="stylesheet" type="text/css" />';

});

add_hook('ClientAreaPrimarySidebar', 1, function (MenuItem $primarySidebar) {
	if (!is_null($primarySidebar->getChild('My Account'))) {
		$primarySidebar->getChild('My Account')
            ->addChild('Digi-ID')
            ->setLabel('My Digi-ID')
            ->setUri('digiid.php')
            ->setOrder(100);
	}
});

add_hook('ClientAreaSecondaryNavbar', 1, function (MenuItem $secondaryNavbar) {
   if (!is_null($secondaryNavbar->getChild('Account'))) {
           $secondaryNavbar->getChild('Account')
                           ->addChild('Emergency Contacts', array(
                                'label' => 'My Digi-ID',
                                'uri' => 'digiid.php',
                                'order' => '40',
                            ));
   }
});

?>