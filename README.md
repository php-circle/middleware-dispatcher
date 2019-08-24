# middleware-dispatcher
A PSR-15 compliant middleware dispatcher.

## Usage
```php
$dispatcher = new Dispatcher($request, [$middleware1, $middleware2]);

$dispatcher->dispatch(new PsrRequestHandler());
```
