<?php
namespace app\models;
use yii\base\Model;

Class ListSelect extends Model{
  public $userLimit;

  public function rules(){
    return
        [['userLimit','number']];
  }
}
