<?php declare(strict_types = 1);

namespace Fixture;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class CalculatorCheck extends TestCase
{

    public function testAdd(): void
    {
        self::assertSame(3, (new Calculator())->add(1, 2));
    }

    public function testAddAgain(): void
    {
        self::assertSame(5, (new Calculator())->add(2, 3));
    }

    #[RunInSeparateProcess]
    public function testDivideInSeparateProcess(): void
    {
        self::assertSame(2.0, (new Calculator())->divide(4, 2));
    }

}
