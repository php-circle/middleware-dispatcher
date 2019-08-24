<?php
declare(strict_types=1);

namespace PhpCircle\Middleware;

use PhpCircle\Middleware\Exceptions\InvalidMiddlewareException;
use PhpCircle\Middleware\Exceptions\NoMiddlewareDispatchedException;
use PhpCircle\Middleware\Interfaces\DispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Dispatcher implements DispatcherInterface
{
    /**
     * @var \Psr\Http\Server\MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    private $request;

    /**
     * Dispatcher constructor.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\MiddlewareInterface[] $middlewares
     */
    public function __construct(ServerRequestInterface $request, array $middlewares)
    {
        $this->middlewares = $middlewares;
        $this->request = $request;
    }

    /**
     * Dispatch middleware.
     *
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \PhpCircle\Middleware\Exceptions\InvalidMiddlewareException
     * @throws \PhpCircle\Middleware\Exceptions\NoMiddlewareDispatchedException
     */
    public function dispatch(RequestHandlerInterface $handler): ResponseInterface
    {
        $response = null;

        foreach ($this->middlewares as $middleware) {
            if ($middleware instanceof MiddlewareInterface) {
                $response = $middleware->process($this->request, $handler);

                continue;
            }

            throw new InvalidMiddlewareException('Invalid middleware.');
        }

        if ($response === null) {
            throw new NoMiddlewareDispatchedException('No middleware has been dispatched.');
        }

        return $response;
    }
}
