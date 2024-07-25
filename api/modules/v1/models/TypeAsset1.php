<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "type_asset1".
 *
 * @property int $id_type_asset
 * @property string $type_asset
 * @property string|null $description
 * @property int $is_active
 */
class TypeAsset1 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type_asset1';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_asset'], 'required'],
            [['description'], 'string'],
            [['is_active'], 'integer'],
            [['type_asset'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_type_asset' => 'Id Type Asset',
            'type_asset' => 'Type Asset',
            'description' => 'Description',
            'is_active' => 'Is Active',
        ];
    }

    public function getStatusAktif()
    {
        if ($this->is_active == 1) {
            return 'AKTIF';
        } else {
            return "TIDAK AKTIF";
        }
    }
}
