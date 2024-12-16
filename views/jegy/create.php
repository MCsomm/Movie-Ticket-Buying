<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Jegy $model */

$this->title = 'Create Jegy';
$this->params['breadcrumbs'][] = ['label' => 'Jegies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jegy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
