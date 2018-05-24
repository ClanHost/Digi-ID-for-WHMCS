<?php
define("CLIENTAREA",true);
require __DIR__ . '/init.php';

require_once(ROOTDIR."/modules/addons/digiid/core/digiid.php");
require_once(ROOTDIR."/modules/addons/digiid/core/digiidcallback.php");
use DigiByte\digiid\core\DigiID;
use DigiByte\digiid\core\DigiIDCallback;
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;


$ca = new ClientArea();

$ca->setPageTitle('My Digi-ID');

$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('clientarea.php', Lang::trans('clientareatitle'));
$ca->addToBreadCrumb('digiid.php', 'My Digi-ID');

$ca->initPage();

$ca->requireLogin();


Menu::addContext();
Menu::primarySidebar('clientView');


$gCCF = mysql_fetch_array(mysql_query("SELECT `id` FROM `tblcustomfields` WHERE `created_at` = '2018-04-11 00:00:00'"));
$checkAddress = mysql_fetch_array(mysql_query("SELECT `value` FROM `tblcustomfieldsvalues` WHERE `fieldid` = '" . $gCCF['id'] . "' AND `relid` = '" . $ca->getUserID() . "'"));

$ca->assign('digiidAddress', $checkAddress['value']);

$callback = new DigiIDCallback();
$callback->user_id = $ca->getUserID();
$ca->assign('DigiIDURL', $callback->get_digiid_callback('register'));
    
    
$ca->setTemplate('clientareadigiid');

$ca->output();

?>
