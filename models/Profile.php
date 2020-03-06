<?php
namespace app\models;

use Yii;
use yii\base\Model;

class Profile extends Model {
  public $user;
  public function renderData() {
    return [ 'username' => $this->user->identity->username,'id' => $this->user->id];
  }
}
