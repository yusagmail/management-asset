<?php

namespace api\modules\v1\models;

/**
 * This is the model class for table "asset_master".
 *
 * @property int $id_asset_master
 * @property string $asset_name
 * @property int $id_asset_code
 * @property string $asset_code
 * @property int $id_type_asset1
 * @property int $id_type_asset2
 * @property int $id_type_asset3
 * @property int $id_type_asset4
 * @property int $id_type_asset5
 * @property string $attribute1
 * @property string $attribute2
 * @property string $attribute3
 * @property string $attribute4
 * @property string $attribute5
 */
class AssetMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asset_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
		$ruleslist = AppFieldConfigSearch::getRules(AssetMaster::tableName());
		
		//jika mau menambah atturan sendiri
		/*
		$ruleslist[] = [['id_type_asset2', 'id_type_asset3'], 'required'];
		$ruleslist[] = [['attribute1','attribute2'], 'string', 'max' => 1];
		*/

        $ruleslist[] = [['deskripsi'], 'string'];
        $ruleslist[] = [['date'], 'safe'];
        //$ruleslist[] = [['id_supplier'], 'string'];
		return $ruleslist;
        
		
		/*
        return [
            [['asset_name', 'asset_code'], 'required'],
            [['id_asset_code', 'id_type_asset1', 'id_type_asset2', 'id_type_asset3', 'id_type_asset4', 'id_type_asset5'], 'integer'],
            [['asset_name', 'attribute1', 'attribute2', 'attribute3', 'attribute4', 'attribute5'], 'string', 'max' => 250],
            [['asset_code'], 'string', 'max' => 150],
        ];
		*/
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
		$labelArray = AppFieldConfigSearch::getLabels(AssetMaster::tableName());
		
		//Jika Mau Menambahkan Sendiri manual
		/*
		$labelArray['id_type_asset2'] = "Label Test 2";
		$labelArray['id_type_asset3'] = "Label Test 3";
		*/
		return $labelArray;
		
		//Ini yang hardcode dimatikan
		
        return [
            'id_asset_master' => 'Id Asset Master',
            'asset_name' => 'Nama Barang',
            'id_asset_code' => 'Id Asset Code',
            'asset_code' => 'Kode Master',
            'date' => 'Tanggal Register',
            'id_supplier' => 'Supplier',
            'id_type_asset1' => 'Jenis Aset',
            'id_type_asset2' => 'Type Asset',
            'id_type_asset3' => 'Type Asset  3',
            'id_type_asset4' => 'Type Asset  4',
            'id_type_asset5' => 'Type Asset  5',
            'attribute1' => 'Attribute  1',
            'attribute2' => 'Attribute  2',
            'attribute3' => 'Attribute  3',
            'attribute4' => 'Attribute  4',
            'attribute5' => 'Attribute  5',
        ];
		
    }

    public function getTypeAsset1()
    {
        return $this->hasOne(TypeAsset1::className(), ['id_type_asset' => 'id_type_asset1']);
    }

    public function getTypeAsset2()
    {
        return $this->hasOne(TypeAsset2::className(), ['id_type_asset' => 'id_type_asset2']);
    }

    public function getTypeAsset3()
    {
        return $this->hasOne(TypeAsset3::className(), ['id_type_asset' => 'id_type_asset3']);
    }

    public function getTypeAsset4()
    {
        return $this->hasOne(TypeAsset4::className(), ['id_type_asset' => 'id_type_asset4']);
    }

    public function getTypeAsset5()
    {
        return $this->hasOne(TypeAsset5::className(), ['id_type_asset' => 'id_type_asset5']);
    }

    public function getAssetCode()
    {
        return $this->hasOne(AssetCode::className(), ['id_asset_code' => 'id_asset_code']);
    }
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id_supplier' => 'id_supplier']);
    }
    public function getYear()
    {
        
    }

    public function generateMasterCode(){

        //Cara Generate Nomor
        /* Ada beberapa cara. 
            1) Cara pertama berdasarkan kategori di type_asset 
            2) Cara kedua menggunakan berdasarkan stuktur di aset master

        */
        $numberingMethod = AppSettingSearch::getValueByKey("MASTER-NUMBER-BASED");
        switch ($numberingMethod){
            case "TYPE_ASSET":
                //Caranya adalah dengan menyusun berdasakan kode dari tabel type_asset1, type_asset2, dst
                /*
                Contoh terpilih sebagai berikut :
                kode_type_asset1 = 0004
                kode_type_asset2 = 0010
                Maka kodenya adalah 0004.0010
                */
                if($this->asset_code == ""){
                    //Kategori yang digunakan di setting di sini
                    $keyTypeAsset = AppSettingSearch::getValueByKey("MASTER-NUMBER-TYPE-ASSET-KEY");
                    $arrayList = explode(';', $keyTypeAsset);
                    $listKey = array();
                    foreach($arrayList as $key=>$value){
                        if($value != ""){
                            $listKey[] = $value;
                        }
                    }

                    $listWhere = array();
                    $prefix = AppSettingSearch::getValueByKey("MASTER-NUMBER-PREFIX");
                    foreach($listKey as $key=>$value){
                        //echo $value."&";
                        $field = "id_".$value;
                        $listWhere[$field] = $this->$field;

                        //Get Kode Untuk Setiap Type Asset
                        // Mengonversi huruf pertama dari setiap kata menjadi huruf besar
                        $modelName = str_replace('_', '', ucwords($value, '_'));

                        $modelClass = "backend\\models\\$modelName";
                        $modelspek = $modelClass::findOne($this->$field);
                        if (($modelspek) !== null) {
                            $prefix .= $modelspek->kode.".";

                        }
                       
                    }
                   
                    
                    $max = AssetMaster::find()
                    ->where($listWhere)
                    ->max('number_series');

                    $number_series = $max+1;
                    $this->number_series = $number_series;
                    
                    $nomer = sprintf("%04d", $number_series); //4 digit saja

                    $result = $prefix.$nomer;
                    $this->asset_code = $result;


                    //Cek Dulu apakah nomor duplicate atau tidak
                    $statusDuplicate = true;
                    $x = 0;
                    while($statusDuplicate){
                        $x++;
                        //Rekursif
                        $modelReceived = \backend\models\AssetMaster::find()
                            ->where(['asset_code' => $result])
                            ->one();
                        if($modelReceived == null){
                            $statusDuplicate = false;
                        }else{
                            $number_series = $number_series+1;
                            $nomer = sprintf("%04d", $number_series); //4 digit saja

                            $result = $prefix.$nomer;
                            $this->asset_code = $result;
                            $this->number_series = $number_series;
                        }

                        if($x > 100){
                            //Biar tidak infinite loop
                            $statusDuplicate = false;
                        }
                    }

                    $this->save(false);
                    return $result;

                }

                break;
        }
    }
}
