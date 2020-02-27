<?php
use yii\helpers\Html;
 ?>
<p> Data that you provided: </p>
<ul>
  <li><lable>name</lable>: <?= Html::encode($model->name) ?> </li>
  <li><lable>email</lable>: <?= Html::encode($model->email) ?></li>
</ul>
