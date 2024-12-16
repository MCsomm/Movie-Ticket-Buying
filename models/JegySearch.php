<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Jegy;

/**
 * JegySearch represents the model behind the search form of `app\models\Jegy`.
 */
class JegySearch extends Jegy
{
    public $date;  // Add date filter if needed
    public $title; // Add title filter for the movie title

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'eloadas_id', 'seat_row', 'seat_number'], 'integer'],
            [['customer_name', 'customer_email', 'customer_phone', 'created_at', 'updated_at', 'date', 'title', 'seat'], 'safe'],
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
        $query = Jegy::find()->joinWith(['eloadas']); // Join with `eloadas` to access title and other attributes

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'jegy.id' => $this->id,
            'eloadas_id' => $this->eloadas_id,
            'seat_row' => $this->seat_row,
            'seat_number' => $this->seat_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_email', $this->customer_email])
            ->andFilterWhere(['like', 'customer_phone', $this->customer_phone])
            ->andFilterWhere(['like', 'seat', $this->seat]);

        if ($this->date) {
            $query->andFilterWhere(['DATE(eloadas.start_time)' => $this->date]);
        }

        // Filtering by title
        if ($this->title) {
            $query->andFilterWhere(['like', 'eloadas.title', $this->title]);
        }

        return $dataProvider;
    }
}