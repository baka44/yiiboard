<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
?>

<h1>Countries</h1>
<p>items per page: </p>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'userLimit')->dropDownList(['1' => 1, '2' => 2,'3' => 3]); ?>
<div class="form-group">
  <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
<?= $model->userLimit ?>

<ul>
  <?php foreach ($countries as $country): ?>
    <li>
      <?= Html::encode("{$country->code} {$country->name}")?>
      <?= $country->population ?>
    </li>
  <?php endforeach; ?>
</ul>

<?= LinkPager::Widget(['pagination' => $pagination])?>
