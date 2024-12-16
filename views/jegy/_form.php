<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Jegy $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="jegy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'eloadas_id')->textInput() ?>

    <?= $form->field($model, 'seat_row')->textInput() ?>

    <?= $form->field($model, 'seat_number')->textInput() ?>

    <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
