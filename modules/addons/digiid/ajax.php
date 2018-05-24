<?php
require_once("../../../init.php");
require_once("core/digiid.php");
require_once("core/digiidcallback.php");
use DigiByte\digiid\core\DigiID;
use DigiByte\digiid\core\DigiIDCallback;

$session_id = session_id();
mysql_query("DELETE FROM `mod_digiid_nonce` WHERE `expiry` < '".time()."'");
$query = mysql_query("SELECT * FROM `mod_digiid_nonce` WHERE `nonce_action` = '".mysql_real_escape_string($_GET['act'])."' AND `session` = '".mysql_real_escape_string($session_id)."'");
$nonce_row = mysql_fetch_array($query);

switch ($_GET['act']) {
    case 'login':
        if(!is_array($nonce_row)) {
            $data['status'] = 3;
            $callback = new DigiIDCallback();
            $data['nonce'] = $callback->get_digiid_callback('login');
        } else if($nonce_row['userid']=="-1") {
            $data['status'] = 1; // Indicate error (error box)
            $data['html'] = "DigiID verification Sucess, but no user account linked.";
        } else if($nonce_row['userid']>="1") {
            $data['status'] = 2; // Indicate success (green box)
            $getUser = mysql_fetch_array(mysql_query("SELECT `firstname` FROM `tblclients` WHERE `id` = '" . $nonce_row['userid'] . "'"));
            $data['html'] = "Success, logged in as " . $getUser['firstname'] . "!";
            $data['reload'] = 1;
        } else {
            $data['status'] = 0; // Nothing changed
        }
    break;
        
    case 'register':
        $userid = $_SESSION['uid'];
        $tenseconds = date("Y-m-d H:i:s", strtotime('-10 seconds'));
        $CCF = mysql_fetch_array(mysql_query("SELECT `id` FROM `tblcustomfields` WHERE `created_at` = '2018-04-11 00:00:00'"));
        $user = mysql_fetch_array(mysql_query("SELECT * FROM `tblcustomfieldsvalues` WHERE `relid` = '" . $userid . "' AND `fieldid` = '" . $CCF['id'] . "' AND `updated_at` > '" . $tenseconds . "'"));
        if(!is_array($nonce_row)) {
            $data['status'] = 3;
            $callback = new DigiIDCallback();
            $callback->user_id = $userid;
            $data['nonce'] = $callback->get_digiid_callback('register');
        } else if(is_array($user)) {
            $data['status'] = 4;
            $data['wallet'] = $user['value'];
            mysql_query("UPDATE `tblcustomfieldsvalues` SET `updated_at` = '0000-00-00 00:00:00' WHERE `id` = '" . $user['id'] . "'");
        } else {
            $data['status'] = 0; // Nothing changed
        }
    break;
        
    case 'deregister':
        $userid = $_SESSION['uid'];
        $CCF = mysql_fetch_array(mysql_query("SELECT `id` FROM `tblcustomfields` WHERE `created_at` = '2018-04-11 00:00:00'"));
        mysql_query("DELETE FROM `tblcustomfieldsvalues` WHERE `relid` = '" . $userid . "' AND `fieldid` = '" . $CCF['id'] . "'");
        $data['status'] = 5;
    break;
}
    
echo json_encode($data) . PHP_EOL;
die();
     
?>