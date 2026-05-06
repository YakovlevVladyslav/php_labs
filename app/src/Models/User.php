<?php
// app/src/Models/User.php

namespace App\Models;

/**
 * Модель для работы с учетными записями пользователей системы.
 * * Обеспечивает операции аутентификации, регистрации, получения
 * списка пользователей и управления учетными записями в таблице users.
 */
class User {
    
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
     * Выполняет поиск пользователя в базе данных по его Email адресу.
     * * @param string $email Уникальный Email пользователя.
     * @return array|null Ассоциативный массив с данными пользователя в случае успеха, иначе null.
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ? $user : null;
    }

    /**
     * Создает новую учетную запись пользователя с автоматическим хешированием пароля.
     * * @param string $email Электронная почта пользователя.
     * @param string $password Пароль в открытом виде (будет захеширован через bcrypt).
     * @param string $fullName Имя или ФИО пользователя.
     * @param string $phone Контактный номер телефона.
     * @param string $role Роль пользователя в системе ('user', 'admin'). По умолчанию 'user'.
     * @return bool True в случае успешного сохранения записи, иначе false.
     */
    public function create(string $email, string $password, string $fullName, string $phone, string $role = 'user'): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password_hash, full_name, phone, role) 
            VALUES (:email, :password_hash, :full_name, :phone, :role)
        ");
        
        return $stmt->execute([
            'email' => $email,
            'password_hash' => $hash,
            'full_name' => $fullName,
            'phone' => $phone,
            'role' => $role
        ]);
    }

    /**
     * Возвращает список всех зарегистрированных пользователей системы.
     * * Исключает из выборки хеши паролей в целях безопасности.
     * * @return array Массив ассоциативных массивов с данными пользователей.
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT id, email, full_name, phone, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Удаляет учетную запись пользователя из базы данных по его уникальному идентификатору.
     * * @param int $id Идентификатор удаляемого пользователя.
     * @return bool True в случае успешного удаления, иначе false.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}