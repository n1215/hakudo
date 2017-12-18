<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use N1215\Http\RequestMatcher\RequestMatchResult;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class RouteTest extends TestCase
{
    public function test_getName()
    {
        $name = 'hello';
        $route = new Route(new MockRequestMatcher(RequestMatchResult::success([])), new MockRequestHandler(), $name);
        $this->assertEquals($name, $route->getName());
    }
    public function test_name()
    {
        $route = new Route(new MockRequestMatcher(RequestMatchResult::success([])), new MockRequestHandler());
        $this->assertNull($route->getName());
        $name = 'hello';
        $this->assertInstanceOf(Route::class, $route->name($name));
        $this->assertEquals($name, $route->getName());
    }

    public function test_getHandler()
    {
        $handler = new MockRequestHandler();
        $route = new Route( new MockRequestMatcher(RequestMatchResult::success([])), $handler, 'dummy');
        $this->assertSame($handler, $route->getHandler());
    }

    public function test_match()
    {
        $result = RequestMatchResult::success([]);
        $route = new Route(new MockRequestMatcher($result), new MockRequestHandler(), 'dummy');
        $this->assertSame($result, $route->match(new ServerRequest()));
    }
}
