<?php
declare(strict_types=1);

namespace N1215\Hakudo\RequestMatcher;

use N1215\Http\RequestMatcher\RequestMatcherInterface;

class Path
{
    public static function get(string $path): RequestMatcherInterface
    {
        return new MethodAndPathMatcher('GET', $path);
    }

    public static function post(string $path): RequestMatcherInterface
    {
        return new MethodAndPathMatcher('POST', $path);
    }
}
