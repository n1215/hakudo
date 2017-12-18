<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use N1215\Http\RequestMatcher\RequestMatcherInterface;
use N1215\Http\Router\RouterInterface;
use N1215\Http\Router\RoutingError;
use N1215\Http\Router\RoutingResult;
use N1215\Http\Router\RoutingResultInterface;
use N1215\Jugoya\RequestHandlerBuilderInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    /**
     * @var Route[]
     */
    private $routes;

    /**
     * @var RequestHandlerBuilderInterface
     */
    private $builder;

    public function __construct(RequestHandlerBuilderInterface $builder)
    {
        $this->builder = $builder;
        $this->routes = [];
    }

    private function addRoute(Route $route): Route
    {
        $this->routes[] = $route;
        return $route;
    }

    public function add(RequestMatcherInterface $matcher, $coreHandlerRef, array $middlewareRefs = []): Route
    {
        $handler = $this->builder->build($coreHandlerRef, $middlewareRefs);
        return $this->addRoute(new Route($matcher, $handler));
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
