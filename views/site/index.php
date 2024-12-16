<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\models\Eloadas;

/** @var yii\web\View $this */

$this->title = 'Lista a vetítésre váró filmekről';

// Define the current date and time
$currentDateTime = new DateTime(); // Using DateTime for easier manipulation

// Fetch all screenings, including those that are already over today
$query = Eloadas::find()
    ->where(['>=', 'show_date', date('Y-m-d')])
    ->orderBy(['show_date' => SORT_ASC, 'start_time' => SORT_ASC]);

// Create the data provider
$dataProvider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => ['pageSize' => 10], // Optional: Paginate results, 10 items per page
]);

?>

<div class="site-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // Film Title Column
            [
                'attribute' => 'title',
                'label' => 'Film Címe',
            ],

            // Show Date Column
            [
                'attribute' => 'show_date',
                'label' => 'Vetítés Dátuma',
            ],

            // Start Time Column
            [
                'attribute' => 'start_time',
                'label' => 'Kezdés Időpontja',
                'value' => function ($model) {
                    return date('H:i', strtotime($model->start_time));
                },
            ],

            // Duration in Hours
            [
                'label' => 'Vetítés Hossza',
                'value' => function ($model) {
                    return $model->calculateDurationInHours() . ' óra'; // Use the function to get duration in hours
                },
            ],

            // Ticket Price Column
            [
                'attribute' => 'ticket_price',
                'label' => 'Jegy Ára',
                'value' => function ($model) {
                    return $model->ticket_price . ' EUR';
                },
            ],

            // Book Now Button Column
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{book}', // Template for the 'book' button
                'buttons' => [
                    'book' => function ($url, $model, $key) {
                        // Get the current date and time
                        $currentDateTime = new DateTime();
                        // Construct the screening date and time object
                        $screeningDateTime = new DateTime($model->show_date . ' ' . $model->start_time);

                        // Calculate the difference between current time and screening time
                        $interval = $currentDateTime->diff($screeningDateTime);
                        $minutesUntilStart = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

                        // Display button only if screening is today but hasn't started yet, or is in the future
                        if ($screeningDateTime > $currentDateTime && $minutesUntilStart >= 60) {
                            return Html::a('Jegyvásárlás', ['jegy/book', 'id' => $model->id], ['class' => 'btn btn-primary']);
                        }

                        // Otherwise, return an empty string (no button)
                        return '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
