<?php
declare(strict_types=1);

namespace Tests\Middleware;

use Mockery\MockInterface;
use PhpCircle\Middleware\Dispatcher;
use PhpCircle\Middleware\Exceptions\InvalidMiddlewareException;
use PhpCircle\Middleware\Exceptions\NoMiddlewareDispatchedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \PhpCircle\Middleware\Dispatcher
 */
final class DispatcherTest extends AbstractTestCase
{
    /**
     * Test dispatcher to dispatch middleware successfully.
     *
     * @return void
     *
     * @throws \PhpCircle\Middleware\Exceptions\InvalidMiddlewareException
     * @throws \PhpCircle\Middleware\Exceptions\NoMiddlewareDispatchedException
     */
    public function testDispatchMiddlewaresSuccessfully(): void
    {
        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->mock(ServerRequestInterface::class);

        /** @var \Psr\Http\Server\RequestHandlerInterface $requestHandler */
        $requestHandler = $this->mock(RequestHandlerInterface::class);

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $this->mock(ResponseInterface::class);

        /** @var \Psr\Http\Server\MiddlewareInterface $middleware1 */
        $middleware1 = $this->mock(MiddlewareInterface::class,
            function (MockInterface $mock) use ($request, $requestHandler) {
                /** @var \Psr\Http\Message\ResponseInterface $response */
                $response = $this->mock(ResponseInterface::class);

                $mock->shouldReceive('process')
                    ->once()
                    ->with($request, $requestHandler)
                    ->andReturn($response);
            }
        );

        /** @var \Psr\Http\Server\MiddlewareInterface $middleware1 */
        $middleware2 = $this->mock(
            MiddlewareInterface::class,
            function (MockInterface $mock) use ($request, $requestHandler, $response) {
                $mock->shouldReceive('process')
                    ->once()
                    ->with($request, $requestHandler)
                    ->andReturn($response);
            }
        );

        $dispatcher = new Dispatcher($request, [$middleware1, $middleware2]);

        self::assertSame($response, $dispatcher->dispatch($requestHandler));
    }

    /**
     * Test dispatcher when given an invalid middleware.
     *
     * @return void
     *
     * @throws \PhpCircle\Middleware\Exceptions\InvalidMiddlewareException
     * @throws \PhpCircle\Middleware\Exceptions\NoMiddlewareDispatchedException
     */
    public function testInvalidMiddlewareGiven(): void
    {
        $this->expectException(InvalidMiddlewareException::class);

        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->mock(ServerRequestInterface::class);

        /** @var \Psr\Http\Server\RequestHandlerInterface $requestHandler */
        $requestHandler = $this->mock(RequestHandlerInterface::class);

        $dispatcher = new Dispatcher($request, [new \stdClass()]);

        $dispatcher->dispatch($requestHandler);
    }

    /**
     * Test dispatcher no middleware has been dispatched.
     *
     * @return void
     *
     * @throws \PhpCircle\Middleware\Exceptions\InvalidMiddlewareException
     * @throws \PhpCircle\Middleware\Exceptions\NoMiddlewareDispatchedException
     */
    public function testNoMiddlewareHasBeenDispatched(): void
    {
        $this->expectException(NoMiddlewareDispatchedException::class);

        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->mock(ServerRequestInterface::class);

        /** @var \Psr\Http\Server\RequestHandlerInterface $requestHandler */
        $requestHandler = $this->mock(RequestHandlerInterface::class);

        $dispatcher = new Dispatcher($request, []);

        $dispatcher->dispatch($requestHandler);
    }
}
