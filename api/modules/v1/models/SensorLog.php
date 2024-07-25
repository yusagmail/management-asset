<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sensor_log".
 *
 * @property string $id_sensor_log
 * @property string $id_sensor
 * @property string $log_time
 * @property string $log_date
 * @property double $value1
 * @property double $value2
 * @property double $value3
 * @property double $value4
 * @property double $value5
 */
class SensorLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sensor_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sensor', 'log_time', 'log_date', 'value1'], 'required'],
            [['id_sensor'], 'integer'],
            [['log_time', 'log_date'], 'safe'],
            [['value1', 'value2', 'value3', 'value4', 'value5'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_sensor_log' => 'Id Sensor Log',
            'id_sensor' => 'Id Sensor',
            'log_time' => 'Log Time',
            'log_date' => 'Log Date',
            'value1' => 'Suhu',
            'value2' => 'Value2',
            'value3' => 'Value3',
            'value4' => 'Value4',
            'value5' => 'Value5',
        ];
    }
    public function getSensor()
    {
        return $this->hasOne(Sensor::className(), ['id_sensor' => 'id_sensor']);
    }
    public function actionIndex()

    {

        $this->view->title = 'custom-title';

        return $this->render('index');

    }
}
