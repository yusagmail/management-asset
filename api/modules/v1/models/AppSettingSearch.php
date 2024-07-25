<?php

namespace api\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AppSetting;


/**
 * AppSettingSearch represents the model behind the search form of `backend\models\AppSetting`.
 */
class AppSettingSearch extends AppSetting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_app_setting', 'is_image'], 'integer'],
            [['setting_name', 'value'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AppSetting::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_app_setting' => $this->id_app_setting,
            'is_image' => $this->is_image,
        ]);

        $query->andFilterWhere(['like', 'setting_name', $this->setting_name])
            ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
	
	public static function getValueByKey($key, $defaultValue=""){
		$one = AppSetting::find()
                ->where(['setting_name' => $key])
                ->one();
		if($one != null){
			return $one->value;
		}else{
			if($defaultValue != ""){
				return $defaultValue;
			}
		}
		
		return "--";
	}
	
	public static function getValueImageByKey($key, $defaultValue="", $prefix="web"){
		$one = AppSetting::find()
                ->where(['setting_name' => $key])
                ->one();
		if($one != null){
			if($one->value != ""){
				return $prefix."/images/app_setting/".$one->value;
			}else{
				if($defaultValue != ""){
					return $defaultValue;
				}
			}
		}else{
			if($defaultValue != ""){
				return $defaultValue;
			}
		}
		
		return "";
	}

	public static function getImageUrl($key, $defaultUrl){
		$one = AppSetting::find()
            ->where(['setting_name' => $key])
            ->one();
		if($one != null){
			//$res = '<img src="' . '../..' . '/frontend/web/images/app_setting/' . $one->value . '" class="img-responsive">';
			$res =  '../..' . '/frontend/web/images/app_setting/' . $one->value;

			return $res;
		}else{
			return "";
		}
	}


	public static function getImageUrlFromFront($key, $defaultUrl){
		$one = AppSetting::find()
            ->where(['setting_name' => $key])
            ->one();
		if($one != null){
			//$res = '<img src="' . '../..' . '/frontend/web/images/app_setting/' . $one->value . '" class="img-responsive">';
			$res =  '@web/images/app_setting/' . $one->value;

			return $res;
		}else{
			return "";
		}
	}
	
	public static function getValueByKeyAndParam($key, $defaultValue=""){
		$one = AppSetting::find()
                ->where(['setting_name' => $key])
                ->one();
		if($one != null){
			$result = $one->value;
			
			//1. {TAHUN} diganti dengan TAHUN Pertama
			$result = str_replace("{TAHUN}", date("Y"),$result);

			return $result;
		}else{
			if($defaultValue != ""){
				return $defaultValue;
			}
		}
		
		return "--";
	}
}
