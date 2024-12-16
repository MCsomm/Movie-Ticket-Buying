<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Eloadas $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="eloadas-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?> <!-- This will display all validation errors -->

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <!-- Date Picker for Show Date -->
    <?= $form->field($model, 'show_date')->input('date') ?>

    <!-- Dropdown for Start Time (Hours and Minutes) -->
    <div class="form-group">
        <label>Start Time</label>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'start_hour')->dropDownList(
                    ArrayHelper::map(range(8, 20), fn($value) => $value, fn($value) => str_pad($value, 2, '0', STR_PAD_LEFT)),
                    ['prompt' => 'Select Hour']
                )->label(false) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'start_minute')->dropDownList(
                    [
                        '00' => '00',
                        '15' => '15',
                        '30' => '30',
                        '45' => '45',
                    ],
                    ['prompt' => 'Select Minute']
                )->label(false) ?>
            </div>
        </div>
    </div>

    <!-- Input for Duration (in Minutes) -->
    <?= $form->field($model, 'duration_minutes')->textInput(['type' => 'number', 'min' => 30])->label('Duration (in Minutes)') ?>

    <?= $form->field($model, 'ticket_price')->textInput(['type' => 'number', 'min' => 1]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
