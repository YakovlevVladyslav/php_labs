<?php

declare(strict_types=1);

require_once 'conflictClass.php';

/**
 * Класс для обработки входящих данных формы и сохранения их в JSON.
 */
class ConflictHandler {
    private string $folder = 'conflicts';
    private string $idFile;

    public function __construct() {
        $basePath = dirname(__DIR__); 
        $this->folder = $basePath . '/conflicts';
        $this->idFile = $this->folder . '/last_id.json';
        $this->ensureDirectoryExists();
    }

    /**
     * Основной метод запуска обработки.
     */
    public function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $submittedDate = new DateTime($_POST['startingDate']);
            $this->validateDate($submittedDate);

            $newId = $this->generateNextId();

            // Создание объекта модели
            $conflictObject = new Conflict(
                $newId,
                htmlspecialchars($_POST['title']),
                htmlspecialchars($_POST['description']),
                $submittedDate,
                IsNuclearWeaponInvolved::from($_POST['nuclearWeapon']),
                isset($_POST['isActive'])
            );

            $this->saveConflict($conflictObject);
            $this->updateLastId($newId);

            header("Location: ../index.php?success=1");
            exit();

        } catch (Exception $e) {
            header("Location: index.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    /**
     * Проверяет, что папка для данных существует.
     */
    private function ensureDirectoryExists(): void {
        if (!is_dir($this->folder)) {
            mkdir($this->folder, 0777, true);
        }
    }

    /**
     * Валидация даты: нельзя создавать конфликты в будущем.
     */
    private function validateDate(DateTime $date): void {
        if ($date > new DateTime()) {
            throw new Exception("The starting date cannot be in the future.");
        }
    }

    /**
     * Получает следующий свободный ID.
     */
    private function generateNextId(): int {
        $currentId = 0;
        if (file_exists($this->idFile)) {
            $data = json_decode(file_get_contents($this->idFile), true);
            $currentId = $data['last_id'] ?? 0;
        }
        return $currentId + 1;
    }

    /**
     * Сохраняет данные конфликта в отдельный JSON файл.
     */
    private function saveConflict(Conflict $conflict): void {
        $serializedData = [
            'id' => $conflict->getId(),
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'date' => $_POST['startingDate'],
            'nuclear' => $_POST['nuclearWeapon'],
            'active' => isset($_POST['isActive'])
        ];
        
        $filePath = dirname($this->folder) . "/conflicts/conflict_{$conflict->getId()}.json";
        file_put_contents($filePath, json_encode($serializedData, JSON_PRETTY_PRINT));
    }

    /**
     * Обновляет счетчик последнего ID.
     */
    private function updateLastId(int $id): void {
        file_put_contents($this->idFile, json_encode(['last_id' => $id]));
    }
}

// Инициализация и запуск обработчика
$handler = new ConflictHandler();
$handler->handleRequest();