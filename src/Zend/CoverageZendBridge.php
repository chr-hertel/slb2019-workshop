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
        $filter = new Filter();
        $filter->addDirectoryToWhitelist($this->projectDirectory.DIRECTORY_SEPARATOR.'library');
        $filter->addDirectoryToWhitelist($this->projectDirectory.DIRECTORY_SEPARATOR.'application');

        $driver = new Xdebug($filter);
        $coverage = new CodeCoverage($driver);

        $coverage->start('ZendBridge');

        $response = $this->zendBridge->executeZend($request, $controller, $action);

        $this->coverage = array_filter($coverage->stop(), function (string $file) {
            return 0 === stripos($file, $this->projectDirectory.DIRECTORY_SEPARATOR.'library')
                || 0 === stripos($file, $this->projectDirectory.DIRECTORY_SEPARATOR.'application');
        }, ARRAY_FILTER_USE_KEY);

        $this->controller = $controller;
        $this->action = $action;

        return $response;
    }

    public function getCoverage(): array
    {
        return $this->coverage;
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
