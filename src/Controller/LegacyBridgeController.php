<?php

namespace TravelOrganizer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TravelOrganizer\Zend\ZendBridgeInterface;

final class LegacyBridgeController
{
    private $zendBridge;

    public function __construct(ZendBridgeInterface $zendBridge)
    {
        $this->zendBridge = $zendBridge;
    }

    public function __invoke(Request $request, string $zendController, string $zendAction): Response
    {
        return $this->zendBridge->executeZend($request, $zendController, $zendAction);
    }
}
