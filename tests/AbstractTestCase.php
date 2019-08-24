<?php
declare(strict_types=1);

namespace Tests\Middleware;

use Closure;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Mockery with closure.
     *
     * @param string $mockedClass
     * @param null|\Closure $expectation
     *
     * @return \Mockery\MockInterface
     */
    protected function mock(string $mockedClass, ?Closure $expectation = null): MockInterface
    {
        $mockery = Mockery::mock($mockedClass);

        if ($expectation !== null) {
            $expectation($mockery);
        }

        return $mockery;
    }
}
