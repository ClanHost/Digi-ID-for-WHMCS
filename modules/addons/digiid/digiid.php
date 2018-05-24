<?php
ob_start();
if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}


function digiid_config() {
    $configarray = array(
        "name"          => "DigiByte Digi-ID",
        "version"       => "1.0.0",
        "description"   => "Secure login with one simple scan",
        "author"        => "<a href='http://whmcsnow.com/' target='_blank'>WHMCSnow.com</a>"
    );
    return $configarray;
}

function digiid_output($vars) {
    global $CONFIG;
    
    function digiDialog($type,$icon,$title,$body) { 
        // Type: info (blue), danger (red), warning (orange), success (green), grey
        // Icon: https://fontawesome.com/icons?d=gallery&m=free
        echo '<div class="alert alert-update-banner alert-update-banner-'.$type.'" style="position:relative; max-width:800px;">
            <div class="alert-icon"{if $icon eq "exclamation"} style="margin-left:10px;"{/if}>
                <i class="fa fa-'.$icon.'"></i>
            </div>
            <div class="alert-msg">
                <strong>'.$title.'</strong><br>
                '.$body.'
            </div>
        </div>';
    }
    
    $checkInstalled = mysql_num_rows(mysql_query("SELECT * FROM `tblcustomfields` WHERE `created_at` = '2018-04-11 00:00:00'"));
    if($checkInstalled==0) { // Install required SQL table
        mysql_query("INSERT INTO `tblcustomfields` (`type`,`fieldname`,`fieldtype`,`adminonly`,`created_at`) VALUES ('client','Digi-ID','text','on','2018-04-11 00:00:00')");
        header("Refresh:0");
    } else if(!file_exists(ROOTDIR."/templates/".$CONFIG['Template']."/clientareadigiid.tpl")) { 
        digiDialog("danger","exclamation","Something's not right",ROOTDIR."/templates/".$CONFIG['Template']."/clientareadigiid.tpl file is missing.<br />If you are using a custom theme please copy files.");
    } else if(!file_exists(ROOTDIR."/templates/".$CONFIG['Template']."/includes/logindigiid.tpl")) { 
        digiDialog("danger","exclamation","Something's not right",ROOTDIR."/templates/".$CONFIG['Template']."/includes/logindigiid.tpl file is missing.<br />If you are using a custom theme please copy files.");
    } else if(strpos(file_get_contents(ROOTDIR."/templates/".$CONFIG['Template']."/login.tpl"),"logindigiid") === false) {
        digiDialog("danger","exclamation","Something's not right",ROOTDIR."/templates/".$CONFIG['Template']."/login.tpl doesn't include Digi-ID login template.<br />Please see readme.");
    } else {
        digiDialog("success","check","You're good to go!","Everything is setup and ready!");
    }
    
echo '<div style="margin:0 0 20px 0;padding:10px 20px 20px;background-color:#f8f8f8;border-radius:3px;max-width:700px;">
    <h4>Donations</h4>
    <p>Digi-ID login for WHMCS is provide free of charge by <a href="https://whmcsnow.com">WHMCSnow.com</a>.<br />
    Please support us and the maintance of this script by donating any DigiByte to us below.</p>
    <div align="center" id="DigiDonate">
        <img />
    </div>
</div>
<script type="text/javascript" src="../modules/addons/digiid/js.js"></script>';
}


function digiid_activate() { 
    $checkInstalled = mysql_num_rows(mysql_query("SELECT * FROM `tblcustomfields` WHERE `created_at` = '2018-04-11 00:00:00'"));
    if($checkInstalled==0) {
        mysql_query("INSERT INTO `tblcustomfields` (`type`,`fieldname`,`fieldtype`,`adminonly`,`created_at`) VALUES ('client','Digi-ID','text','on','2018-04-11 00:00:00')");
    }
    if(!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'mod_digiid_nonce'"))) {
        mysql_query("CREATE TABLE `mod_digiid_nonce` (
                      `id` int(11) NOT NULL auto_increment,
                      `nonce` varchar(32) NOT NULL,
                      `session` varchar(64) NOT NULL,
                      `ip` varchar(20) NOT NULL,
                      `userid` int(11) NOT NULL,
                      `nonce_action` varchar(15) NOT NULL,
                      `expiry` int(32) NOT NULL,
                      PRIMARY KEY  (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
    }
}


?>