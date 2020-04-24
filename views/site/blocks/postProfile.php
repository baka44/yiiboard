<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Users;
use app\models\Posts;
?>
<style>
.user-avatar .img-responsive {
    margin: 0 auto;
}
</style>
<div class="col-md-12">
<img src="/img/default/no-avatar.png" class="user-avatar img-responsive">
<br>
<p class="text-center">Group: <?= Users::findIdentity($userId)->role ?></p>
<p class="text-center"><?= Posts::find()->where(['user_id' => $userId ])->count() ?> posts</p>
</div>
