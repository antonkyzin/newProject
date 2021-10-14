<?php

namespace Controllers;

use Models\UserModel;
use Models\ViewModel;

class UserController
{
    private $userModel;
    private $viewModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->viewModel = new ViewModel();
    }

    public function loginAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $result = $this->userModel->login($login, $password);
            switch ($result) {
                case 1 :
                    $this->viewModel->location('/');
                    break;
                case 2 :
                    $errMsg = 'Мама пока не дала разрешение на вход!';
                    $this->viewModel->render(null, $errMsg);
                    break;
                case 0 :
                    $errMsg = 'Неверный логин или пароль!';
                    $this->viewModel->render(null, $errMsg);
            }
        } else {
            $errMsg = 'Простите, но что-то пошло не так. Попробуйте еще раз';
            $this->viewModel->render(null, $errMsg);
        }
    }

    public function logoutAction()
    {
        session_destroy();
        $this->viewModel->location('/');
    }

    public function registrationAction($errMsg = null)
    {
        $isSigned = $this->userModel->isSigned();
        if (!$isSigned) {
            $options = [
                'title' => 'Регистрация',
                'content' => 'registration.phtml'
            ];
            $this->viewModel->render($options, $errMsg);
        } else {
            $this->viewModel->location('/');
        }
    }

    public function newAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->userModel->newUser($_POST);
        }
        if ($result === 'exist') {
            $this->registrationAction('Пользователь с таким логином уже зарегистрирован.');
        } elseif ($result) {
            $this->registrationAction('Вы успешно зарегистрировались. Ожидайте подтверждения от мамы.');
        } else {
            $this->registrationAction('Произошла ошибка. Вы ввели некорректные данные.');
        }
    }

    public function allUsersAction()
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $allUsers = $this->userModel->getAllUsers();
            $options = [
                'title' => 'Список пользователей',
                'content' => 'all_users.phtml',
                'allUsers' => $allUsers
            ];
            $this->viewModel->render($options);
        } else {
            $this->viewModel->location('/');
        }
    }

    public function deleteAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->userModel->deleteUser($_POST);
            if ($result) {
                $this->viewModel->location('/user/allUsers');
            }
        }
    }

    public function updateUserAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->userModel->updateUser($_POST);
            if ($result) {
                $this->viewModel->location('/user/allUsers');
            }
        }
    }

    public function approveUserAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->userModel->approveUser($_POST);
            if ($result) {
                $this->viewModel->location('/user/allUsers');
            }
        }
    }

    public function avatarFormAction($errMsg = null)
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $options = [
                'title' => 'Аватар',
                'content' => 'change_avatar.phtml'
            ];
            $this->viewModel->render($options, $errMsg);
        } else {
            $this->viewModel->location('/');
        }
    }

    public function changeAvatarAction()
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $result = $this->userModel->changeAvatar();
            if ($result) {
                $this->viewModel->location('/user/avatarForm');
            } else {
                $errMsg = 'Произошла ошибка';
                $this->avatarFormAction($errMsg);
            }
        } else {
            $this->viewModel->location('/');
        }
    }

    public function deleteAvatarAction()
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $result = $this->userModel->deleteAvatar();
            if ($result) {
                $errMsg = 'Аватар успешно удалён';
                $this->avatarFormAction($errMsg);
            }
        } else {
            $this->viewModel->location('/');
        }
    }
}
