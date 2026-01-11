<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ContactMessageSearch extends ContactMessage
{
    public $q;

    public function rules(): array
    {
        return [
            [['q'], 'safe'],
        ];
    }

    public function search($params, $query = null)
    {
        if ($query === null) {
            $query = ContactMessage::find();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'created_at',
                    'type',
                ],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->q) {
            $query->andFilterWhere(['or',
                ['ilike', 'name', $this->q],
                ['ilike', 'email', $this->q],
                ['ilike', 'telephone', $this->q],
                ['ilike', 'message', $this->q],
            ]);
        }

        return $dataProvider;
    }
}
