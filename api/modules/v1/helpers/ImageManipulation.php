<?php

namespace api\modules\v1\helpers;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use api\modules\v1\helpers\EncryptionDB;

class ImageManipulation extends Component
{

    public static function uploadFilePelanggaranToFolder($data, $model, $filename)
    {
        if($data != ""){
            $repo = "http://localhost/simdek/".Yii::$app->urlManagerBackend->baseUrl."/uploads/pelanggaran/";
            //$repo = Yii::getAlias('@web');
           //$repo = Yii::getAlias('@frontend/web/upload/laporan_masyarakat/') ;
           // $repo = Url::base() . '/../frontend/web/upload/laporan_masyarakat/' ;
           

            $repo = "uploads/pelanggaran/"; //Ini berhasil untuk lokal api
            $repo = "../../backend/web/uploads/pelanggaran/";
            //$repo = Yii::getAlias('@web')."/uploads/pelanggaran/";
            //$repo = "/uploads/pelanggaran/";
            
            /*
            echo $repo;
            if(is_dir($repo)){
                echo "Ada"; 
                //exit();
            }else{
                echo ("Tidak ada"); 
                //exit();
            }
            */
            $idc = EncryptionDB::staticEncryptor("encrypt", $model->id_pelanggaran);
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                    //throw new \Exception('invalid image type');
                    return "Type image tidak sesuai";
                }

                $data = base64_decode($data);

                if ($data === false) {
                    //throw new \Exception('base64_decode failed');
                    return "Decode image tidak berhasil";
                }
            } else {
                //throw new \Exception('did not match data URI with image data');
                return "Bukan data image";
            }

            $filenamenew = $filename."_".$idc.".{$type}";
            $filepathname = $repo.$filename."_".$idc.".{$type}";
            if(file_exists($filepathname)){
                unlink($filepathname);
            }
            //$filepath = Yii::$app->request->baseUrl.$repo."pelanggaran.{$type}";
            //return $filepath;
            //file_put_contents($repo."pelanggaran".$model->id_pelanggaran."_.{$type}", $data);
            //$filepathname = $repo."pelanggaran".$model->id_pelanggaran."_.{$type}";

            //$ifp = fopen( $filepathname, 'wb' );
            file_put_contents($filepathname, $data);
            return $filenamenew;
        }else{
            return "";
        }
    }

    public static function uploadFileLaporanMasyrakatToFolder($data, $model, $filename, $id)
    {
        if($data != ""){
            $repo = "uploads/pelanggaran/"; //Ini berhasil untuk lokal api
            $repo = "../../backend/web/uploads/laporan/";

            $idc = EncryptionDB::staticEncryptor("encrypt", $id);
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                    //throw new \Exception('invalid image type');
                    return "Type image tidak sesuai";
                }

                $data = base64_decode($data);

                if ($data === false) {
                    //throw new \Exception('base64_decode failed');
                    return "Decode image tidak berhasil";
                }
            } else {
                //throw new \Exception('did not match data URI with image data');
                return "Bukan data image";
            }

            $filenamenew = $filename."_".$idc.".{$type}";
            $filepathname = $repo.$filename."_".$idc.".{$type}";
            if(file_exists($filepathname)){
                unlink($filepathname);
            }
            //$filepath = Yii::$app->request->baseUrl.$repo."pelanggaran.{$type}";
            //return $filepath;
            //file_put_contents($repo."pelanggaran".$model->id_pelanggaran."_.{$type}", $data);
            //$filepathname = $repo."pelanggaran".$model->id_pelanggaran."_.{$type}";

            //$ifp = fopen( $filepathname, 'wb' );
            file_put_contents($filepathname, $data);
            return $filenamenew;
        }else{
            return "";
        }
    }

    public static function uploadFileGeneric($data, $filename, $id, $folder)
    {
        if($data != ""){
            $repo = "../../backend/web/uploads/".$folder."/";

            $idc = EncryptionDB::staticEncryptor("encrypt", $id);
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                    //throw new \Exception('invalid image type');
                    return "Type image tidak sesuai";
                }

                $data = base64_decode($data);

                if ($data === false) {
                    //throw new \Exception('base64_decode failed');
                    return "Decode image tidak berhasil";
                }
            } else {
                //throw new \Exception('did not match data URI with image data');
                return "Bukan data image";
            }

            $filenamenew = $filename."_".$idc.".{$type}";
            $filepathname = $repo.$filename."_".$idc.".{$type}";
            if(file_exists($filepathname)){
                unlink($filepathname);
            }

            //$ifp = fopen( $filepathname, 'wb' );
            file_put_contents($filepathname, $data);
            return $filenamenew;
        }else{
            return "";
        }
    }

    public static function getImageURL($model, $fieldname)
    {
        if($model->$fieldname != ""){
            if(strlen($model->$fieldname <= 250)){
                return Yii::$app->urlManagerBackend->baseUrl."/uploads/pelanggaran/".$model->$fieldname;
            }else{
                return "";
            }
        }else{
            return "";
        }
    }

    public static function getImageURLGeneric($model, $fieldname, $foldername)
    {
        if($model->$fieldname != ""){
            if(strlen($model->$fieldname <= 250)){
                return Yii::$app->urlManagerBackend->baseUrl."/uploads/".$foldername."/".$model->$fieldname;
            }else{
                return "";
            }
        }else{
            return "";
        }
    }

    public static function getImageURLFrontendGeneric($model, $fieldname, $foldername)
    {
        if($model->$fieldname != ""){
            if(strlen($model->$fieldname <= 250)){
                return Yii::$app->urlManagerFrontend->baseUrl."/img/".$foldername."/".$model->$fieldname;
            }else{
                return "";
            }
        }else{
            return "";
        }
    }
}
