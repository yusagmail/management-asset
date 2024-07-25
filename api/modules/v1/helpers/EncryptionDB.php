<?php
namespace api\modules\v1\helpers;

use common\helpers\Timeanddate;

class EncryptionDB {
	
    public static function encryptor($action, $string) {
		$output = false;

		$encrypt_method = "AES-256-CBC";
		//pls set your unique hashing key

        $currentTime = Timeanddate::getCurrentDate();
        $secret_key = 'www.sineka.co.id';
		$secret_iv = 'sinekasatuindo'.$currentTime;


		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		//do the encyption given text/string/number
		if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		else if( $action == 'decrypt' ){
			//decrypt the given text/string/number
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}
	
    public static function staticEncryptor($action, $string) {
		$output = false;

		$encrypt_method = "AES-256-CBC";
		//pls set your unique hashing key

        //$currentTime = Timeanddate::getCurrentDate();
        $secret_key = 'www.sineka.co.id';
		$secret_iv = 'pt.sinekasatuindo';


		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		//do the encyption given text/string/number
		if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		else if( $action == 'decrypt' ){
			//decrypt the given text/string/number
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}
	
}
?>