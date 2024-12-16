<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Jegy[] $bookedTickets */

$this->title = 'Vásárlás befejezés';
?>

<div class="booking-confirmation">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Köszönjük a vásárlást, az alábbi jegyeket választotta:</p>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Film Címe</th>
            <th>Választott Ülőhely</th>
            <th>Vásárló Neve</th>
            <th>Vásárló Email Címe</th>
            <th>Vásárló Telefonszáma</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bookedTickets as $ticket): ?>
            <tr>
                <td><?= Html::encode($ticket->eloadas->title) ?></td>
                <td><?= Html::encode($ticket->seat) ?></td>
                <td><?= Html::encode($ticket->customer_name) ?></td>
                <td><?= Html::encode($ticket->customer_email) ?></td>
                <td><?= Html::encode($ticket->customer_phone) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p>Bármi kérdése lenne keresse, kollégáinkat valamerre.</p>

    <?= Html::a('Vissza a kezdőoldalra', ['/site/index'], ['class' => 'btn btn-primary']) ?>
</div>