<?php

namespace common\helpers;

use Yii;
use yii\base\Component;

class ContentStringManipulator extends Component
{

    public static function changeParagrafIntoSpan($string)
    {
        //Untuk mengganti parafraf html menjadi span agar tidak ketimpa CSSnya
		$res = str_replace("<p","<span",$string);
		$res = str_replace("/p>","/span>",$res);
        return $res;
    }

	public static function changeParagrafIntoDiv($string)
    {
        //Untuk mengganti parafraf html menjadi span agar tidak ketimpa CSSnya
		$res = str_replace("<p","<div",$string);
		$res = str_replace("/p>","/div>",$res);
        return $res;
    }
}
