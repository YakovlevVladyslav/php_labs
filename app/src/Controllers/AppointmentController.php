<?php
// app/src/Controllers/AppointmentController.php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Appointment;

/**
 * Контроллер для работы с записями на обслуживание.
 * * Обрабатывает вывод личного кабинета клиента и процесс
 * создания новых заявок на ремонт автомобиля.
 */
class AppointmentController extends BaseController {
    
    /**
     * Модель для управления записями.
     * @var Appointment
     */
    private Appointment $appointmentModel;

    /**
     * Конструктор контроллера.
     * * Инициализирует модель записей и проверяет сессию авторизованного пользователя.
     * Если сессия отсутствует, выполняет редирект на форму авторизации.
     */
    public function __construct() {
        parent::__construct();
        $this->appointmentModel = new Appointment($this->db);

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Отображает форму онлайн-записи на ремонт и обрабатывает её отправку.
     * * Выполняет комплексную валидацию данных автомобиля, даты визита
     * и проверяет фактическую активность выбранной услуги в базе данных.
     * * @return void
     */
    public function create(): void {
        $errors = [];
        $old = [
            'serviceId' => isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0
        ];

        $services = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY category, name")->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serviceId = (int)($_POST['service_id'] ?? 0);
            $carMake = trim($_POST['car_make'] ?? '');
            $carModel = trim($_POST['car_model'] ?? '');
            $carYear = (int)($_POST['car_year'] ?? 0);
            $licensePlate = trim($_POST['license_plate'] ?? '');
            $visitDate = $_POST['visit_date'] ?? '';
            $comment = trim($_POST['comment'] ?? '');

            $old = compact('serviceId', 'carMake', 'carModel', 'carYear', 'licensePlate', 'visitDate', 'comment');

            if ($serviceId <= 0) {
                $errors[] = 'Выберите услугу из списка.';
            } else {
                $stmt = $this->db->prepare("SELECT id FROM services WHERE id = :id AND is_active = 1");
                $stmt->execute(['id' => $serviceId]);
                if (!$stmt->fetch()) {
                    $errors[] = 'Выбранная услуга не существует или была удалена из каталога.';
                }
            }

            if (empty($carMake) || empty($carModel)) {
                $errors[] = 'Укажите марку и модель автомобиля.';
            }
            if ($carYear < 1900 || $carYear > (int)date('Y') + 1) {
                $errors[] = 'Укажите корректный год выпуска автомобиля.';
            }
            if (empty($licensePlate)) {
                $errors[] = 'Укажите гос. номер автомобиля.';
            }
            if (empty($visitDate)) {
                $errors[] = 'Выберите желаемую дату визита.';
            } elseif (strtotime($visitDate) < strtotime(date('Y-m-d'))) {
                $errors[] = 'Дата визита не может быть в прошлом.';
            }

            if (empty($errors)) {
                $success = $this->appointmentModel->create(
                    $_SESSION['user_id'],
                    $serviceId,
                    $carMake,
                    $carModel,
                    $carYear,
                    $licensePlate,
                    $visitDate,
                    $comment
                );

                if ($success) {
                    header('Location: /my-appointments?success=1');
                    exit;
                } else {
                    $errors[] = 'Не удалось создать запись. Пожалуйста, попробуйте еще раз.';
                }
            }
        }

        $this->render('appointment/create.html.twig', [
            'services' => $services,
            'errors' => $errors,
            'old' => $old
        ]);
    }

    /**
     * Отображает страницу личного кабинета со списком всех записей текущего клиента.
     * * @return void
     */
    public function index(): void {
        $appointments = $this->appointmentModel->getByUserId($_SESSION['user_id']);

        $this->render('appointment/index.html.twig', [
            'appointments' => $appointments,
            'success' => $_GET['success'] ?? null
        ]);
    }
}