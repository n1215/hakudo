<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use N1215\Http\RequestMatcher\RequestMatcherInterface;
use N1215\Http\Router\RouterInterface;
use N1215\Http\Router\RoutingError;
use N1215\Http\Router\RoutingResult;
use N1215\Http\Router\RoutingResultInterface;
use N1215\Jugoya\RequestHandlerFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    /**
     * @var Route[]
     */
    private $routes;

    /**
     * @var RequestHandlerFactoryInterface
     */
    private $factory;

    public function __construct(RequestHandlerFactoryInterface $factory)
    {
        $this->factory = $factory;
        $this->routes = [];
    }

    private function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    public function add(string $name, RequestMatcherInterface $matcher, $coreHandlerRef, array $middlewareRefs = [])
    {
        $handler = $this->factory->create($coreHandlerRef, $middlewareRefs);
        $this->addRoute(new Route($name, $matcher, $handler));
    }

    public function match(ServerRequestInterface $request): RoutingResultInterface
    {
        foreach ($this->routes as $route) {

            $requestMatchResult = $route->match($request);

            if ($requestMatchResult->isSuccess()) {
                return RoutingResult::success(
                    $route->getHandler(),
                    $requestMatchResult->getParams()
                );
            }
        }

        return RoutingResult::failure(new RoutingError(404, 'route not found'));
    }
}
