<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model {
    public $username;
    public $password;

    public function rules() {
        return [
            [['username', 'password'], 'required'],
            ['username', 'unique', 'targetClass' => Users::className(),  'message' => 'This username already taken'],
            ];
    }

    public function signup() {

    }
}
