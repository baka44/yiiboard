<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Users;
<<<<<<< HEAD
use app\models\Posts;
=======
>>>>>>> 21f50476c0f24747292083aeb1d1582306884a08

$this->title =  Html::Encode($result['post']['subject']).'|Post';
$this->params['breadcrumbs'][] = ['label' => $result['category']['name'], 'url' => [ 'site/category', 'categoryId' => $result['category']['id'] ] ];
$this->params['breadcrumbs'][] = ['label' => $result['subject']['name'], 'url' => [ 'site/subject', 'subjectId' => $result['subject']['id'] ] ];
$this->params['breadcrumbs'][] = $result['post']['subject'];
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="row">
<<<<<<< HEAD
      <div class="col-md-2">
        <b><?= Users::findIdentity($result['post']['user_id'])->username ?></b>
=======
      <div class="col-md-2 text-center">
        <b class="text-center"><?= Users::findIdentity($result['post']['user_id'])->username ?></b>
>>>>>>> 21f50476c0f24747292083aeb1d1582306884a08
      </div>
      <div class="col-md-9">
        <span>Posted: <?= $result['post']['post_time'] ?></span>
      </div>
      <div class="col-md-1">
        <a href="#"><span>Sahre</span> <span class="glyphicon glyphicon-share-alt"></span></a>
      </div>
    </div>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-2">
<<<<<<< HEAD
        <p>user_avatar.jpg</p>
        <p>USER_RANK</p>
        <p>Group: <?= Users::findIdentity($result['post']['user_id'])->role ?></p>
        <p><?= Posts::find()->where(['user_id' => $result['post']['user_id'] ])->count() ?> posts</p>
=======
        <?= $this->render('blocks/postProfile', ['userId' => $result['post']['user_id'] ]); ?>
>>>>>>> 21f50476c0f24747292083aeb1d1582306884a08
      </div>
      <div class="col-md-10">
        <p><?= Html::Encode($result['post']['body']) ?></p>
      </div>
    </div>
  </div>
  </div>
<?php if (isset($result['reply'])): ?>
<?php foreach ($result['reply'] as $replyID => $reply): ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="row">
<<<<<<< HEAD
        <div class="col-md-2">
=======
        <div class="col-md-2 text-center">
>>>>>>> 21f50476c0f24747292083aeb1d1582306884a08
          <b><?= Users::findIdentity($reply['user_id'])->username ?></b>
        </div>
        <div class="col-md-9">
          <span>Posted: <?= $reply['post_time'] ?></span>
        </div>
        <div class="col-md-1">
          <a href="#"><span>Sahre</span> <span class="glyphicon glyphicon-share-alt"></span></a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-2">
<<<<<<< HEAD
          <p>user_avatar.jpg</p>
          <p>USER_RANK</p>
          <p>Group: <?= Users::findIdentity($reply['user_id'])->role ?></p>
          <p><?= Posts::find()->where(['user_id' => $reply['user_id'] ])->count() ?> posts</p>
=======
          <?= $this->render('blocks/postProfile', ['userId' => $reply['user_id'] ]); ?>
>>>>>>> 21f50476c0f24747292083aeb1d1582306884a08
        </div>
        <div class="col-md-10">
          <p><?= Html::Encode($reply['body']) ?></p>
        </div>
      </div>
    </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
