<?php

declare(strict_types=1);

namespace TravelOrganizer\Zend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TraceableZendBridge implements ZendBridgeInterface
{
    private $zendBridge;
    private $cacheDirectory;
    private $zendTraces = [];

    public function __construct(ZendBridgeInterface $zendBridge, string $cacheDirectory)
    {
        $this->zendBridge = $zendBridge;
        $this->cacheDirectory = $cacheDirectory;
    }

    public function executeZend(Request $request, string $controller, string $action): Response
    {
        $fileId = uniqid('xdebug-trace-', true);
        $filename = sprintf('%s/%s', $this->cacheDirectory, $fileId);
        @mkdir($this->cacheDirectory);

        xdebug_start_code_coverage();
        xdebug_start_trace($filename);

        $response = $this->zendBridge->executeZend($request, $controller, $action);

        xdebug_stop_trace();
        file_put_contents($filename.'-coverage.php', '<?php'.PHP_EOL.var_export(xdebug_get_code_coverage(), true));
        xdebug_stop_code_coverage();

        $this->zendTraces[] = $filename;

        return $response;
    }

    public function getZendTraces(): array
    {
        return $this->zendTraces;
    }
}
