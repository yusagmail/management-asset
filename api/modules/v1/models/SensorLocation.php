<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sensor_location".
 *
 * @property int $id_sensor_location
 * @property int $id_sensor
 * @property int $id_location_unit
 * @property string $last_updated
 */
class SensorLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sensor_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sensor', 'id_location_unit'], 'required'],
            [['id_sensor', 'id_location_unit'], 'integer'],
            [['last_updated'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_sensor_location' => 'Id Sensor Location',
            'id_sensor' => 'Sensor',
            'id_location_unit' => 'Location Unit',
            'last_updated' => 'Last Updated',
        ];
    }

    public function getSensor()
    {
        return $this->hasOne(Sensor::className(), ['id_sensor' => 'id_sensor']);
    }

    public function getLocationUnit()
    {
        return $this->hasOne(LocationUnit::className(), ['id_location_unit' => 'id_location_unit']);
    }
}
