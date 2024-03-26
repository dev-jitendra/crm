<?php

namespace FastRoute\Dispatcher;

use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;

abstract class DispatcherTest extends TestCase
{
    
    abstract protected function getDispatcherClass();

    
    abstract protected function getDataGeneratorClass();

    
    private function generateDispatcherOptions()
    {
        return [
            'dataGenerator' => $this->getDataGeneratorClass(),
            'dispatcher' => $this->getDispatcherClass()
        ];
    }

    
    public function testFoundDispatches($method, $uri, $callback, $handler, $argDict)
    {
        $dispatcher = \FastRoute\simpleDispatcher($callback, $this->generateDispatcherOptions());
        $info = $dispatcher->dispatch($method, $uri);
        $this->assertSame($dispatcher::FOUND, $info[0]);
        $this->assertSame($handler, $info[1]);
        $this->assertSame($argDict, $info[2]);
    }

    
    public function testNotFoundDispatches($method, $uri, $callback)
    {
        $dispatcher = \FastRoute\simpleDispatcher($callback, $this->generateDispatcherOptions());
        $routeInfo = $dispatcher->dispatch($method, $uri);
        $this->assertArrayNotHasKey(1, $routeInfo,
            'NOT_FOUND result must only contain a single element in the returned info array'
        );
        $this->assertSame($dispatcher::NOT_FOUND, $routeInfo[0]);
    }

    
    public function testMethodNotAllowedDispatches($method, $uri, $callback, $availableMethods)
    {
        $dispatcher = \FastRoute\simpleDispatcher($callback, $this->generateDispatcherOptions());
        $routeInfo = $dispatcher->dispatch($method, $uri);
        $this->assertArrayHasKey(1, $routeInfo,
            'METHOD_NOT_ALLOWED result must return an array of allowed methods at index 1'
        );

        list($routedStatus, $methodArray) = $dispatcher->dispatch($method, $uri);
        $this->assertSame($dispatcher::METHOD_NOT_ALLOWED, $routedStatus);
        $this->assertSame($availableMethods, $methodArray);
    }

    
    public function testDuplicateVariableNameError()
    {
        \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/foo/{test}/{test:\d+}', 'handler0');
        }, $this->generateDispatcherOptions());
    }

    
    public function testDuplicateVariableRoute()
    {
        \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{id}', 'handler0'); 
            $r->addRoute('GET', '/user/{name}', 'handler1');
        }, $this->generateDispatcherOptions());
    }

    
    public function testDuplicateStaticRoute()
    {
        \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/user', 'handler0');
            $r->addRoute('GET', '/user', 'handler1');
        }, $this->generateDispatcherOptions());
    }

    
    public function testShadowedStaticRoute()
    {
        \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}', 'handler0');
            $r->addRoute('GET', '/user/nikic', 'handler1');
        }, $this->generateDispatcherOptions());
    }

    
    public function testCapturing()
    {
        \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/{lang:(en|de)}', 'handler0');
        }, $this->generateDispatcherOptions());
    }

    public function provideFoundDispatchCases()
    {
        $cases = [];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/resource/123/456', 'handler0');
        };

        $method = 'GET';
        $uri = '/resource/123/456';
        $handler = 'handler0';
        $argDict = [];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/handler0', 'handler0');
            $r->addRoute('GET', '/handler1', 'handler1');
            $r->addRoute('GET', '/handler2', 'handler2');
        };

        $method = 'GET';
        $uri = '/handler2';
        $handler = 'handler2';
        $argDict = [];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}/{id:[0-9]+}', 'handler0');
            $r->addRoute('GET', '/user/{id:[0-9]+}', 'handler1');
            $r->addRoute('GET', '/user/{name}', 'handler2');
        };

        $method = 'GET';
        $uri = '/user/rdlowrey';
        $handler = 'handler2';
        $argDict = ['name' => 'rdlowrey'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        

        $method = 'GET';
        $uri = '/user/12345';
        $handler = 'handler1';
        $argDict = ['id' => '12345'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        

        $method = 'GET';
        $uri = '/user/NaN';
        $handler = 'handler2';
        $argDict = ['name' => 'NaN'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        

        $method = 'GET';
        $uri = '/user/rdlowrey/12345';
        $handler = 'handler0';
        $argDict = ['name' => 'rdlowrey', 'id' => '12345'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{id:[0-9]+}', 'handler0');
            $r->addRoute('GET', '/user/12345/extension', 'handler1');
            $r->addRoute('GET', '/user/{id:[0-9]+}.{extension}', 'handler2');
        };

        $method = 'GET';
        $uri = '/user/12345.svg';
        $handler = 'handler2';
        $argDict = ['id' => '12345', 'extension' => 'svg'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}', 'handler0');
            $r->addRoute('GET', '/user/{name}/{id:[0-9]+}', 'handler1');
            $r->addRoute('GET', '/static0', 'handler2');
            $r->addRoute('GET', '/static1', 'handler3');
            $r->addRoute('HEAD', '/static1', 'handler4');
        };

        $method = 'HEAD';
        $uri = '/user/rdlowrey';
        $handler = 'handler0';
        $argDict = ['name' => 'rdlowrey'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        

        $method = 'HEAD';
        $uri = '/user/rdlowrey/1234';
        $handler = 'handler1';
        $argDict = ['name' => 'rdlowrey', 'id' => '1234'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        

        $method = 'HEAD';
        $uri = '/static0';
        $handler = 'handler2';
        $argDict = [];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        

        $method = 'HEAD';
        $uri = '/static1';
        $handler = 'handler4';
        $argDict = [];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}', 'handler0');
            $r->addRoute('POST', '/user/{name:[a-z]+}', 'handler1');
        };

        $method = 'POST';
        $uri = '/user/rdlowrey';
        $handler = 'handler1';
        $argDict = ['name' => 'rdlowrey'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}', 'handler0');
            $r->addRoute('POST', '/user/{name:[a-z]+}', 'handler1');
            $r->addRoute('POST', '/user/{name}', 'handler2');
        };

        $method = 'POST';
        $uri = '/user/rdlowrey';
        $handler = 'handler1';
        $argDict = ['name' => 'rdlowrey'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}', 'handler0');
            $r->addRoute('GET', '/user/{name}/edit', 'handler1');
        };

        $method = 'GET';
        $uri = '/user/rdlowrey/edit';
        $handler = 'handler1';
        $argDict = ['name' => 'rdlowrey'];

        $cases[] = [$method, $uri, $callback, $handler, $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute(['GET', 'POST'], '/user', 'handlerGetPost');
            $r->addRoute(['DELETE'], '/user', 'handlerDelete');
            $r->addRoute([], '/user', 'handlerNone');
        };

        $argDict = [];
        $cases[] = ['GET', '/user', $callback, 'handlerGetPost', $argDict];
        $cases[] = ['POST', '/user', $callback, 'handlerGetPost', $argDict];
        $cases[] = ['DELETE', '/user', $callback, 'handlerDelete', $argDict];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('POST', '/user.json', 'handler0');
            $r->addRoute('GET', '/{entity}.json', 'handler1');
        };

        $cases[] = ['GET', '/user.json', $callback, 'handler1', ['entity' => 'user']];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '', 'handler0');
        };

        $cases[] = ['GET', '', $callback, 'handler0', []];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('HEAD', '/a/{foo}', 'handler0');
            $r->addRoute('GET', '/b/{foo}', 'handler1');
        };

        $cases[] = ['HEAD', '/b/bar', $callback, 'handler1', ['foo' => 'bar']];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('HEAD', '/a', 'handler0');
            $r->addRoute('GET', '/b', 'handler1');
        };

        $cases[] = ['HEAD', '/b', $callback, 'handler1', []];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/foo', 'handler0');
            $r->addRoute('HEAD', '/{bar}', 'handler1');
        };

        $cases[] = ['HEAD', '/foo', $callback, 'handler1', ['bar' => 'foo']];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('*', '/user', 'handler0');
            $r->addRoute('*', '/{user}', 'handler1');
            $r->addRoute('GET', '/user', 'handler2');
        };

        $cases[] = ['GET', '/user', $callback, 'handler2', []];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('*', '/user', 'handler0');
            $r->addRoute('GET', '/user', 'handler1');
        };

        $cases[] = ['POST', '/user', $callback, 'handler0', []];

        

        $cases[] = ['HEAD', '/user', $callback, 'handler1', []];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/{bar}', 'handler0');
            $r->addRoute('*', '/foo', 'handler1');
        };

        $cases[] = ['GET', '/foo', $callback, 'handler0', ['bar' => 'foo']];

        

        $callback = function(RouteCollector $r) {
            $r->addRoute('GET', '/user', 'handler0');
            $r->addRoute('*', '/{foo:.*}', 'handler1');
        };

        $cases[] = ['POST', '/bar', $callback, 'handler1', ['foo' => 'bar']];

        

        return $cases;
    }

    public function provideNotFoundDispatchCases()
    {
        $cases = [];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/resource/123/456', 'handler0');
        };

        $method = 'GET';
        $uri = '/not-found';

        $cases[] = [$method, $uri, $callback];

        

        
        $method = 'POST';
        $uri = '/not-found';

        $cases[] = [$method, $uri, $callback];

        

        
        $method = 'PUT';
        $uri = '/not-found';

        $cases[] = [$method, $uri, $callback];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/handler0', 'handler0');
            $r->addRoute('GET', '/handler1', 'handler1');
            $r->addRoute('GET', '/handler2', 'handler2');
        };

        $method = 'GET';
        $uri = '/not-found';

        $cases[] = [$method, $uri, $callback];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}/{id:[0-9]+}', 'handler0');
            $r->addRoute('GET', '/user/{id:[0-9]+}', 'handler1');
            $r->addRoute('GET', '/user/{name}', 'handler2');
        };

        $method = 'GET';
        $uri = '/not-found';

        $cases[] = [$method, $uri, $callback];

        

        
        $method = 'GET';
        $uri = '/user/rdlowrey/12345/not-found';

        $cases[] = [$method, $uri, $callback];

        

        
        $method = 'HEAD';

        $cases[] = [$method, $uri, $callback];

        

        return $cases;
    }

    public function provideMethodNotAllowedDispatchCases()
    {
        $cases = [];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/resource/123/456', 'handler0');
        };

        $method = 'POST';
        $uri = '/resource/123/456';
        $allowedMethods = ['GET'];

        $cases[] = [$method, $uri, $callback, $allowedMethods];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/resource/123/456', 'handler0');
            $r->addRoute('POST', '/resource/123/456', 'handler1');
            $r->addRoute('PUT', '/resource/123/456', 'handler2');
            $r->addRoute('*', '/', 'handler3');
        };

        $method = 'DELETE';
        $uri = '/resource/123/456';
        $allowedMethods = ['GET', 'POST', 'PUT'];

        $cases[] = [$method, $uri, $callback, $allowedMethods];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}/{id:[0-9]+}', 'handler0');
            $r->addRoute('POST', '/user/{name}/{id:[0-9]+}', 'handler1');
            $r->addRoute('PUT', '/user/{name}/{id:[0-9]+}', 'handler2');
            $r->addRoute('PATCH', '/user/{name}/{id:[0-9]+}', 'handler3');
        };

        $method = 'DELETE';
        $uri = '/user/rdlowrey/42';
        $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH'];

        $cases[] = [$method, $uri, $callback, $allowedMethods];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('POST', '/user/{name}', 'handler1');
            $r->addRoute('PUT', '/user/{name:[a-z]+}', 'handler2');
            $r->addRoute('PATCH', '/user/{name:[a-z]+}', 'handler3');
        };

        $method = 'GET';
        $uri = '/user/rdlowrey';
        $allowedMethods = ['POST', 'PUT', 'PATCH'];

        $cases[] = [$method, $uri, $callback, $allowedMethods];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute(['GET', 'POST'], '/user', 'handlerGetPost');
            $r->addRoute(['DELETE'], '/user', 'handlerDelete');
            $r->addRoute([], '/user', 'handlerNone');
        };

        $cases[] = ['PUT', '/user', $callback, ['GET', 'POST', 'DELETE']];

        

        $callback = function (RouteCollector $r) {
            $r->addRoute('POST', '/user.json', 'handler0');
            $r->addRoute('GET', '/{entity}.json', 'handler1');
        };

        $cases[] = ['PUT', '/user.json', $callback, ['POST', 'GET']];

        

        return $cases;
    }
}
