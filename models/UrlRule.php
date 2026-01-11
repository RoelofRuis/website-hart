<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $source_url
 * @property string $target_url
 * @property int $hit_counter
 */
class UrlRule extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%url_rule}}';
    }

    public function rules()
    {
        return [
            [['source_url', 'target_url'], 'required'],
            [['source_url', 'target_url'], 'string', 'max' => 500],
            [['hit_counter'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'source_url' => Yii::t('app', 'Source URL'),
            'target_url' => Yii::t('app', 'Target URL'),
            'hit_counter' => Yii::t('app', 'Times Hit'),
        ];
    }
}