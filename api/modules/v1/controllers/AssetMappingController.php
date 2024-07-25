<?php
namespace api\modules\v1\controllers;

//use yii\rest\ActiveController;
use api\modules\v1\models\AssetItem;
use api\modules\v1\models\AssetItemLog;
use api\modules\v1\models\AssetMaster;
use api\modules\v1\models\SensorLocation;
use api\modules\v1\helpers\ImageManipulation;
use api\modules\v1\models\ResponMessage;
use api\modules\v1\models\ResponMessageSingle;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use api\modules\v1\helpers\Timeanddate;
use api\modules\v1\helpers\IPAddressFunction;
//use api\modules\v1\helpers\ImageManipulation;

use app\common\helpers\CoordinateCalculation;
use Yii;


class AssetMappingController extends Controller
{
    public $enableCsrfValidation=false;
//    public $modelClass = 'app\models\AssetItem';

    /*
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }
    */

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Mengaktifkan CORS
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => ['*'],
            ],
        ];

        $behaviors['verbs'] = [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
        ];

        return $behaviors;
    }

    public function actionPostLocation(){
         \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;

        //$this->actionCheckAccessRequest(yii::$app->request->headers);
        $params = Yii::$app->request->queryParams;

        $nama_barang = isset($_POST['nama_barang']) ? $_POST['nama_barang'] : "";
        $waktu_scan = isset($_POST['waktu_scan']) ? $_POST['waktu_scan'] : "";
        $accuracy = isset($_POST['accuracy']) ? $_POST['accuracy'] : 0;
        $id_device = isset($_POST['id_device']) ? $_POST['id_device'] : "";
        $id_object = isset($_POST['id_object']) ? $_POST['id_object'] : 0;
        $id_object = $id_object*1;
        $id_barang = isset($_POST['id_barang']) ? $_POST['id_barang'] : "";

        /*
        echo $nama_barang; 
        echo $waktu_scan."<Br>";
        echo $accuracy."<Br>";
        echo $id_device."<Br>";
        echo $id_object."<Br>";
        echo $id_barang."<Br>";
        exit();
        */
        

        $data = AssetMaster::find()
        ->where(['asset_name'=>$nama_barang])
        ->one();

        if($data == null) {
            //echo 'create data';
            $data = new AssetMaster();
            $data->asset_name = $nama_barang;
            $data->generateMasterCode();
            if($data->save(false)){

            }
        }


        //Mendapatkan id_asset_item
        if($id_object > 0){
            $assetitem = AssetItem::find()
            ->where([
                'id_asset_master'=>$data->id_asset_master,
                'number_series'=>$id_object,
            ])
            ->one();

            if($assetitem == null) {
                $assetitem = new AssetItem();
                $assetitem->id_asset_master = $data->id_asset_master;
                $assetitem->number_series = $id_object;
                $assetitem->generateAssetItemNumberByNumberSeries();
                if($assetitem->save(false)){

                }
            }
        }

        //Dapatkan posisi device
        $id_location_unit = 0;
        if($id_device > 0){
            $loc = SensorLocation::find()
            ->where([
                'id_sensor'=>$id_device,
            ])
            ->one();

            if($loc != null) {
                $id_location_unit = $loc->id_location_unit;
                //echo $id_location_unit; exit();

                $loc->last_updated = $waktu_scan;
                if($loc->save(false)){
                }
            }
        }




        //Update posisi lokasi barang
        $locunit = \backend\models\AssetItemLocationUnit::find()
            ->where([
                'id_asset_item'=>$assetitem->id_asset_item,
            ])
            ->one();
        if($locunit == null) {
            $locunit = new \backend\models\AssetItemLocationUnit();
            $locunit->id_asset_item = $assetitem->id_asset_item;
            $locunit->id_location_unit = $id_location_unit;
            $locunit->id_asset_master = $data->id_asset_master;


            if($locunit->save(false)){
                //echo "Buat berhasil"; exit();
            }
        }else{
            //Update posisi
            $locunit->id_location_unit = $id_location_unit;
            $locunit->id_asset_master = $data->id_asset_master;
            if($locunit->save(false)){
                //echo "Updated berhasil"; exit();
            }
        }

        //Simpan ke data log
        $loglocation = new \backend\models\SensorLogLocation();
        $loglocation->id_asset_item = $assetitem->id_asset_item;
        $loglocation->id_location_unit = $id_location_unit;
        $loglocation->id_asset_master = $data->id_asset_master;
        $loglocation->id_sensor = $id_device;
        $loglocation->number_series = $id_object;
        $loglocation->waktu_scan = $waktu_scan;
        $loglocation->accuracy = $accuracy;

        if($loglocation->save(false)){
        }

        $jsonFormat = [
                [
                    "status" => true,
                    "msg" => "Success update location",
                    "result" => 0,
                    "hw-report" => '#end#' 
                ]
            ];



        return $jsonFormat;
    }


    public function actionIndex() {
        $header = header('Access-Control-Allow-Origin: *');
        //$header = header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        //$header = header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

        $records = AssetItem::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id"] = $record->id_asset_item;
            $datas["asset_code"] = $record->number1;
            $datas["name"] = isset($record->assetMaster) ? $record->assetMaster->asset_name : "-";
            $datas["category"] = isset($record->assetMaster->typeAsset1) ? $record->assetMaster->typeAsset1->type_asset : "-";

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        //return json_encode($myobj);
        // Mengatur format respons menjadi JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $myobj;
    }

    public function actionAll() {
        $header = header('Access-Control-Allow-Origin: *');
        //$header = header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        //$header = header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

        $records = AssetItem::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id"] = $record->id_asset_item;
            $datas["asset_code"] = $record->number1;
            $datas["name"] = isset($record->assetMaster) ? $record->assetMaster->asset_name : "-";
            $datas["category"] = isset($record->assetMaster->typeAsset1) ? $record->assetMaster->typeAsset1->type_asset : "-";

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        //return json_encode($myobj);
        // Mengatur format respons menjadi JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $myobj;
    }

    public function actionAllFilterByCategory() {
        $header = header('Access-Control-Allow-Origin: *');
        //$header = header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        //$header = header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        $id_type_asset1 = isset($_GET['id_category']) ? $_GET['id_category'] : 0;

        $records = AssetItem::find()
        ->joinWith('assetMaster') // 'assetMaster' sesuai dengan nama relasinya di model AssetItem
        ->where(['asset_master.id_type_asset1' => $id_type_asset1])
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id"] = $record->id_asset_item;
            $datas["asset_code"] = $record->number1;
            $datas["name"] = isset($record->assetMaster) ? $record->assetMaster->asset_name : "-";
            $datas["category"] = isset($record->assetMaster->typeAsset1) ? $record->assetMaster->typeAsset1->type_asset : "-";

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        //return json_encode($myobj);
        // Mengatur format respons menjadi JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $myobj;
    }

    public function actionSearchByName() {
        $header = header('Access-Control-Allow-Origin: *');
        //$header = header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        //$header = header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        $name = isset($_GET['name']) ? $_GET['name'] : "";

        $records = AssetItem::find()
        ->joinWith('assetMaster') // 'assetMaster' sesuai dengan nama relasinya di model AssetItem
        ->where(['like', 'asset_master.asset_name', $name])
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id"] = $record->id_asset_item;
            $datas["asset_code"] = $record->number1;
            $datas["name"] = isset($record->assetMaster) ? $record->assetMaster->asset_name : "-";
            $datas["category"] = isset($record->assetMaster->typeAsset1) ? $record->assetMaster->typeAsset1->type_asset : "-";

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        //return json_encode($myobj);
        // Mengatur format respons menjadi JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $myobj;
    }

    public function actionSearchByCode() {
        $header = header('Access-Control-Allow-Origin: *');
        //$header = header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        //$header = header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        $code = isset($_GET['code']) ? $_GET['code'] : "";

        $records = AssetItem::find()
        //->joinWith('assetMaster') // 'assetMaster' sesuai dengan nama relasinya di model AssetItem
        ->where(['number1' => $code])
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id"] = $record->id_asset_item;
            $datas["asset_code"] = $record->number1;
            $datas["name"] = isset($record->assetMaster) ? $record->assetMaster->asset_name : "-";
            $datas["category"] = isset($record->assetMaster->typeAsset1) ? $record->assetMaster->typeAsset1->type_asset : "-";

            $rows[] = $datas;
        }

        $myobj = new ResponMessage();
        $myobj->items = $rows;
        //return json_encode($myobj);
        // Mengatur format respons menjadi JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $myobj;
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

    public function actionGetListAssetItem(){
        $records = AssetItem::find()
        ->all();
        $rows = array();
        foreach($records as $record){
            $datas = array();
            $datas["id_AssetItem"] = $record->id_AssetItem;
            $datas["AssetItem_name"] = $record->AssetItem_name;
            $datas["AssetItem_analog1"] = $record->AssetItem_analog1;

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

        $data = AssetItem::find()
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
                        "reference" => $data->id_AssetItem,
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
                    "msg" => "AssetItem dengan id ".$id." tidak ditemukan di database",
                    "result" => 0,
                    "hw-report" => '#end#' 
                ]
            ];
        }



        return $jsonFormat;
    }


    //http://localhost/krl/api/web/v1/AssetItems/set-data-track-sim?IDDEV=1777&nmea=9012312
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

        $data = AssetItem::find()
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
            $log = new AssetItemLog();
            $log->value1 = $lat;
            $log->value2 = $long;
            $log->value3 = $speed;
            $log->value4 = $id;
            $log->value5 = $status_gps;
            $log->value6 = $datetime;
            $log->id_AssetItem = $data->id_AssetItem;

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
                        "reference" => $data->id_AssetItem,
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
                    "msg" => "AssetItem dengan id ".$id." tidak ditemukan di database",
                    "result" => 0,
                    "hw-report" => '#start#mr5swh3m20;x1=0;x2=0;x3=0;x4=0;x5=0;x6=0;x7=0;x8=0#end#'
                ]
            ];
        }



        return $jsonFormat;
    }

    public function actionGetItem($id){
        $header = header('Access-Control-Allow-Origin: *');
        $header = header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        $header = header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $id = $id*1;

        if($id > 0){
            $record = AssetItem::find()
            ->where([
                'id_asset_item'=>$id
            ])
            ->one();
            
            if($record != null){
                $datas["id"] = $record->id_asset_item;
                $datas["asset_code"] = $record->number1;
                $datas["name"] = isset($record->assetMaster) ? $record->assetMaster->asset_name : "-";
                $datas["category"] = isset($record->assetMaster->typeAsset1) ? $record->assetMaster->typeAsset1->type_asset : "-";

                $myobj = new ResponMessageSingle();
                $myobj->data = $datas;
            }else{
                $myobj = new ResponMessageSingle();
                $myobj->status = "error";
                $myobj->message = "Data tidak ditemukan.";    
            }
        }else{
            $myobj = new ResponMessageSingle();
            $myobj->status = "error";
            $myobj->message = "Data tidak ditemukan.";    
        }
        
        //return json_encode($myobj, JSON_UNESCAPED_SLASHES);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $myobj;
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

    public function actionCreateAssetItem()
    {
        \Yii::$app->getResponse()->format= \yii\web\Response::FORMAT_JSON;
//        return array('status'=> true);
        $assetitem = new AssetItem();

        $assetitem->scenario=AssetItem::SCENARIO_CREATE;
        $assetitem->attributes = \Yii::$app->request->post();

        if ($assetitem->validate()){
            $assetitem->save();
            return array('status' => true, 'data'=>'AssetItem Created successfully');
        }else {
            return array('status' => false,'data' => $assetitem->getErrors());
        }


    }

    public function actionListAssetItem()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $assetItemList = AssetItem::find()->all();
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

		$sa = AssetItem::find()
			->where(['imei' => $imei, 'cid'=>$cid])
			->one();
		if($sa != null){
			$x=10;
			for($i=1;$i<=$x;$i++){
				if(isset($postData['A'.$i])){
					$field  = "AssetItem_analog".$i;
					
					$val = $postData['A'.$i];
					$sa->$field= $val;
				}
			}
			
			$sa->save(false);
			return array('status'=>true,
				'name' => $sa->AssetItem_name, 

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