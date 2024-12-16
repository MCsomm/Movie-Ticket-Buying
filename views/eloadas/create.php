<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Eloadas $model */

$this->title = 'Create Eloadas';
$this->params['breadcrumbs'][] = ['label' => 'Eloadas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eloadas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
