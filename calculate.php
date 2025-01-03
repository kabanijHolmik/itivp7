<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/itivp7/DeliveryCalculator.php";
require_once __DIR__ . "/ReactiveDeliveryCalculator.php";
require_once __DIR__ . "/queries.php";
require 'vendor/autoload.php';

use React\EventLoop\Factory;

try {
    // Проверяем, что форма отправлена методом POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Некорректный метод отправки формы.');
    }

    // Получаем данные из формы
    $weight = $_POST['weight'] ?? null;
    $size = $_POST['size'] ?? null;
    $fromPoint = $_POST['from-point'] ?? null;
    $toPoint = $_POST['to-point'] ?? null;
    $isFragile = isset($_POST['is-fragile']) ? true : false;
    $needsInspection = isset($_POST['needs-inspection']) ? true : false;


    if (!$fromPoint) {
        throw new Exception('Не выбран пункт отправления.');
    }

    if (!$toPoint) {
        throw new Exception('Не выбран пункт назначения.');
    }

    if ($fromPoint === $toPoint) {
        throw new Exception('Пункт отправления и пункт назначения не могут совпадать.');
    }

    // Проверка веса
    if (!$weight || !preg_match('/^\d+([\,\.]\d+)?$/', $weight)) {
        throw new Exception('Вес должен быть положительным числом. Запрещены символы + и - ');
    }

    // Преобразование веса в число с плавающей точкой
    $weight = (float) str_replace([',', '.'], '.', $weight);

    // Указание допустимого диапазона веса
    if ($weight <= 0 || $weight > 100) {
        throw new Exception('Вес должен быть больше 0 и не превышать 100 кг.');
    }

    // Проверка размера
    if (!$size || !preg_match('/^\d+([\,\.]\d+)?$/', $size)) {
        throw new Exception('Размер должен быть положительным числом. Запрещены символы + и - ');
    }

    // Преобразование размера в число с плавающей точкой
    $size = (float) str_replace([',', '.'], '.', $size);

    // Указание допустимого диапазона размера
    if ($size <= 0 || $size > 10) {
        throw new Exception('Размер должен быть больше 0 и не превышать 10 метров.');
    }

    // Преобразуем точки отправления и назначения в координаты (пример)
    // В реальном приложении координаты должны браться из базы данных
    $fromCoord = getCoordinates($fromPoint);
    $toCoord = getCoordinates($toPoint);

    // Инициализируем калькулятор доставки
    // $syncStart = microtime(true); // Начало синхронного расчета

    // $syncCalculator = new DeliveryCalculator();
    // $syncCost = $syncCalculator->calculateDeliveryCost(
    //     (float)$weight,
    //     (float)$size,
    //     $isFragile,
    //     $needsInspection,
    //     $fromCoord,
    //     $toCoord
    // );

    // $syncEnd = microtime(true); // Конец синхронного расчета
    // $syncDuration = $syncEnd - $syncStart;

    // *** Асинхронный подход (ReactPHP) ***
    $reactStart = microtime(true); // Начало асинхронного расчета

    $loop = Factory::create();
    $calculator = new ReactiveDeliveryCalculator(
        $loop,
        $weight,
        $size,
        $isFragile,
        $needsInspection,
        $fromCoord,
        $toCoord
    );

    $deliveryCost = null; // Переменная для хранения результата

    $calculator->calculateDeliveryCost()
        ->then(function ($cost) use (&$deliveryCost) {
            $deliveryCost = $cost; // Сохраняем результат в переменную
        });

    $loop->run();

    $reactEnd = microtime(true); // Конец асинхронного расчета
    $reactDuration = $reactEnd - $reactStart;

    $_SESSION['success'] = sprintf(
        "Стоимость доставки: %.2f руб.",
        $deliveryCost
    );
    $_SESSION['success'] = $_SESSION['success'] . " время: $reactDuration";
} catch (Exception $e) {
    // Если произошла ошибка, записываем её в сессию
    $_SESSION['error'] = $e->getMessage();
}

// Перенаправляем пользователя обратно на index.php
header('Location: index.php');
exit;
