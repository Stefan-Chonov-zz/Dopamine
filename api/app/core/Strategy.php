<?php

namespace App\Core;

use App\Core\Interfaces\StrategyInterface;

class Strategy
{
    private $strategy;

    /**
     * Strategy constructor.
     * @param StrategyInterface $strategy
     */
    public function __construct(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param StrategyInterface $strategy
     * @return void
     */
    public function setStrategy(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function execute($data)
    {
        return $this->strategy->execute($data);
    }
}