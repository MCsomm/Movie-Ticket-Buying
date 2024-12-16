<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jegy".
 *
 * @property int $id
 * @property int $eloadas_id
 * @property int $seat_row
 * @property int $seat_number
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Eloadas $eloadas
 */
class Jegy extends \yii\db\ActiveRecord
{
    /**
     * @var mixed|string|null
     *

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jegy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eloadas_id', 'seat_row', 'seat_number', 'customer_name', 'customer_email', 'customer_phone'], 'required'],
            [['eloadas_id', 'seat_row'], 'integer'],
            [['seat_number'], 'string', 'max' => 1], // Adjust max length as needed
            [['customer_name'], 'string', 'max' => 255],
            [['customer_email'], 'email'],
            [['customer_phone'], 'string', 'max' => 20],
            [['seat'], 'string', 'max' => 10],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eloadas_id' => 'Eloadas ID',
            'seat_row' => 'Seat Row',
            'seat_number' => 'Seat Number',
            'seat' => 'Seat', // Add this line
            'customer_name' => 'Customer Name',
            'customer_email' => 'Customer Email',
            'customer_phone' => 'Customer Phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Eloadas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEloadas()
    {
        return $this->hasOne(Eloadas::class, ['id' => 'eloadas_id']);
    }
}
