<?php
declare(strict_types=1);

namespace PhpCircle\Middleware\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface DispatcherInterface
{
    public function dispatch(RequestHandlerInterface $handler): ResponseInterface;
}
