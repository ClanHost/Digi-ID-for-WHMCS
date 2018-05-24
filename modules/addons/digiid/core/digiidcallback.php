<?php
/*
Copyright 2014 Daniel Esteban

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

namespace DigiByte\digiid\core;


class DigiIDCallback {

    public $user_id; 
    
	private function digiid_get_nonce($nonce_action) {
    
        
        $session_id = session_id();
        mysql_query("DELETE FROM `mod_digiid_nonce` WHERE `expiry` < '".time()."'");
        
        $query = mysql_query("SELECT * FROM `mod_digiid_nonce` WHERE `nonce_action` = '".mysql_real_escape_string($nonce_action)."' AND `session` = '".mysql_real_escape_string($session_id)."'");
        $nonce_row = mysql_fetch_array($query);

		if($nonce_row) {
			return $nonce_row['nonce'];
		}

		$nonce_row = array();
		$nonce_row['nonce'] = DigiID::generateNonce();
		$nonce_row['nonce_action'] = $nonce_action;
		$nonce_row['session'] = $session_id;
        $nonce_row['ip'] = $_SERVER['REMOTE_ADDR'];
		$nonce_row['expiry'] =  time()+600;
        
        
        mysql_query("INSERT INTO `mod_digiid_nonce` (`nonce`,`session`,`ip`,`userid`,`nonce_action`,`expiry`) VALUES ('".$nonce_row['nonce']."','".$nonce_row['session']."','".$nonce_row['ip']."','".$this->user_id ."','".$nonce_row['nonce_action']."','".$nonce_row['expiry']."')");

        return $nonce_row['nonce'];
	}

	public function digiid_get_callback_url($nonce = NULL, $nonce_action = NULL) {
		
        if(!$nonce AND $nonce_action) {
			$nonce = $this->digiid_get_nonce($nonce_action);
		}

		if(!$nonce) { 
			return FALSE;
		}
        global $CONFIG;
		$url = $CONFIG['SystemURL']."/modules/addons/digiid/callback.php?x=" . $nonce;
		if(substr($url, 0, 8) == 'https://') {
			return 'digiid://' . substr($url, 8);
		} else {
			return 'digiid://' . substr($url, 7) . "&u=1";
		}
	}

	public function get_digiid_callback($method) {
		return $this->digiid_get_callback_url(NULL, $method);
	}
}
