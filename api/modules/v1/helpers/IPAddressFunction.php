<?php
namespace api\modules\v1\helpers;

use Yii;
use yii\base\Component;

class IPAddressFunction extends Component {
	public static function getUserIPAddress(){
		$ip = "NOT IDENTIFIED";
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
		
		//return "NOT IDENTIFIED";
	}	
}
?>
