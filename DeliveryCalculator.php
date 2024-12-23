<?php

class DeliveryCalculator
{
    private float $weightRate = 5.0;
    private float $sizeRate = 2.0;
    private float $fragilityRate = 1.1;
    private float $inspectionRate = 1.2;
    private float $distanceRate = 0.001;

    public function calculateDeliveryCost(
        float $weight,
        float $size,
        bool $isFragile,
        bool $needsInspection,
        string $fromCoord,
        string $toCoord
    ): float {
        $this->simulateDelay(2000);
        $distance = $this->calculateDistance($fromCoord, $toCoord);
        $this->simulateDelay(1000);

        $cost = ($weight * $this->weightRate) + ($size * $this->sizeRate);

        if ($isFragile) {
            $cost *= $this->fragilityRate;
        }

        if ($needsInspection) {
            $cost *= $this->inspectionRate;
        }

        $cost += $distance * $this->distanceRate;

        return round($cost, 2);
    }

    private function calculateDistance(string $fromCoord, string $toCoord): float
    {
        [$x1, $y1] = $this->parseCoordinates($fromCoord);
        [$x2, $y2] = $this->parseCoordinates($toCoord);

        $distance = sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));

        return $distance;
    }

    private function simulateDelay(int $milliseconds): void
    {
        usleep($milliseconds * 1000);
    }

    private function parseCoordinates(string $coordinates): array
    {
        $cleaned = str_replace(['(', ')'], '', $coordinates);
        return array_map('floatval', explode(';', $cleaned));
    }
}
