<?php
namespace Tests;

class CalculatorTest extends \PHPUnit\Framework\TestCase {
    public function testAdd()
    {
        $calculator = new \App\Calculator;
        $calculator->setOperands([5,20]);
        $this->assertEquals(25, $calculator->add());
    }
}

