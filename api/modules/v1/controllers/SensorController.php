<?php
namespace api\modules\v1\controllers;

//use yii\rest\ActiveController;
use api\modules\v1\models\Sensor;
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


class SensorController extends Controller
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


    public function actionIndex() {
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;
        return array('info' => 'Hello World Pelanggaran');
    }

    public function actionHello()
    {
        return 'hello';

    }

    public function actionCheck()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $assetItemList = Pelanggaran::find()->all();
        if (count($assetItemList) > 0){
            return array('status'=> true,'data'=> $assetItemList);
        }else{
            return array('status'=> false, 'data'=> 'No AssetItem Found.');
        }
    }

    public function actionCheckAccessRequest($headers) {
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;
        $this->actionCheckAccessMain($headers);

        /*
        $user_mobile_token = $headers['user_mobile_token'];
        $session = MobileSession::find()->select([
                    "auth_key"
                ])
                ->where("auth_key = '" . $user_mobile_token . "' AND status = 1 AND valid_date_time >= NOW()")
                ->asArray()
                ->one();

        if (!$session) {
            throw new ForbiddenHttpException('User Mobile Token Not Valid, maybe is expired, please login again');
        }
        */
    }

    public function actionCheckAccessMain($headers) {
        if ($headers['app_mobile_token'] != $this->getApplicationMobileToken()) {
            throw new ForbiddenHttpException('Application Mobile Token Not Valid');
        }
    }

    private function getApplicationMobileToken() {
        return '4553TMAN4GEM3NT';
    }

    public function actionGetMobileMasterPelanggaran(){
        $records = JenisKendaraan::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id_jenis_kendaraan"] = $record->id_jenis_kendaraan;
            $datas["jenis_kendaraan"] = $record->jenis_kendaraan;

            $rows[] = $datas;
        }

        $myobj = new ResponMessageMobileMaster();
        $myobj->jenis_kendaraan = $rows;


        //Kendaraan Derek
        $records = KendaraanDerek::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id_kendaraan_derek"] = $record->id_kendaraan_derek;
            $datas["no_polisi"] = $record->no_polisi;
            $datas["pemilik"] = $record->pemilik;

            $rows[] = $datas;
        }
        $myobj->kendaraan_derek = $rows;

        //Tujuan Derek
        $records = LokasiInapKendaraan::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id_lokasi_inap_kendaraan"] = $record->id_lokasi_inap_kendaraan;
            $datas["nama_lokasi"] = $record->nama_lokasi;
            $datas["alamat"] = $record->alamat;
            $datas["longitude"] = $record->longitude;
            $datas["latitude"] = $record->latitude;

            $rows[] = $datas;
        }
        $myobj->tujuan_derek = $rows;

        //Jenis Pelanggaran
        $records = JenisPelanggaran::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id_jenis_pelanggaran"] = $record->id_jenis_pelanggaran;
            $datas["jenis_pelanggaran"] = $record->jenis_pelanggaran;
            $rows[] = $datas;
        }
        $myobj->jenis_pelanggaran = $rows;

        return json_encode($myobj, JSON_UNESCAPED_SLASHES);
    }

    public function actionGetJenisKendaraan(){
        $records = JenisKendaraan::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id_jenis_kendaraan"] = $record->id_jenis_kendaraan;
            $datas["jenis_kendaraan"] = $record->jenis_kendaraan;

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        return json_encode($myobj);
    }

    public function actionGetListSensor(){
        $records = Sensor::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id_sensor"] = $record->id_sensor;
            $datas["sensor_name"] = $record->sensor_name;
            $datas["sensor_analog1"] = $record->sensor_analog1;

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        return json_encode($myobj);
    }

    public function actionSetDataTrack(){
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;

        $this->actionCheckAccessRequest(yii::$app->request->headers);
        $params = Yii::$app->request->queryParams;

        $lat = isset($_POST['lat']) ? $_POST['lat'] : "";
        $long = isset($_POST['long']) ? $_POST['long'] : "";
        $speed = isset($_POST['speed']) ? $_POST['speed'] : 0;
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $status_gps = isset($_POST['status_gps']) ? $_POST['status_gps'] : "";
        $datetime = isset($_POST['datetime']) ? $_POST['datetime'] : "";


        /*
        echo $lat.$long; 
        echo $speed."<Br>";
        echo $id."<Br>";
        echo $status_gps."<Br>";
        echo $datetime."<Br>";
        //echo $speed."<Br>";
        exit();
        */

        $data = Sensor::find()
        ->where(['msisdn'=>$id])
        ->one();

        if($data != null) {
            $data->data_value1 = $lat;
            $data->data_value2 = $long;
            $data->data_value3 = $speed;
            $data->data_value4 = $id;
            $data->data_value5 = $status_gps;
            $data->data_value6 = $datetime;

            //$data->last_user_update = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            $data->last_update_ip_address = IPAddressFunction::getUserIPAddress();
            $data->last_update = Timeanddate::getCurrentDateTime();



            if($data->save(false)){
                $jsonFormat = [
                    [
                        "status" => true,
                        "msg" => "Data track sudah tersimpan!",
                        "datetime" => $data->last_update,
                        "reference" => $data->id_sensor,
                        "hw-report" => '#end#' 
                    ]
                ];
            }
            else{
                $jsonFormat = [
                    [
                        "status" => false,
                        "msg" => "Mohon maaf terdapat kesalahan ketika simpan. Silakan cek kembali!",
                        "result" => 0,
                        "hw-report" => '#end#' 
                    ]
                ];
            }
        }else{
            $jsonFormat = [
                [
                    "status" => false,
                    "msg" => "Sensor dengan id ".$id." tidak ditemukan di database",
                    "result" => 0,
                    "hw-report" => '#end#' 
                ]
            ];
        }



        return $jsonFormat;
    }


    //http://localhost/krl/api/web/v1/sensors/set-data-track-sim?IDDEV=1777&nmea=9012312
    public function actionSetDataTrackSimVer1(){
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;

        //$this->actionCheckAccessRequest(yii::$app->request->headers);
        $params = Yii::$app->request->queryParams;

        $lat = isset($_GET['lat']) ? $_GET['lat'] : "";
        $long = isset($_GET['long']) ? $_GET['long'] : "";
        $speed = isset($_GET['speed']) ? $_GET['speed'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : "";
        $status_gps = isset($_GET['status_gps']) ? $_GET['status_gps'] : "";
        $datetime = isset($_GET['datetime']) ? $_GET['datetime'] : "";
        $nmea = isset($_GET['nmea']) ? $_GET['nmea'] : "";
        $IDDEV = isset($_GET['IDDEV']) ? $_GET['IDDEV'] : "";

        /*
        echo $lat.$long; 
        echo $speed."<Br>";
        echo $nmea."<Br>";
        echo $IDDEV."<Br>";
        echo $id."<Br>";
        echo $status_gps."<Br>";
        echo $datetime."<Br>";
        */
        //echo $speed."<Br>";
        //exit();
        
        $id = 12345; //Harcoded dulu

        $data = Sensor::find()
        ->where(['msisdn'=>$id])
        ->one();

        if($data != null) {
            $data->data_value1 = $IDDEV;
            $data->data_value2 = $nmea;
            $data->data_value3 = $speed;
            $data->data_value4 = $id;
            $data->data_value5 = $status_gps;
            $data->data_value6 = $datetime;

            //$data->last_user_update = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            $data->last_update_ip_address = IPAddressFunction::getUserIPAddress();
            $data->last_update = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());

            //Save To Log
            $log = new SensorLog();
            $log->value1 = $lat;
            $log->value2 = $long;
            $log->value3 = $speed;
            $log->value4 = $id;
            $log->value5 = $status_gps;
            $log->value6 = $datetime;
            $log->id_sensor = $data->id_sensor;

            //$data->last_user_update = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            $log->log_date = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            $log->log_time = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            if($log->save(false)){
                //echo 'Berhasil';
            }else{
                //echo 'Gagal';
            }

            if($data->save(false)){
                $jsonFormat = [
                    [
                        "status" => true,
                        "msg" => "Data track sudah tersimpan!",
                        "datetime" => $data->last_update,
                        "reference" => $data->id_sensor,
                        "hw-report" => '#start#mr5swh3m20;x1=0;x2=0;x3=0;x4=0;x5=0;x6=0;x7=0;x8=0#end#' 
                    ]
                ];
            }
            else{
                $jsonFormat = [
                    [
                        "status" => false,
                        "msg" => "Mohon maaf terdapat kesalahan ketika simpan. Silakan cek kembali!",
                        "result" => 0,
                        "hw-report" => '#start#mr5swh3m20;x1=0;x2=0;x3=0;x4=0;x5=0;x6=0;x7=0;x8=0#end#'
                    ]
                ];
            }
        }else{
            $jsonFormat = [
                [
                    "status" => false,
                    "msg" => "Sensor dengan id ".$id." tidak ditemukan di database",
                    "result" => 0,
                    "hw-report" => '#start#mr5swh3m20;x1=0;x2=0;x3=0;x4=0;x5=0;x6=0;x7=0;x8=0#end#'
                ]
            ];
        }



        return $jsonFormat;
    }

    //http://localhost/krl/api/web/v1/sensors/set-data-track-sim?IDDEV=1777&nmea=9012312
    public function actionSetDataTrackSim(){
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;

        //$this->actionCheckAccessRequest(yii::$app->request->headers);
        $params = Yii::$app->request->queryParams;

        $lat = isset($_GET['lat']) ? $_GET['lat'] : "";
        $long = isset($_GET['long']) ? $_GET['long'] : "";
        $speed = isset($_GET['speed']) ? $_GET['speed'] : 0;
        $id = isset($_GET['id']) ? $_GET['id'] : "";
        $status_gps = isset($_GET['status_gps']) ? $_GET['status_gps'] : "";
        $datetime = isset($_GET['datetime']) ? $_GET['datetime'] : "";
        $nmea = isset($_GET['nmea']) ? $_GET['nmea'] : "";
        $IDDEV = isset($_GET['IDDEV']) ? $_GET['IDDEV'] : "";
        $datacomplete = isset($_GET['data']) ? $_GET['data'] : "";

        //Contoh NMEA
        //12345,000100.00,A,0654.43621,S,10737.73779,E,0.037,,300723,44,99
        //id, jam, status, lat, lat2, lon, lon2, speed, course over ground, tanggal, suhu, humidity
        $list = explode(",",$datacomplete);
        if(count($list) >= 8){
            /*
            echo $list[0]; //id
            echo $list[1]; //jam
            echo $list[2]; //status
            echo $list[3]; //lat
            echo $list[4]; //lat2
            echo $list[5]; //lon
            echo $list[6]; //lon2
            echo $list[7]; //speed
            echo $list[8]; //course over ground
            echo $list[9]; //tanggal
            echo $list[10]; //suhu
            echo $list[11]; //humidity
            */
            $IDDEV = $list[0];
            $lat = $list[3].",".$list[4];
            $long = $list[5].",".$list[6];
            if($list[7] != ""){
                $speed = $list[7]*1.852; //KOnvert dari knot ke km/jam
            }else{
                $speed = 0;
            }
            $suhu = $list[10];
            $humidity = $list[11];
        }

        /*
        echo $lat.$long; 
        echo $speed."<Br>";
        echo $nmea."<Br>";
        echo $IDDEV."<Br>";
        echo $id."<Br>";
        echo $status_gps."<Br>";
        echo $datetime."<Br>";
        */
        //echo $speed."<Br>";
        //exit();
        
        $id = 12345; //Harcoded dulu

        $data = Sensor::find()
        ->where(['imei'=>$IDDEV])
        ->one();

        if($data != null) {
            //$data->data_value1 = $lat;
            $data->data_value1 = \common\helpers\GisHelper::convertNMEAToDegreeFormat("lat", $lat);
            //$data->data_value2 = $long;
            $data->data_value2 = \common\helpers\GisHelper::convertNMEAToDegreeFormat("long", $long);
            $data->data_value3 = $speed;
            $data->data_value4 = $suhu;
            $data->data_value5 = $humidity;
            $data->data_value6 = $datacomplete;

            //$data->last_user_update = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            $data->last_update_ip_address = IPAddressFunction::getUserIPAddress();
            $data->last_update = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());

            //Save To Log
            $log = new SensorLog();
            //$log->value1 = $lat;
            $log->value1 = \common\helpers\GisHelper::convertNMEAToDegreeFormat("lat", $lat);
            //$log->value2 = $long;
            $log->value2 = \common\helpers\GisHelper::convertNMEAToDegreeFormat("long", $long);

            $log->value3 = $speed;
            $log->value4 = $suhu;
            $log->value5 = $humidity;
            $log->value6 = $datacomplete;
            $log->id_sensor = $data->id_sensor;

            //$data->last_user_update = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            $log->log_date = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            $log->log_time = Timeanddate::getGMTPlus7Time(Timeanddate::getCurrentDateTime());
            if($log->save(false)){
                //echo 'Berhasil';
            }else{
                //echo 'Gagal';
            }

            if($data->save(false)){
                $jsonFormat = [
                    //[
                        /*
                        "status" => "true",
                        //"msg" => "Data track ".$IDDEV." sudah tersimpan!",
                        //"datetime" => $data->last_update,
                        //"reference" => $data->id_sensor,
                        "status-digital" => "true",
                        "hw-report" => "#start#mr5swh3m20;x1=0;x2=0;x3=0;x4=0;x5=0;x6=0;x7=0;x8=0#end#",
                        */
                        "status" => "true",
                        "hw-report" => "true",
                        "d1"=>"1",
                        "d2"=>"0",
                        "d3"=>"1",
                        "msg" => "Data track ".$IDDEV." sudah tersimpan!",
                        //"status-digital" => "true",
                        //"d1"=>"1",
                        //"d2"=>"0",
                        //"d3"=>"1"
                        /*
                        "digital-input" => [
                            "d1"=>"1",
                            "d2"=>"0",
                            "d3"=>"1"
                        ]
                        */
                    //]
                ];
            }
            else{
                $jsonFormat = [
                    //[
                        "status" => "false",
                        "msg" => "Mohon maaf terdapat kesalahan ketika simpan. Silakan cek kembali!",
                        //"result" => 0,
                        "hw-report" => '#start#mr5swh3m20;x1=0;x2=0;x3=0;x4=0;x5=0;x6=0;x7=0;x8=0#end#'
                    //]
                ];
            }
        }else{
            $jsonFormat = [
                //[
                    "status" => "false",
                    "msg" => "Sensor dengan id ".$IDDEV." tidak ditemukan di database",
                    //"result" => 0,
                    "hw-report" => '#start#mr5swh3m20;x1=0;x2=0;x3=0;x4=0;x5=0;x6=0;x7=0;x8=0#end#'
                //]
            ];
        }



        return $jsonFormat;
    }

    public function actionGetHomeInfo(){
        $records = HomeInfo::find()
        ->orderBy(["no" => SORT_ASC])
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["judul"] = $record->judul;
            $datas["deskripsi"] = $record->deskripsi;

            $rows[] = $datas;
        }

        //Bagian Content
        $records2 = Content::find()
        ->where(['id_section_content'=>2])
        //->orderBy(["no" => SORT_ASC])
        ->all();
        $rows2 = array();
        foreach($records2 as $data){
            $datas2 = array();
            $datas["judul"] = $data->keyname;
            $datas["content"] = $data->content_lang1;
            $datas["have_image"] = $data->have_image;
            if($data->have_image == 1){
                $datas["image"] = ImageManipulation::getImageURLFrontendGeneric($data,  "image_filename","content");
            }else{
                  $datas["image"] = "";
            }

            $rows2[] = $datas;
        }

        $myobj = new ResponMessageMultiple();
        $myobj->items = $rows;
        $myobj->items2 = $rows2;
        return json_encode($myobj,JSON_UNESCAPED_SLASHES);
    }

    public function actionGetSpecificContent($key){
        $data = Content::find()
        ->where(['keyname'=>$key])
        ->one();
        $datas = array();
        if($data != null){

            $datas["content"] = $data->content_lang1;
            $datas["have_image"] = $data->have_image;
            if($data->have_image == 1){
                $datas["image"] = ImageManipulation::getImageURLFrontendGeneric($data,  "image_filename","content");
            }else{
                  $datas["image"] = "";
            }

            //$rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $datas;
        return json_encode($myobj, JSON_UNESCAPED_SLASHES);
    }

    public function actionGetAboutApp(){
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;

        $this->actionCheckAccessRequest(yii::$app->request->headers);
        $params = Yii::$app->request->queryParams;

        $jsonFormat = [
            [
                "app_name" => "DISDROMETER APP",
                "version" => "1.0.0 (Beta)",
                "release_date" => 'Sept 2020',
                "description" => 'Aplikasi ini merupakan aplikasi untuk memonitor curah hujan dan status informasi yang diberikan. Memberikan informasi peringatan kepada para pengguna',
                "contact" => 'Untuk kontak silakan hubungi BMKG (021-4201111)',
            ]
        ];

        return $jsonFormat;


    }

    public function actionCreateSensor()
    {
        \Yii::$app->getResponse()->format= \yii\web\Response::FORMAT_JSON;
//        return array('status'=> true);
        $assetitem = new Sensor();

        $assetitem->scenario=Sensor::SCENARIO_CREATE;
        $assetitem->attributes = \Yii::$app->request->post();

        if ($assetitem->validate()){
            $assetitem->save();
            return array('status' => true, 'data'=>'Sensor Created successfully');
        }else {
            return array('status' => false,'data' => $assetitem->getErrors());
        }


    }

    public function actionListSensor()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $assetItemList = Sensor::find()->all();
        if (count($assetItemList) > 0){
            return array('status'=> true,'data'=> $assetItemList);
        }else{
            return array('status'=> false, 'data'=> 'No AssetItem Found.');
        }
    }

    public function actionGetData(){
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;
        return array('info' => 'Test GEt Data');
    }
	
	public function actionSetValue(){
		\Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;

		$postData = \yii::$app->request->post();
		if(isset($postData['im'])){
			$imei = $postData['im'];
		}else{
			$imei = "==";
		}
		
		if(isset($postData['c'])){
			$cid = $postData['c'];
		}else{
			$cid = "==";
		}

		$sa = Sensor::find()
			->where(['imei' => $imei, 'cid'=>$cid])
			->one();
		if($sa != null){
			$x=10;
			for($i=1;$i<=$x;$i++){
				if(isset($postData['A'.$i])){
					$field  = "sensor_analog".$i;
					
					$val = $postData['A'.$i];
					$sa->$field= $val;
				}
			}
			
			$sa->save(false);
			return array('status'=>true,
				'name' => $sa->sensor_name, 

			);
		}else{
			return array('status' => false, 'name' => 'Unknown device');
		}
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