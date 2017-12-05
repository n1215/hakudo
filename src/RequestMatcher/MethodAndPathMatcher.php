<?php
declare(strict_types=1);

namespace N1215\Hakudo\RequestMatcher;

use N1215\Http\RequestMatcher\RequestMatcherInterface;
use N1215\Http\RequestMatcher\RequestMatchResult;
use N1215\Http\RequestMatcher\RequestMatchResultInterface;
use Psr\Http\Message\RequestInterface;

class MethodAndPathMatcher implements RequestMatcherInterface {

    /** @var string */
    private $method;

    /** @var string */
    private $pathRegex;

    public function __construct(string $method, string $pathRegex)
    {
        $this->method = $method;
        $this->pathRegex = $pathRegex;
    }

    public function match(RequestInterface $request): RequestMatchResultInterface
    {
        if (strtolower($this->method) !== strtolower($request->getMethod())) {
            return RequestMatchResult::failure();
        }

        if (!preg_match($this->pathRegex, $request->getUri()->getPath(), $matches)) {
            return RequestMatchResult::failure();
        }

        $params = array_filter($matches, function($key) {
            return is_string($key);
        }, ARRAY_FILTER_USE_KEY);

        return RequestMatchResult::success($params);
    }
}
