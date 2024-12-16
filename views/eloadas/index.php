<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dailyDataProvider */
/** @var yii\data\ActiveDataProvider $allDataProvider */
/** @var yii\data\ActiveDataProvider $soldTicketsDataProvider */
/** @var app\models\JegySearch $searchModel */

$this->title = 'Vetítések Áttekintése';


?>

<h1><?= Html::encode($this->title) ?></h1>

<!-- Create Screening Button -->
<p>
    <?= Html::a('Vetítés létrehozása', ['create'], ['class' => 'btn btn-success']) ?>
</p>

<!-- Daily Screenings Section -->
<h2>Napi Vetítések</h2>
<?= GridView::widget([
    'dataProvider' => $dailyDataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'title',
            'label' => 'Film címe',
            'value' => function ($model) {
                return Html::encode($model->title);
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'show_date',
            'label' => 'Dátum',
        ],
        [
            'attribute' => 'start_time',
            'label' => 'Kezdés időpontja',
            'value' => function ($model) {
                return date('H:i', strtotime($model->start_time));
            },
        ],
        [
            'attribute' => 'end_time',
            'label' => 'Befejezés időpontja',
            'value' => function ($model) {
                return date('H:i', strtotime($model->end_time));
            },
        ],
        [
            'attribute' => 'ticket_price',
            'label' => 'Jegy ára (EUR)',
            'value' => function ($model) {
                return Html::encode($model->ticket_price) . ' EUR';
            },
            'format' => 'raw',
        ],
        [
            'label' => 'Eladott jegyek',
            'value' => function ($model) {
                return Html::encode($model->getReservedSeatsCount() . '/40');
            },
            'format' => 'raw',
        ],
        [
            'label' => 'Admini lehetőségek',
            'format' => 'raw',
            'value' => function ($model) {
                $buttons = '';
                if ($model->getReservedSeatsCount() == 0) {
                    $buttons .= Html::a('Módosítás', ['eloadas/update', 'id' => $model->id], ['class' => 'btn btn-warning']) . ' ';
                    $buttons .= Html::a('Törlés', ['eloadas/delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Biztosan törli ezt a vetítést?',
                            'method' => 'post',
                        ],
                    ]);
                }
                return $buttons;
            },
        ],
        [
            'label' => 'Admin Nézet',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a('Admin Nézet', ['eloadas/admin-view', 'id' => $model->id], ['class' => 'btn btn-primary']);
            },
        ],
    ],
]); ?>

<!-- All Screenings Section -->
<h2>Összes Vetítés</h2>
<?= GridView::widget([
    'dataProvider' => $allDataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'title',
            'label' => 'Film címe',
            'value' => function ($model) {
                return Html::encode($model->title);
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'show_date',
            'label' => 'Dátum',
        ],
        [
            'attribute' => 'start_time',
            'label' => 'Kezdés időpontja',
            'value' => function ($model) {
                return date('H:i', strtotime($model->start_time));
            },
        ],
        [
            'attribute' => 'end_time',
            'label' => 'Befejezés időpontja',
            'value' => function ($model) {
                return date('H:i', strtotime($model->end_time));
            },
        ],
        [
            'attribute' => 'ticket_price',
            'label' => 'Jegy ára (EUR)',
            'value' => function ($model) {
                return Html::encode($model->ticket_price) . ' EUR';
            },
            'format' => 'raw',
        ],
        [
            'label' => 'Eladott jegyek',
            'value' => function ($model) {
                return Html::encode($model->getReservedSeatsCount() . '/40');
            },
            'format' => 'raw',
        ],
        [
            'label' => 'Admini lehetőségek',
            'format' => 'raw',
            'value' => function ($model) {
                $buttons = '';
                if ($model->getReservedSeatsCount() == 0) {
                    $buttons .= Html::a('Módosítás', ['eloadas/update', 'id' => $model->id], ['class' => 'btn btn-warning']) . ' ';
                    $buttons .= Html::a('Törlés', ['eloadas/delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Biztosan törli ezt a vetítést?',
                            'method' => 'post',
                        ],
                    ]);
                }
                return $buttons;
            },
        ],
        [
            'label' => 'Admin Nézet',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a('Admin Nézet', ['eloadas/admin-view', 'id' => $model->id], ['class' => 'btn btn-primary']);
            },
        ],
    ],
]); ?>

<!-- Filters Section for Sold Tickets -->
<h2>Eladott Jegyek</h2>

<div class="filter-section">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['eloadas/index'],
        'options' => ['class' => 'form-inline filter-container'], // Makes the filters inline and compact
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($searchModel, 'eloadas_id')->textInput(['placeholder' => 'Film címe', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($searchModel, 'customer_name')->textInput(['placeholder' => 'Vevő neve', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($searchModel, 'customer_phone')->textInput(['placeholder' => 'Telefon', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($searchModel, 'customer_email')->textInput(['placeholder' => 'Email', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($searchModel, 'seat')->textInput(['placeholder' => 'Szék', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($searchModel, 'date')->input('date', ['class' => 'form-control'])->label(false) ?>
        </div>
        <div class="col-md-4 button-container">
            <?= Html::submitButton('Keresés', ['class' => 'btn btn-primary mr-2']) ?>
            <?= Html::resetButton('Visszaállítás', ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Sold Tickets Section -->
<?= GridView::widget([
    'dataProvider' => $soldTicketsDataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        // Movie Title Column
        [
            'attribute' => 'eloadas.title',
            'label' => 'Film címe',
            'value' => function ($model) {
                return $model->eloadas ? Html::encode($model->eloadas->title) : 'N/A';
            },
            'format' => 'raw',
        ],

        // Date Column
        [
            'attribute' => 'eloadas.show_date',
            'label' => 'Dátum',
            'value' => function ($model) {
                return $model->eloadas ? $model->eloadas->show_date : 'N/A';
            },
        ],

        // Start Time Column
        [
            'attribute' => 'eloadas.start_time',
            'label' => 'Kezdés időpontja',
            'value' => function ($model) {
                return $model->eloadas ? date('H:i', strtotime($model->eloadas->start_time)) : 'N/A';
            },
        ],

        // End Time Column
        [
            'attribute' => 'eloadas.end_time',
            'label' => 'Befejezés időpontja',
            'value' => function ($model) {
                return $model->eloadas ? date('H:i', strtotime($model->eloadas->end_time)) : 'N/A';
            },
        ],

        // Ticket Price Column
        [
            'attribute' => 'eloadas.ticket_price',
            'label' => 'Jegy ára (EUR)',
            'value' => function ($model) {
                return $model->eloadas ? Html::encode($model->eloadas->ticket_price) . ' EUR' : 'N/A';
            },
            'format' => 'raw',
        ],

        // Seat Row Column
        [
            'attribute' => 'seat_row',
            'label' => 'Sor',
            'value' => function ($model) {
                return Html::encode($model->seat_row);
            },
        ],

        // Seat Number Column
        [
            'attribute' => 'seat_number',
            'label' => 'Betű',
            'value' => function ($model) {
                return Html::encode($model->seat_number);
            },
        ],

        // Seat Column (Combined)
        [
            'attribute' => 'seat',
            'label' => 'Ülőhely',
            'value' => function ($model) {
                return Html::encode($model->seat);
            },
        ],

        // Customer Name Column
        [
            'attribute' => 'customer_name',
            'label' => 'Vevő neve',
            'value' => function ($model) {
                return Html::encode($model->customer_name);
            },
        ],

        // Customer Phone Column
        [
            'attribute' => 'customer_phone',
            'label' => 'Vevő telefonja',
            'value' => function ($model) {
                return Html::encode($model->customer_phone);
            },
        ],

        // Customer Email Column
        [
            'attribute' => 'customer_email',
            'label' => 'Vevő email címe',
            'value' => function ($model) {
                return Html::encode($model->customer_email);
            },
        ],
    ],
]); ?>
