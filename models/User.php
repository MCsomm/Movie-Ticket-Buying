<?php
namespace app\models;

use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\BaseObject;

class User extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;

    // Hardcoded admin credentials
    private static $admin = [
        'id' => '1000',
        'username' => 'admin',
        'password' => 'admin', // You can hash this password for security
        'authKey' => 'test100key',
    ];

    // Identity interface methods
    public static function findIdentity($id)
    {
        return $id === self::$admin['id'] ? new static(self::$admin) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // No token authentication
    }

    public static function findByUsername($username)
    {
        if ($username === self::$admin['username']) {
            return new static(self::$admin);
        }
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}


