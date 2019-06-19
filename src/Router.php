<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use N1215\Http\RequestMatcher\RequestMatcherInterface;
use N1215\Http\Router\Exception\RouteNotFoundException;
use N1215\Http\Router\Result\RoutingResultFactory;
use N1215\Http\Router\RouterInterface;
use N1215\Http\Router\RoutingError;
use N1215\Http\Router\RoutingResult;
use N1215\Http\Router\RoutingResultFactoryInterface;
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

    /**
     * @var RoutingResultFactory
     */
    private $resultFactory;

    public function __construct(RequestHandlerBuilderInterface $builder, RoutingResultFactory $resultFactory)
    {
        $this->builder = $builder;
        $this->resultFactory = $resultFactory;
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
                return $this->resultFactory->make(
                    $route->getHandler(),
                    $requestMatchResult->getParams()
                );
            }
        }

        throw new RouteNotFoundException('route not found');
    }
}
