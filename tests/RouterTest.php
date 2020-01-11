<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use N1215\Http\Router\Exception\RouteNotFoundException;
use N1215\Http\Router\Result\RoutingResultFactory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use N1215\Hakudo\RequestMatcher\Path;
use N1215\Jugoya\LazyRequestHandlerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;

class RouterTest extends TestCase
{
    public function test_match_when_success()
    {
        $router = new Router(LazyRequestHandlerBuilder::fromContainer(new MockContainer()), new RoutingResultFactory());

        $router->add(Path::get('|/test/get|'), GetTestAction::class, [HogeMiddleware::class]);
        $router->add(Path::post('|no_match|'), PostTestAction::class);
        $router->add(Path::post('|/resources/(?<resource_name>[a-z_]+)/(?<id>[0-9]+)|'), PostTestAction::class, [FugaMiddleware::class]);

        $request = new ServerRequest([], [], new Uri('/resources/posts/12345'), 'POST');

        $result = $router->match($request);

        $this->assertEquals(['resource_name' => 'posts', 'id' => '12345'], $result->getParams());
        $this->assertInstanceOf(RequestHandlerInterface::class, $result->getHandler());
        $this->assertEquals('post', $result->getHandler()->handle($request)->getBody()->__toString());
    }

    public function test_match_when_failure()
    {
        $router = new Router(LazyRequestHandlerBuilder::fromContainer(new MockContainer()), new RoutingResultFactory());

        $router->add(Path::get('|/test/get|'), GetTestAction::class, [HogeMiddleware::class]);

        $request = new ServerRequest([], [], new Uri('/resources/posts/12345'), 'POST');

        $this->expectException(RouteNotFoundException::class);
        $this->expectExceptionMessage('route not found');

        $router->match($request);
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
