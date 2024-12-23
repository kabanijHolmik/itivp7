<?php

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Promise\PromiseInterface;

require_once __DIR__ . '/../ReactiveDeliveryCalculator.php';

class ReactiveDeliveryCalculatorTest extends TestCase
{
    public function testCalculateDeliveryCost()
    {
        $loop = Factory::create();
        $calculator = new ReactiveDeliveryCalculator(
            $loop,
            10.0, // weight
            5.0,  // size
            true, // isFragile
            false, // needsInspection
            '(10;10)', // fromCoord
            '(20;10)' // toCoord
        );

        $promise = $calculator->calculateDeliveryCost();
        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->then(function ($cost) {
            $this->assertIsFloat($cost);
            $this->assertGreaterThan(0, $cost);
        });

        $loop->run();
    }

    public function testInvalidSize()
    {
        $this->expectException(Exception::class);
        $loop = Factory::create();
        $calculator = new ReactiveDeliveryCalculator(
            $loop,
            10.0, // weight
            -5.0,  // invalid size
            true, // isFragile
            false, // needsInspection
            '(10;10)', // fromCoord
            '(20;10)' // toCoord
        );

        $calculator->calculateDeliveryCost();
        $loop->run();
    }
}