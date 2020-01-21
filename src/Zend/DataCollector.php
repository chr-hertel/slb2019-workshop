<?php

declare(strict_types=1);

namespace TravelOrganizer\Zend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseCollector;

class DataCollector extends BaseCollector
{
    private $zendBridge;

    public function __construct(CoverageZendBridge $zendBridge)
    {
        $this->zendBridge = $zendBridge;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        $this->data = [
            'coverage' => $this->zendBridge->getCoverage(),
            'controller' => $this->zendBridge->getController(),
            'action' => $this->zendBridge->getAction(),
        ];
    }

    public function getName(): string
    {
        return 'zend';
    }

    public function getCoverage(): array
    {
        return $this->data['coverage'];
    }

    public function getController(): string
    {
        return $this->data['controller'];
    }

    public function getAction(): string
    {
        return $this->data['action'];
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
