<?php


namespace api\modules\v1\controllers;



use yii\filters\VerbFilter;
use yii\web\Controller;

class TestController extends Controller
{

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
        return array('info' => 'Hello World');
    }

}