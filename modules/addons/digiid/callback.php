<?php
require_once("../../../init.php");
require_once("./core/digiid.php");
require_once("./core/digiidcallback.php");
use DigiByte\digiid\core\DigiID;
use DigiByte\digiid\core\DigiIDCallback;

$raw_post_data = file_get_contents('php://input');


$variables = array('address', 'signature', 'uri');
$callback = new DigiIDCallback();


$post_data = array();
	
$json = NULL;
$uri = NULL;
$nonce = NULL;

$GLOBALS['digiid_vars']['json'] = &$json;
$GLOBALS['digiid_vars']['uri'] = &$uri;
$GLOBALS['digiid_vars']['nonce'] = &$nonce;

if(substr($raw_post_data, 0, 1) == "{") {
    $json = json_decode($raw_post_data, TRUE);
    foreach($variables as $key) {
        if(isset($json[$key])) {
            $post_data[$key] = (string) $json[$key];
        } else {
            $post_data[$key] = NULL;
        }
    }
} else {
    /*
    $json = FALSE;
    foreach($variables as $key) {
        if($this->request->variable($key, '') !== '') {
            $post_data[$key] = (string) html_entity_decode($this->request->variable($key, ''));
        } else {
            $post_data[$key] = NULL;
        }
    }
    */
}

if(!array_filter($post_data)) {
    DigiID::http_error(20, 'No data recived');
    die();
}

$nonce = DigiID::extractNonce($post_data['uri']);


if(!$nonce OR strlen($nonce) != 32) {
    DigiID::http_error(40, 'Bad nonce');
    die();
}

$uri = $callback->digiid_get_callback_url($nonce);

if($uri != $post_data['uri']) {
    DigiID::http_error(10, 'Bad URI', NULL, NULL, array('expected' => $uri, 'sent_uri' => $post_data['uri']));
    die();
}

$nonce_row = mysql_fetch_array(mysql_query("SELECT * FROM `mod_digiid_nonce` WHERE `nonce` = '".mysql_real_escape_string($nonce)."'"));

if(!$nonce_row){
    DigiID::http_error(41, 'Bad or expired nonce');
    die();
}

if($nonce_row AND $nonce_row['address'] AND $nonce_row['address'] != $post_data['address']) {
    DigiID::http_error(41, 'Bad or expired nonce');
    die();
}


$digiid = new DigiID();
$signValid = $digiid->isMessageSignatureValidSafe($post_data['address'], $post_data['signature'], $post_data['uri'], FALSE);
if(!$signValid) {
    DigiID::http_error(30, 'Bad signature', $post_data['address'], $post_data['signature'], $post_data['uri']);
    die();
}

if(!$nonce_row['address']) {
    $nonce_row['address'] = $post_data['address'];
}


$CCF = mysql_fetch_array(mysql_query("SELECT `id` FROM `tblcustomfields` WHERE `created_at` = '2018-04-11 00:00:00'"));
if($nonce_row['nonce_action']=="login") {
    $user = mysql_fetch_array(mysql_query("SELECT `relid` FROM `tblcustomfieldsvalues` WHERE `value` = '" . mysql_real_escape_string($nonce_row['address']) . "' AND `fieldid` = '" . $CCF['id'] . "'"));
    if(is_array($user)) {  
        mysql_query("UPDATE `mod_digiid_nonce` SET `userid` = '" . $user['relid'] . "' WHERE `id` = '" . $nonce_row['id'] . "'");
    } else {
        mysql_query("UPDATE `mod_digiid_nonce` SET `userid` = '-1' WHERE `id` = '" . $nonce_row['id'] . "'");
    }
} else if($nonce_row['nonce_action']=="register") {
    $user = mysql_fetch_array(mysql_query("SELECT `relid` FROM `tblcustomfieldsvalues` WHERE `relid` = '" . $nonce_row['userid'] . "' AND `fieldid` = '" . $CCF['id'] . "'"));
    if(is_array($user)) {  
        mysql_query("UPDATE `tblcustomfieldsvalues` SET `value` = '" . $nonce_row['address'] . "', `updated_at` = NOW() WHERE `relid` = '" . $nonce_row['userid'] . "'");
    } else {
        mysql_query("INSERT INTO `tblcustomfieldsvalues` (`fieldid`,`relid`,`value`,`updated_at`) VALUES ('" . $CCF['id'] . "', '" . $nonce_row['userid'] . "', '" . $post_data['address'] . "',NOW())");
    } 
}

DigiID::http_ok($post_data['address'], $nonce);
die();

?>