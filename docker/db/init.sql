-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- <-- Изменили с password на password_hash
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы услуг (С ДОБАВЛЕННЫМ ПОЛЕМ is_active)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    is_active TINYINT(1) NOT NULL DEFAULT 1 -- <<-- Наше новое поле!
);

-- Создание таблицы записей на ремонт
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT,
    car_make VARCHAR(100) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    car_year INT NOT NULL,
    license_plate VARCHAR(50) NOT NULL,
    visit_date DATE NOT NULL,
    comment TEXT,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- Наполняем базу базовыми услугами по умолчанию
INSERT INTO services (name, category, price, description, is_active) VALUES
('Замена масла и фильтра', 'ТО', 1200.00, 'Замена моторного масла и масляного фильтра (материалы не включены).', 1),
('Компьютерная диагностика', 'Диагностика', 800.00, 'Полное сканирование электронных систем автомобиля на ошибки.', 1),
('Замена передних тормозных колодок', 'Тормозная система', 1500.00, 'Замена тормозных колодок передней оси автомобиля.', 1),
('Ремонт генератора', 'Электрика', 2500.00, 'Снятие, диагностика и замена изношенных деталей генератора.', 1);