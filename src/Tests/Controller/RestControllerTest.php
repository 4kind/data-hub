<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Controller\RestController;
use Pimcore\Bundle\DataHubBundle\Helper\ConfigurationHelper;
use Pimcore\Bundle\DataHubBundle\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RestControllerTest extends TestCase
{
    private $controller;
    private $responseHandlerMock;
    private $configurationHelperMock;
    private $requestMock;
    private $configurationMock;

    protected function setUp() : void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Request::class);

        $this->responseHandlerMock = $this->createMock(ResponseHandler::class);
        $this->configurationHelperMock = $this->createMock(ConfigurationHelper::class);

        $this->configurationMock = $this->createMock(Configuration::class);
        $this->configurationMock->method('getSecurityConfig')->willReturn(['method' => 'datahub_apikey', 'apikey' => 'API-KEY-123']);

        $this->configurationHelperMock->method('getConfigurationByName')->willReturn($this->configurationMock);

        $this->requestMock->method('getMethod')->willReturn('GET');

        $this->controller = new RestController();
    }


    public function testRestFulActionWithAuthenticationSuccessWillReturnJson(): void
    {
        $jsonResponse = new JsonResponse();

        $this->requestMock->method('get')->willReturnOnConsecutiveCalls('clientName', 'entityName', 'API-KEY-123');
        $this->responseHandlerMock->method('getJsonResponse')->with($this->configurationMock, 'GET', 'EntityName')->willReturn($jsonResponse);

        $controllerResponse = $this->controller->restFulAction($this->requestMock, $this->responseHandlerMock, $this->configurationHelperMock);

        self::assertSame($jsonResponse, $controllerResponse);
    }

    public function testRestFulActionWithAuthenticationErrorWillThrowException(): void
    {
        $this->requestMock->method('get')->willReturnOnConsecutiveCalls('ClientName', 'EntityName', 'API-KEY-WRONG');

        self::expectException(AccessDeniedHttpException::class);

        $this->controller->restFulAction($this->requestMock, $this->responseHandlerMock, $this->configurationHelperMock);
    }
}
