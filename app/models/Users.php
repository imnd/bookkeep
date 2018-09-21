<?php
namespace app\models;

/**
 * Модель пользователей
 * 
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 */ 
class Users extends \tachyon\db\models\ArModel
{
    const STATUS_NOTCONFIRMED = 0;
    const STATUS_CONFIRMED = 1;

    public static $tableName = 'users';
    public static $primKey = 'id';
    public static $fields = array('username', 'email', 'password_hash', 'confirmed', 'confirm_code');
    protected static $attributeNames = array(
        'username' => 'Логин',
        'email' => 'Email',
        'password' => 'Пароль',
        'confirmed' => 'Подтвержден',
        'confirm_code' => 'Код подтверждения',
    );
    /**
     * соль для шифровки пароля
     * @var string $salt
     */
    protected static $salt = 'cjwgu-k6837hfka--eifjo3ji34bceb76ta2vdu';

    /**
     * Извлекает пользователя
     * 
     * @param array $attributes
     * @return integer
     */
    public function find($attributes)
    {
        $attributes['password_hash'] = $this->hashPassword($attributes['password']);
        unset($attributes['password']);
        return $this->findOneByAttrs($attributes);
    }

    /**
     * Добавляет нового пользователя
     * 
     * @param array $attributes
     * @return integer
     */
    public function add($attributes)
    {
        if ($this->findOneByAttrs(array(
            'username' => $attributes['username'],
        ))) {
            $this->addError('username', "Пользователь {$attributes['username']} уже существует");
        }
        if ($this->findOneByAttrs(array(
            'email' => $attributes['email'],
        ))) {
            $this->addError('email', "Пользователь с email {$attributes['email']} уже существует");
        }
        if (empty($attributes['password'])) {
            $this->addError('password', "Пароль обязателен.");
        }
        if ($attributes['password']!==$attributes['password_confirm']) {
            $this->addError('password', "Пароли должны совпадать.");
        }
        if ($this->hasErrors()) {
            return $this;
        }
        $attributes['password_hash'] = $this->hashPassword($attributes['password']);
        unset($attributes['password']);
        $attributes['confirm_code'] = $this->hashPassword(time());
        $this->setAttributes($attributes);

        $this->insert();
        return $this;
    }

    public function hashPassword($text)
    {
        return hash('md5', $text . self::$salt);
    }
}
