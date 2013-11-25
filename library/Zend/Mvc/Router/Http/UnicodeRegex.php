<?php
namespace Zend\Mvc\Router\Http;

use Zend\Mvc\Router\Http\Regex;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

class UnicodeRegex extends Regex
{
    /**
     * match(): defined by RouteInterface interface.
     *
     * @param  Request $request
     * @param  integer $pathOffset
     * @return RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri  = $request->getUri();
        // path decoded before match
        $path = rawurldecode($uri->getPath());

        // regex with u modifier    
        if ($pathOffset !== null) {
            $result = preg_match('(\G' . $this->regex . ')u', $path, $matches, null, $pathOffset);
        } else {
            $result = preg_match('(^' . $this->regex . '$)u', $path, $matches);
        }

        if (!$result) {
            return null;
        }

        $matchedLength = strlen($matches[0]);

        foreach ($matches as $key => $value) {
            if (is_numeric($key) || is_int($key) || $value === '') {
                unset($matches[$key]);
            } else {
                $matches[$key] = $value;
            }
        }

        return new RouteMatch(array_merge($this->defaults, $matches), $matchedLength);
    }
}