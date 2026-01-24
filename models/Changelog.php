<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * @property int $id
 * @property string $model_class
 * @property string $model_id
 * @property int|null $changed_by
 * @property string $changed_at
 * @property array $changes
 */
class Changelog extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%changelog}}';
    }

    public function rules(): array
    {
        return [
            [['model_class', 'model_id'], 'required'],
            [['changed_by'], 'integer'],
            [['changed_at'], 'safe'],
            [['model_class', 'model_id'], 'string', 'max' => 255],
            [['changes'], 'safe'],
        ];
    }

    public function afterFind(): void
    {
        parent::afterFind();
        if (is_string($this->changes)) {
            $this->changes = Json::decode($this->changes);
        }
    }

    public function getChangedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'changed_by']);
    }
}
