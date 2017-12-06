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
        $route = new Route($name, new MockRequestMatcher(RequestMatchResult::success([])), new MockRequestHandler());
        $this->assertEquals($name, $route->getName());
    }

    public function test_getHandler()
    {
        $handler = new MockRequestHandler();
        $route = new Route('dummy', new MockRequestMatcher(RequestMatchResult::success([])), $handler);
        $this->assertSame($handler, $route->getHandler());
    }

    public function test_match()
    {
        $result = RequestMatchResult::success([]);
        $route = new Route('dummy', new MockRequestMatcher($result), new MockRequestHandler());
        $this->assertSame($result, $route->match(new ServerRequest()));
    }
}
