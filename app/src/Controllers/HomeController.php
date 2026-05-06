<?php
// app/src/Controllers/HomeController.php

namespace App\Controllers;

use App\Core\BaseController;

/**
 * Контроллер главной страницы автосервиса AutoFix.
 * * Отвечает за вывод каталога услуг для неавторизованных пользователей
 * и клиентов, предоставляя возможность полнотекстового поиска и фильтрации по категориям.
 */
class HomeController extends BaseController {

    /**
     * Отображает главную страницу с каталогом активных услуг.
     * * Динамически формирует SQL-запрос на основе переданных GET-параметров фильтрации
     * (поисковый запрос по названию/описанию и выбор категории). В выборку попадают
     * только фактически активные (не архивированные) услуги СТО.
     * * @return void
     */
    public function index(): void {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';

        $categoriesStmt = $this->db->query("SELECT DISTINCT category FROM services WHERE is_active = 1 ORDER BY category ASC");
        $categories = $categoriesStmt->fetchAll(\PDO::FETCH_COLUMN);

        $sql = "SELECT * FROM services WHERE is_active = 1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE :search_name OR description LIKE :search_desc)";
            $params['search_name'] = '%' . $search . '%';
            $params['search_desc'] = '%' . $search . '%';
        }

        if (!empty($category)) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }

        $sql .= " ORDER BY category ASC, name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $services = $stmt->fetchAll();

        $this->render('home.html.twig', [
            'title' => 'Услуги автосервиса AutoFix',
            'services' => $services,
            'categories' => $categories,
            'currentSearch' => $search,
            'currentCategory' => $category
        ]);
    }
}