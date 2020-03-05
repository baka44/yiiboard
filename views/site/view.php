<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'View';
?>
<?php foreach ($result as $key => $value): ?>
  <?php $this->params['breadcrumbs'][] = $key; ?>
  <div class="panel panel-default">
    <div class="panel-heading"><a href="<?= Url::base(true) ."/index.php?r=site/view&category=". $result[$key]['id']?>"><?= Html::Encode($key)?></a></div>
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
      <?php if (isset($result[$key]['content'])): ?>
      <?php foreach ($result[$key]['content'] as $k => $v): ?>
        <?php if ($type == 'subject'){ $this->params['breadcrumbs'][] = $k; } ?>
          <div class="col-md-6">
            <span class="glyphicon glyphicon-comment"></span> <a href="<?= Url::base(true) ."/index.php?r=site/view&subject=". $result[$key]['content'][$k]['id']?>"><?= Html::Encode($k)?></a>
          </div>
          <div class="col-md-2">
            <?= Html::Encode($result[$key]['content'][$k]['count'])?>
          </div>
          <div class="col-md-4">
            <?= $result[$key]['content'][$k]['count'] ? "SOMEONE" : "-" ?>
          </div>
        <div class="col-md-12"><hr></div>
      <?php endforeach; ?>
    <?php endif; ?>
    </div>
    </div>
  </div>
<?php endforeach; ?>
