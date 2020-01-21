<?php

declare(strict_types=1);

namespace TravelOrganizer\Zend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ZendBridgeInterface
{
    public function executeZend(Request $request, string $controller, string $action): Response;
}
