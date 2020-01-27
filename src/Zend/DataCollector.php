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
            'zend_score' => $this->zendBridge->getZendScore(),
            'doctrine_score' => $this->zendBridge->getDoctrineScore(),
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

    public function getControllerFiles(): array
    {
        return array_filter($this->data['coverage'], static function (string $file) {
            return false !== stripos($file, 'controllers');
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getModelFiles(): array
    {
        return array_filter($this->data['coverage'], static function (string $file) {
            return false !== stripos($file, 'models');
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getTemplateFiles(): array
    {
        return array_filter($this->data['coverage'], static function (string $file) {
            return false !== stripos($file, '.phtml');
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getZendScore(): int
    {
        return $this->data['zend_score'];
    }

    public function getDoctrineScore(): int
    {
        return $this->data['doctrine_score'];
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
