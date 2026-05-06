<?php
// app/src/Controllers/AuthController.php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;

/**
 * Контроллер авторизации и регистрации пользователей.
 * * Обрабатывает процессы регистрации новых клиентов, аутентификации
 * существующих пользователей и корректного завершения сессии (выхода из системы).
 */
class AuthController extends BaseController {
    
    /**
     * Модель для работы с данными пользователей.
     * @var User
     */
    private User $userModel;

    /**
     * Конструктор контроллера.
     * * Инициализирует модель пользователя для последующей проверки учетных данных.
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = new User($this->db);
    }

    /**
     * Обрабатывает отображение формы регистрации и её отправку.
     * * Выполняет комплексную валидацию почты, имени, паролей и маски телефона.
     * Если в базе данных еще нет пользователей, первому зарегистрировавшемуся
     * автоматически присваивается роль 'admin'.
     * * @return void
     */
    public function register(): void {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $errors = [];
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            $old = compact('email', 'fullName', 'phone');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Укажите корректный Email.';
            } elseif ($this->userModel->findByEmail($email)) {
                $errors[] = 'Пользователь с таким Email уже зарегистрирован.';
            }

            if (empty($fullName) || mb_strlen($fullName) < 2) {
                $errors[] = 'ФИО должно содержать не менее 2 символов.';
            }

            $phoneRegex = '/^\+?[0-9]{1,4}?[-.\s]?\(?[0-9]{1,3}?\)?[-.\s]?[0-9]{1,4}[-.\s]?[0-9]{1,4}[-.\s]?[0-9]{1,9}$/';
            if (empty($phone)) {
                $errors[] = 'Укажите номер телефона.';
            } elseif (!preg_match($phoneRegex, $phone)) {
                $errors[] = 'Неверный формат номера телефона. Используйте формат: +7 (999) 123-45-67 или аналогичный.';
            }

            if (strlen($password) < 6) {
                $errors[] = 'Пароль должен быть не менее 6 символов.';
            } elseif ($password !== $passwordConfirm) {
                $errors[] = 'Пароли не совпадают.';
            }

            if (empty($errors)) {
                $role = 'user';
                $stmt = $this->db->query("SELECT COUNT(*) FROM users");
                if ($stmt->fetchColumn() == 0) {
                    $role = 'admin';
                }

                $success = $this->userModel->create($email, $password, $fullName, $phone, $role);

                if ($success) {
                    header('Location: /login?registered=1');
                    exit;
                } else {
                    $errors[] = 'Произошла ошибка при сохранении. Попробуйте позже.';
                }
            }
        }

        $this->render('auth/register.html.twig', [
            'errors' => $errors,
            'old' => $old
        ]);
    }

    /**
     * Обрабатывает аутентификацию пользователя в системе.
     * * Проверяет введенную пару логин/пароль с использованием безопасного
     * алгоритма хеширования, инициализирует сессию и перенаправляет на главную страницу.
     * * @return void
     */
    public function login(): void {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $errors = [];
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $errors[] = 'Заполните все поля.';
            } else {
                $user = $this->userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];

                    header('Location: /');
                    exit;
                } else {
                    $errors[] = 'Неверный Email или пароль.';
                }
            }
        }

        $this->render('auth/login.html.twig', [
            'errors' => $errors,
            'email' => $email,
            'registered' => $_GET['registered'] ?? null
        ]);
    }

    /**
     * Осуществляет выход пользователя из системы.
     * * Полностью очищает массив $_SESSION, деактивирует и удаляет куки сессии
     * на клиенте, после чего уничтожает сессию на сервере.
     * * @return void
     */
    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: /');
        exit;
    }
}