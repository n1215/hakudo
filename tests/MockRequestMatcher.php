<?php
declare(strict_types=1);

namespace N1215\Hakudo;

use N1215\Http\RequestMatcher\RequestMatcherInterface;
use N1215\Http\RequestMatcher\RequestMatchResultInterface;
use Psr\Http\Message\RequestInterface;

class MockRequestMatcher implements RequestMatcherInterface
{
    /** @var RequestMatchResultInterface  */
    private $result;

    public function __construct(RequestMatchResultInterface $result)
    {
        $this->result = $result;
    }

    public function match(RequestInterface $request): RequestMatchResultInterface
    {
        return $this->result;
    }
}
