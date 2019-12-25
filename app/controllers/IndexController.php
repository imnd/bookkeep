<?php
namespace app\controllers;

use 
    tachyon\Controller,
    tachyon\Config,
    tachyon\traits\AuthActions,
    tachyon\components\Flash,
    tachyon\Request,
    app\models\Users
;

/**
 * Контроллер начальной страницы
 * 
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 */ 
class IndexController extends Controller
{
    use AuthActions;

    /**
     * @var Config $config
     */
    protected $config;
    /**
     * @var Users
     */
    protected $users;
    /**
     * @var Flash
     */
    protected $flash;

    /**
     * @param Config $config
     * @param Users $users
     * @param Flash $flash
     * @param array $params
     */
    public function __construct(Config $config, Users $users, Flash $flash, ...$params)
    {
        $this->config = $config;
        $this->users = $users;
        $this->flash = $flash;

        parent::__construct(...$params);
    }

    /**
     * Главная страница
     */
    public function index()
    {
        $this->view();
    }

    /**
     * Страница логина
     */
    public function login()
    {
        if (!Request::isPost()) {
            $this->view('login');
            return;            
        }
        if (!$user = $this->users->findByPassword([
            'username' => $this->post['username'],
            'password' => $this->post['password'],
        ])) {
            $this->unauthorised('Пользователя с таким логином и паролем нет.');
        }
        if ($user->confirmed != Users::STATUS_CONFIRMED) {
            $this->unauthorised('Вы не подтвердили свою регистрацию.');
        }
        $this->_login($this->post['remember']);

        $this->redirect(Request::getReferer());
    }

    /**
     * Страница логаута
     */
    public function logout()
    {
        $this->_logout();
        $this->redirect('/index');
    }

    /**
     * Страница регистрации
     */
    public function register()
    {
        if (Request::isPost()) {
            if ($user = $this->users->add(array(
                'username' => $this->post['username'],
                'email' => $this->post['email'],
                'password' => $this->post['password'],
                'password_confirm' => $this->post['password_confirm'],
            ))) {
                if (!$user->hasErrors()) {
                    $msg = 'Пожалуйста подтвердите свою регистрацию';
                    $email = $user->email;
                    $activationUrl = "{$_SERVER['HTTP_ORIGIN']}/index/activate?confirm_code={$user->confirm_code}&email=$email";
                    mail($email, 'Подтверждение регистрации', "$msg перейдя по ссылке: $activationUrl");

                    $msg .= ". На ваш почтовый ящик $email придет письмо со ссылкой подтверждения.";
                    $this->view('register-end', compact('msg'));
                    return;
                }
                $error = $user->getErrorsSummary();
            }
        }
        $this->view('register', compact('msg', 'error'));
    }

    /**
     * Страница подтверждения регистрации
     */
    public function activate()
    {
        if (
                $user = $this->users->findOne(array(
                    'email' => $this->get['email'],
                ))
            and $user->confirm_code===$this->get['confirm_code']
        ) {
            $user->setAttribute('confirmed', Users::STATUS_CONFIRMED);
            $user->update();
            $msg = 'Регистрация прошла успешно.';
        } else {
            $error = 'Неправильная ссылка.';
        }
        $this->view('register-end', compact('msg', 'error'));
    }
}
