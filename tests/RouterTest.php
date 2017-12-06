<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use N1215\Hakudo\RequestMatcher\Path;
use N1215\Jugoya\LazyRequestHandlerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class RouterTest extends TestCase
{
    public function test_match_when_success()
    {
        $router = new Router(LazyRequestHandlerFactory::fromContainer(new MockContainer()));

        $router->add('get_endpoint_name', Path::get('|/test/get|'), GetTestAction::class, [HogeMiddleware::class]);
        $router->add('no_match_endpoint_name', Path::post('|no_match|'), PostTestAction::class);
        $router->add('post_endpoint_name', Path::post('|/resources/(?<resource_name>[a-z_]+)/(?<id>[0-9]+)|'), PostTestAction::class, [FugaMiddleware::class]);

        $request = new ServerRequest([], [], new Uri('/resources/posts/12345'), 'POST');

        $result = $router->match($request);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(['resource_name' => 'posts', 'id' => '12345'], $result->getMatchedParams());
    }

    public function test_match_when_failure()
    {
        $router = new Router(LazyRequestHandlerFactory::fromContainer(new MockContainer()));

        $router->add('get_endpoint_name', Path::get('|/test/get|'), GetTestAction::class, [HogeMiddleware::class]);

        $request = new ServerRequest([], [], new Uri('/resources/posts/12345'), 'POST');

        $result = $router->match($request);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals([], $result->getMatchedParams());
        $this->assertNull($result->getMatchedHandler());
    }
}

class MockContainer implements ContainerInterface
{
    public function get($id)
    {
        return new $id();
    }

    public function has($id)
    {
        return true;
    }
}


class GetTestAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('get');
        return $response;
    }
}

class PostTestAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('post');
        return $response;
    }
}

class HogeMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}


class FugaMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
