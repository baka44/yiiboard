<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Users;
use app\models\Posts;

?>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="row">
      <div class="col-md-6">
        <b>Thread</b>
      </div>
      <div class="col-md-2">
        <b>Date</b>
      </div>
      <div class="col-md-2">
        <b>Author</b>
      </div>
      <div class="col-md-2">
        <b>Replies</b>
      </div>
      <div class="col-md-12"><hr></div>
<?php foreach ($result as $key => $value): ?>
  <?php $this->title =  Html::Encode($key).'|Subject'; ?>
  <?php $this->params['breadcrumbs'][] = ['label' => $result[$key]['category']['name'], 'url' => [ 'site/category', 'categoryId' => $result[$key]['category']['id'] ] ]; ?>
  <?php $this->params['breadcrumbs'][] = $key; ?>
  <?php if (isset($result[$key]['post'])) : ?>
  <?php foreach ($result[$key]['post'] as $postId => $postBody): ?>
      <div class="col-md-6">
        <span class="glyphicon glyphicon-tag"></span> <a href="/index.php?r=site/post&postId=<?= $postId ?>"><?= $postBody['subject'] ?></a>
      </div>
      <div class="col-md-2">
        <?= $postBody['post_time'] ?>
      </div>
      <div class="col-md-2">
        <?= Users::findIdentity($postBody['user_id'])->username ?>
      </div>
      <div class="col-md-2">
        <?= Posts::find()->where(['reply_to_post_id' => $postId ])->count() ?>
      </div>
      <div class="col-md-12"><hr></div>
  <?php endforeach; ?>
  <?php else: ?>
  <p class="text-center">There is no any posts in this subject yet. Try to <a href="#">create</a> one.</p>
  <?php endif; ?>
  <?php endforeach; ?>
  </div>
</div>
</div>
