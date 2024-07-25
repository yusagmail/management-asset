<?php
namespace common\helpers;

use Yii;
use yii\base\Component;

class LogHelper extends Component{	
	public static function setPublicSubmitted($model){
		$model->created_date = Timeanddate::getCurrentDateTime();
		$model->created_id_user = Yii::$app->user->identity->id;
//		$model->request_time = Timeanddate::getCurrentDate();
		$model->created_ip_address = IPAddressFunction::getUserIPAddress();
		
		$model->save(false);
	}
	
	public static function setAssesmentLog($model){
		$model->assesment_id_user = Yii::$app->user->identity->id;
		$model->assesment_ip_address = IPAddressFunction::getUserIPAddress();
		$model->assesment_date = Timeanddate::getCurrentDate();
		$model->assesment_time = Timeanddate::getCurrentDateTime();
		
		$model->save(false);
	}

	public static function setPublicLog($model){
		$model->updated_user  = Yii::$app->user->identity->id;
		$model->updated_ip_address = IPAddressFunction::getUserIPAddress();
		$model->updated_date = Timeanddate::getCurrentDateTime();

		$model->save(false);
	}

}
?>
