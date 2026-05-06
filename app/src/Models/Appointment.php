<?php
// app/src/Models/Appointment.php

namespace App\Models;

/**
 * Модель для работы с записями на ремонт автомобиля (заявками).
 * * Обеспечивает CRUD-операции над таблицей appointments, включая
 * агрегацию данных с сопутствующими таблицами пользователей и услуг.
 */
class Appointment {
    
    /**
     * Экземпляр подключения к базе данных PDO.
     * @var \PDO
     */
    private \PDO $db;

    /**
     * Конструктор модели.
     * * @param \PDO $db Подключение к базе данных.
     */
    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    /**
     * Создает новую заявку на обслуживание автомобиля в базе данных.
     * * @param int $userId Идентификатор клиента.
     * @param int $serviceId Идентификатор выбранной услуги.
     * @param string $carMake Марка автомобиля.
     * @param string $carModel Модель автомобиля.
     * @param int $carYear Год выпуска автомобиля.
     * @param string $licensePlate Государственный регистрационный номер.
     * @param string $visitDate Желаемая дата посещения СТО (формат Y-m-d).
     * @param string $comment Дополнительный комментарий клиента к неисправности.
     * @return bool True в случае успешной записи, иначе false.
     */
    public function create(
        int $userId, 
        int $serviceId, 
        string $carMake, 
        string $carModel, 
        int $carYear, 
        string $licensePlate, 
        string $visitDate, 
        string $comment = ''
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO appointments (user_id, service_id, car_make, car_model, car_year, license_plate, visit_date, comment)
            VALUES (:user_id, :service_id, :car_make, :car_model, :car_year, :license_plate, :visit_date, :comment)
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'service_id' => $serviceId,
            'car_make' => $carMake,
            'car_model' => $carModel,
            'car_year' => $carYear,
            'license_plate' => $licensePlate,
            'visit_date' => $visitDate,
            'comment' => $comment
        ]);
    }

    /**
     * Получает список всех записей конкретного клиента с деталями по услугам.
     * * @param int $userId Идентификатор клиента.
     * @return array Массив ассоциативных массивов с данными записей и услуг.
     */
    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT a.*, s.name as service_name, s.price as service_price 
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.user_id = :user_id
            ORDER BY a.visit_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Возвращает полный список записей всех пользователей для панели администратора.
     * * Объединяет данные заявок с именами/телефонами клиентов и информацией об услугах.
     * * @return array Массив всех заявок СТО.
     */
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT a.*, 
                   s.name as service_name, 
                   s.price as service_price,
                   u.full_name as client_name,
                   u.phone as client_phone
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            JOIN users u ON a.user_id = u.id
            ORDER BY a.visit_date DESC, a.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Изменяет текущий статус заявки с валидацией на стороне PHP.
     * * Допускает к сохранению только значения из строго определенного списка статусов.
     * * @param int $id Идентификатор заявки.
     * @param string $status Новый статус ('pending', 'in_progress', 'completed', 'cancelled').
     * @return bool True, если статус успешно изменен, иначе false.
     */
    public function updateStatus(int $id, string $status): bool {
        $allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE appointments 
            SET status = :status 
            WHERE id = :id
        ");
        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }
}