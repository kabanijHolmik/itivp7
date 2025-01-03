<?php
require_once __DIR__ . "/ReactiveDeliveryCalculator.php";
require_once __DIR__ . "/queries.php";
require_once __DIR__ . "/DeliveryCalculator.php";

use React\EventLoop\Factory;
function profileCalculations($weight, $size, $isFragile, $needsInspection, $fromPoint, $toPoint) {
    // Преобразуем точки отправления и назначения в координаты (пример)
    $fromCoord = getCoordinates($fromPoint);
    $toCoord = getCoordinates($toPoint);

    // Инициализируем калькулятор доставки
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

    return $deliveryCost;
}

// Пример вызова функции
$weight = 10;
$size = 5;
$isFragile = true;
$needsInspection = false;
$fromPoint = 'Сурганова д.54';
$toPoint = 'Якуба Коласа д.54';

$cost = profileCalculations($weight, $size, $isFragile, $needsInspection, $fromPoint, $toPoint);
echo "Delivery cost: " . $cost;