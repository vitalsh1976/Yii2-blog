<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
// use app\models\ImageUpload;

/* @var $this yii\web\View */
/* @var $model app\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= Html::dropDownList('category', $selectedCategory, $categories,['class' => 'form-control']) ?>



    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-info']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>