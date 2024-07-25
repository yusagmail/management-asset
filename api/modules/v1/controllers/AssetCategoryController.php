<?php
namespace api\modules\v1\controllers;

//use yii\rest\ActiveController;
use api\modules\v1\models\TypeAsset1;
use api\modules\v1\models\SensorLog;
use api\modules\v1\models\HomeInfo;
use api\modules\v1\helpers\ImageManipulation;
use api\modules\v1\models\ResponMessage;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use api\modules\v1\helpers\Timeanddate;
use api\modules\v1\helpers\IPAddressFunction;
//use api\modules\v1\helpers\ImageManipulation;

use app\common\helpers\CoordinateCalculation;
use Yii;


class AssetCategoryController extends Controller
{
    public $enableCsrfValidation=false;
//    public $modelClass = 'app\models\AssetItem';

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    public function actionHello()
    {
        return 'hello';

    }

    public function actionAll() {
        $header = header('Access-Control-Allow-Origin: *');
        //$header = header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        //$header = header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

        $records = TypeAsset1::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id"] = $record->id_type_asset;
            $datas["category"] = $record->type_asset;

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        //return json_encode($myobj);
        // Mengatur format respons menjadi JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $myobj;
    }

}

class ResponMessageMobileMaster 
{
    public $status = "ok";
    public $message = "";
    public $tujuan_derek;
    public $kendaraan_derek;
    public $jenis_kendaraan;

    public function ResponMessageMobileMaster(){

    }
}