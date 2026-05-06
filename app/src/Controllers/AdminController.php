<?php
// app/src/Controllers/AdminController.php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Appointment;
use App\Models\User;

/**
 * Контроллер для управления панелью администратора СТО AutoFix.
 * * Обеспечивает управление заявками на ремонт, пользователями системы
 * и каталогом доступных услуг автосервиса.
 */
class AdminController extends BaseController {
    
    /**
     * Модель для работы с записями на ремонт.
     * @var Appointment
     */
    private Appointment $appointmentModel;
    
    /**
     * Модель для работы с пользователями.
     * @var User
     */
    private User $userModel;

    /**
     * Конструктор контроллера.
     * * Инициализирует необходимые модели и выполняет строгую проверку прав доступа.
     * Если пользователь не авторизован или не имеет роли администратора,
     * выполнение прерывается с отправкой HTTP-статуса 403.
     */
    public function __construct() {
        parent::__construct();
        $this->appointmentModel = new Appointment($this->db);
        $this->userModel = new User($this->db);

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("HTTP/1.1 403 Forbidden");
            echo "<div style='text-align:center; margin-top:50px;'>
                    <h1>Доступ ограничен (403 Forbidden)</h1>
                    <p>У вас нет прав для просмотра этой страницы. <a href='/'>На главную</a></p>
                  </div>";
            exit;
        }
    }

    /**
     * Отображает главную страницу панели администратора.
     * * Передает в шаблон списки всех заявок, зарегистрированных пользователей,
     * полный каталог услуг (включая архивные) и статусы выполнения операций.
     * * @return void
     */
    public function index(): void {
        $appointments = $this->appointmentModel->getAll();
        $users = $this->userModel->getAll();
        $services = $this->db->query("SELECT * FROM services ORDER BY is_active DESC, category, name")->fetchAll();

        $this->render('admin/index.html.twig', [
            'appointments' => $appointments,
            'users' => $users,
            'services' => $services,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
        ]);
    }

    /**
     * Обрабатывает POST-запрос на изменение текущего статуса записи на ремонт.
     * * После обновления статуса выполняет перенаправление на страницу админ-панели.
     * * @return void
     */
    public function updateStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['appointment_id'] ?? 0);
            $status = $_POST['status'] ?? '';

            if ($id > 0 && !empty($status)) {
                $this->appointmentModel->updateStatus($id, $status);
            }
        }
        header('Location: /admin?success=status_updated');
        exit;
    }

    /**
     * Создает новую учетную запись с ролью администратора.
     * * Проверяет заполнение полей, уникальность Email в системе и
     * регистрирует нового администратора, возвращая соответствующий статус операции.
     * * @return void
     */
    public function createAdmin(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($fullName) || empty($phone) || strlen($password) < 6) {
                header('Location: /admin?error=invalid_admin_data');
                exit;
            }

            if ($this->userModel->findByEmail($email)) {
                header('Location: /admin?error=email_exists');
                exit;
            }

            $success = $this->userModel->create($email, $password, $fullName, $phone, 'admin');
            
            if ($success) {
                header('Location: /admin?success=admin_created');
            } else {
                header('Location: /admin?error=db_error');
            }
            exit;
        }
    }

    /**
     * Удаляет учетную запись пользователя из базы данных по его идентификатору.
     * * Предотвращает попытку удаления администратором собственной учетной записи.
     * * @return void
     */
    public function deleteUser(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['user_id'] ?? 0);

            if ($id === (int)$_SESSION['user_id']) {
                header('Location: /admin?error=cannot_delete_self');
                exit;
            }

            $success = $this->userModel->delete($id);
            if ($success) {
                header('Location: /admin?success=user_deleted');
            } else {
                header('Location: /admin?error=db_error');
            }
            exit;
        }
    }

    /**
     * Добавляет новую услугу в активный каталог автосервиса СТО.
     * * Валидирует переданные данные (название, категорию и положительную цену)
     * перед записью в базу данных.
     * * @return void
     */
    public function createService(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $description = trim($_POST['description'] ?? '');

            if (empty($name) || empty($category) || $price <= 0) {
                header('Location: /admin?error=invalid_service_data');
                exit;
            }

            $stmt = $this->db->prepare("
                INSERT INTO services (name, category, price, description) 
                VALUES (:name, :category, :price, :description)
            ");
            
            $success = $stmt->execute([
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'description' => $description
            ]);

            if ($success) {
                header('Location: /admin?success=service_created');
            } else {
                header('Location: /admin?error=db_error');
            }
            exit;
        }
    }

    /**
     * Реализует мягкое удаление (архивацию) услуги из каталога СТО.
     * * Вместо физического удаления строки из таблицы, устанавливает флаг активности `is_active` в 0.
     * Это сохраняет целостность истории записей клиентов в базе данных.
     * * @return void
     */
    public function deleteService(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['service_id'] ?? 0);

            if ($id > 0) {
                $stmt = $this->db->prepare("UPDATE services SET is_active = 0 WHERE id = :id");
                $success = $stmt->execute(['id' => $id]);

                if ($success) {
                    header('Location: /admin?success=service_deleted');
                } else {
                    header('Location: /admin?error=db_error');
                }
                exit;
            }
        }
        header('Location: /admin');
        exit;
    }
}