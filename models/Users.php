<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Users extends ActiveRecord implements IdentityInterface {
    //public $id;
    //public $username;
    //public $password;
    public $auth_key;
    //public $token;

    public static function tableName(){
       return 'users';
   }

    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    public function getAuthKey() {
       return $this->auth_key;

    }
    public function validateAuthKey($authKey) {
       return $this->getAuthKey() === $authKey;
    }

    public static function findIdentity($id) {
        return self::findOne(array('id'=>$id));
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    public static function findByUsername($username) {
        $userdata = Users::findOne(['username' => $username]);
            if (isset($userdata)) {
                return new static($userdata);
            }

        return null;
    }

    public function getId() {
        return $this->id;
    }

    public function validatePassword($password) {
        return $this->password === $password;
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }
}
