<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php foreach ($result as $key => $value): ?>
  <?php $this->title =  Html::Encode($key).'|Category'; ?>
  <?php $this->params['breadcrumbs'][] = $key; ?>
  <div class="panel panel-default">
    <div class="panel-heading"><?= Html::Encode($key)?></div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <b>Subject</b>
        </div>
        <div class="col-md-2">
          <b>Total posts</b>
        </div>
        <div class="col-md-4">
          <b>Last post</b>
        </div>
        <div class="col-md-12"><hr></div>
      <?php foreach ($result[$key]['content'] as $k => $v): ?>
          <div class="col-md-6">
            <span class="glyphicon glyphicon-comment"></span> <a href="<?= Url::base(true) ."/index.php?r=site/subject&subjectId=". $result[$key]['content'][$k]['id']?>"><?= Html::Encode($k)?></a>
          </div>
          <div class="col-md-2">
            <?= Html::Encode($result[$key]['content'][$k]['count'])?>
          </div>
          <div class="col-md-4">
            <?= $result[$key]['content'][$k]['count'] ? "SOMEONE" : "-" ?>
          </div>
        <div class="col-md-12"><hr></div>
      <?php endforeach; ?>
    </div>
    </div>
  </div>
<?php endforeach; ?>
