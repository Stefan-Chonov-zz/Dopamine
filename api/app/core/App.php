<?php

namespace App\Core;

class App
{
    private $log;

    public function __construct()
    {
        $this->log = Log::getInstance(env('LOG_PATH'));

        $this->init();
    }

    private function init()
    {
        try {
            // Load parameters (breadcrumbs)
            $this->initRequestParameters();
            $url = $this->parseUrl();
            $breadcrumbs = $url ? array_values($url) : [];
            $breadcrumbs = array_filter($breadcrumbs, function ($input) {return ($input & 1);}, ARRAY_FILTER_USE_KEY);
            $breadcrumbs = array_values($breadcrumbs);

            // Load routes
            $routes = require_once '../app/routes.php';

            // Init default route
            $routeAndDirection = new Route();
            $routeAndDirection->setRoute('notFound');
            $routeAndDirection->setDirection('index');

            // Determine route and direction
            if ($routes) {
                $last = 0;
                foreach ($routes as $route) {
                    if (count($breadcrumbs) == count($route->getBreadcrumbs())) {
                        $requestedUrl = vsprintf($route->getAlias(), $breadcrumbs);
                        similar_text($_GET['url'], $requestedUrl, $similar);
                        if ($similar > $last && $similar > 80.00 && $route->getMethod() == $_SERVER['REQUEST_METHOD']) {
                            $last = $similar;
                            $routeAndDirection = $route;
                        }
                    }
                }
            } else {
                $this->log->warning("There is invalid route in '/app/routes.php'");
            }

            // If route and direction are not determined load "notFound" route
            if ($routeAndDirection->getRoute() == 'notFound') {
                require_once '../app/routes/' . $routeAndDirection->getRoute() . '.php';
            }

            $class = $routeAndDirection->getRoute();
            $func = $routeAndDirection->getDirection();
            $obj = new $class();
            echo $obj->$func(array_shift($breadcrumbs));
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }

    /**
     * @return array
     */
    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    }

    /**
     * @return array
     */
    public function initRequestParameters()
    {
        try {
            $putDeleteParameters = file_get_contents("php://input");
            if (!empty($putDeleteParameters)) {
                $putDeleteParameters = json_decode($putDeleteParameters, true);
            }

            if (empty($putDeleteParameters)) {
                $putDeleteParameters = [];
            }

            $postParameters = $_POST;
            if (count($putDeleteParameters) == 0 && !empty($postParameters)) {
                foreach ($postParameters as $key => $value) {
                    if (strpos($key, '_') !== false) {
                        throw new \Exception(json_encode([ 'message' => 'Post parameters should be posted in raw JSON format. Form-data is not supported.' ]));
                    }
                }
            } else {
                $postParameters = [];
                $_POST = $putDeleteParameters;
            }

            return array_merge_recursive($putDeleteParameters, $postParameters);
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }
}
