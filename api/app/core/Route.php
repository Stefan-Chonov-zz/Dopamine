<?php

namespace App\Core;

class Route
{
    private $route;
    private $direction;
    private $breadcrumbs;
    private $method;
    private $alias;

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}