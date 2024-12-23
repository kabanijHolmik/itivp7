<?php

require 'vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Promise\Deferred;

class ReactiveDeliveryCalculator
{
    private LoopInterface $loop;

    // Параметры пользователя
    private float $weight;
    private float $size;
    private bool $isFragile;
    private bool $needsInspection;
    private string $fromCoord;
    private string $toCoord;

    // Веса для расчета
    private float $weightRate;
    private float $sizeRate;
    private float $fragilityRate;
    private float $inspectionRate;
    private float $distanceRate;

    public function __construct(
        LoopInterface $loop,
        float $weight,
        float $size,
        bool $isFragile,
        bool $needsInspection,
        string $fromCoord,
        string $toCoord
    ) {
        $this->loop = $loop;
        $this->weight = $weight;
        $this->size = $size;
        $this->isFragile = $isFragile;
        $this->needsInspection = $needsInspection;
        $this->fromCoord = $fromCoord;
        $this->toCoord = $toCoord;

        if ($size <= 0) {
            throw new Exception("Size must be greater than 0");
        }
    }

    private function getUserParams(): PromiseInterface
    {
        $deferred = new Deferred();

        $this->loop->addTimer(2, function () use ($deferred) {
            // Имитация получения сохраненных параметров
            $deferred->resolve([
                $this->weight,
                $this->size,
                $this->isFragile,
                $this->needsInspection,
                $this->fromCoord,
                $this->toCoord
            ]);
        });

        return $deferred->promise();
    }

    private function getWeights(): PromiseInterface
    {
        $deferred = new Deferred();

        $this->loop->addTimer(1.5, function () use ($deferred) {
            // Симуляция получения весов
            $this->weightRate = 10.0;
            $this->sizeRate = 5.0;
            $this->fragilityRate = 1.5;
            $this->inspectionRate = 1.2;
            $this->distanceRate = 0.5;

            $deferred->resolve([
                $this->weightRate,
                $this->sizeRate,
                $this->fragilityRate,
                $this->inspectionRate,
                $this->distanceRate
            ]);
        });

        return $deferred->promise();
    }

    private function calculateDistance(string $fromCoord, string $toCoord): float
    {
        [$x1, $y1] = $this->parseCoordinates($fromCoord);
        [$x2, $y2] = $this->parseCoordinates($toCoord);

        return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
    }

    private function parseCoordinates(string $coord): array
    {
        $cleaned = str_replace(['(', ')'], '', $coord);
        return array_map('floatval', explode(';', $cleaned));
    }

    public function calculateDeliveryCost(): PromiseInterface
    {
        return \React\Promise\all([$this->getUserParams(), $this->getWeights()])
            ->then(function () {
                $distance = $this->calculateDistance($this->fromCoord, $this->toCoord);

                $cost = ($this->weight * $this->weightRate) +
                    ($this->size * $this->sizeRate);

                if ($this->isFragile) {
                    $cost *= $this->fragilityRate;
                }

                if ($this->needsInspection) {
                    $cost *= $this->inspectionRate;
                }

                $cost += $distance * $this->distanceRate;

                return round($cost, 2);
            });
    }
}
