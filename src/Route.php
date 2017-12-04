<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use Interop\Http\Server\RequestHandlerInterface;
use N1215\Http\RequestMatcher\RequestMatcherInterface;
use N1215\Http\RequestMatcher\RequestMatchResultInterface;
use Psr\Http\Message\ServerRequestInterface;

class Route
{
    /** @var string  */
    private $name;

    /** @var RequestMatcherInterface  */
    private $matcher;

    /** @var RequestHandlerInterface  */
    private $handler;

    public function __construct(string $name, RequestMatcherInterface $matcher, RequestHandlerInterface $handler)
    {
        $this->name = $name;
        $this->matcher = $matcher;
        $this->handler = $handler;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function match(ServerRequestInterface $request): RequestMatchResultInterface
    {
        return $this->matcher->match($request);
    }

    public function getHandler(): RequestHandlerInterface
    {
        return $this->handler;
    }
}
