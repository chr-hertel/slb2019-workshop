<?php

declare(strict_types=1);

namespace TravelOrganizer\Zend;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Xdebug;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CoverageZendBridge implements ZendBridgeInterface
{
    private $zendBridge;
    private $projectDirectory;
    private $coverage = [];
    private $controller = '';
    private $action = '';

    public function __construct(ZendBridgeInterface $zendBridge, string $projectDirectory)
    {
        $this->zendBridge = $zendBridge;
        $this->projectDirectory = $projectDirectory;
    }

    public function executeZend(Request $request, string $controller, string $action): Response
    {
        xdebug_start_code_coverage();

        $response = $this->zendBridge->executeZend($request, $controller, $action);

        $this->coverage = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();

        $this->controller = $controller;
        $this->action = $action;

        return $response;
    }

    public function getCoverage(): array
    {
        $filtered = array_filter($this->coverage, function (string $file) {
            return 0 === stripos($file, $this->projectDirectory.DIRECTORY_SEPARATOR.'library')
                || 0 === stripos($file, $this->projectDirectory.DIRECTORY_SEPARATOR.'application');
        }, ARRAY_FILTER_USE_KEY);

        $data = [];
        foreach ($filtered as $file => $values) {
            $key = str_replace($this->projectDirectory.DIRECTORY_SEPARATOR, '', $file);
            $data[$key] = $values;
        }

        return $data;
    }

    public function getZendScore(): int
    {
        $filtered = array_filter($this->coverage, static function (string $file) {
            return false !== stripos($file, DIRECTORY_SEPARATOR.'Zend'.DIRECTORY_SEPARATOR);
        }, ARRAY_FILTER_USE_KEY);

        return count($filtered);
    }

    public function getDoctrineScore(): int
    {
        $filtered = array_filter($this->coverage, static function (string $file) {
            return false !== stripos($file, DIRECTORY_SEPARATOR.'Doctrine'.DIRECTORY_SEPARATOR);
        }, ARRAY_FILTER_USE_KEY);

        return count($filtered);
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
