<?php

namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Users extends ActiveRecord implements IdentityInterface{
    public $id;
    public $username;
    public $password;

    public static function findIdentityByAccessToken() {
    }
    public function getAuthKey() {
      return $this->auth_key;
    }
    public function validateAuthKey($authKey) {
      return $this->getAuthKey() === $authKey;
    }

    public static function findIdentity($id) {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    public static function findByUsername($username) {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    public function getId() {
        return $this->id;
    }

    public function validatePassword($password) {
        return $this->password === $password;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }
}
