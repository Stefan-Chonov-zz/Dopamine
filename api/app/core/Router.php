<?php

namespace App\Core;

class Router
{
    private static $routes;

    /**
     * @param string $requestMethod
     * @param string $urlAlias
     * @param $routeAndDirection
     * @return Route|null
     */
    public function map($requestMethod, $urlAlias, $routeAndDirection)
    {
        $route = new Route();
        $route->setMethod($requestMethod);

        $breadcrumbs = $this->findAliases($urlAlias);
        $route->setBreadcrumbs($breadcrumbs);

        $urlAlias = str_replace('[', '', $urlAlias);
        $urlAlias = str_replace(']', '', $urlAlias);
        $urlAlias = $this->replaceAliasesWithPlaceholders($urlAlias);

        $route->setAlias($urlAlias);

        if (is_string($routeAndDirection)) {
            $routeAndDirection = $this->parseRouteAndDirection($routeAndDirection);
        } else if (is_array($routeAndDirection) && count($routeAndDirection) != 2) {
            $routeAndDirection = NULL;
        }

        if (isset($routeAndDirection) && !empty($routeAndDirection)) {
            $route->setRoute(array_keys($routeAndDirection)[0]);
            $route->setDirection(array_values($routeAndDirection)[0]);

            self::$routes[] = $route;
        }

        return $route;
    }

    /**
     * @return array|bool
     */
    public function match()
    {
        if (!empty(self::$routes)) {
            foreach (self::$routes as $route) {
                $isRouteExists = file_exists('../app/routes/' . $route->getRoute() . '.php');
                if (!$isRouteExists) {
                    return FALSE;
                }

                require_once '../app/routes/' . $route->getRoute() . '.php';

                $routeName = $route->getRoute();
                if (!method_exists(new $routeName(), $route->getDirection())) {
                    return FALSE;
                }
            }

            return array_values(self::$routes);
        }

        return FALSE;
    }

    private function parseRouteAndDirection($routeAndDirection)
    {
        $delimiter = '#';
        $result = [];

        if (is_string($routeAndDirection) && strpos($routeAndDirection, $delimiter) !== FALSE) {
            $routeAndDirection = explode($delimiter, $routeAndDirection);
            if (isset($routeAndDirection) && count($routeAndDirection) == 2) {
                $result = [ $routeAndDirection[0] => $routeAndDirection[1] ];
            }
        }

        return $result;
    }

    private function findAliases($aliases)
    {
        preg_match_all('/:(\w+)/', $aliases, $breadcrumbs);

        return $breadcrumbs[1];
    }

    private function replaceAliasesWithPlaceholders($aliases)
    {
        $aliases = preg_replace('/:id/', '%d', $aliases);

        return preg_replace('/:(\w+)/', '%s', $aliases);
    }
}