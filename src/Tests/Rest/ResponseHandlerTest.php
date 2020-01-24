<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Tests\Rest;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Rest\DataProvider;
use Pimcore\Bundle\DataHubBundle\Rest\ResponseHandler;

class ResponseHandlerTest extends TestCase
{
    /**
     * @var MockObject|ResponseHandler
     */
    private $responseHandler;
    private $dataProviderMock;

    protected function setUp(): void
    {
        $this->dataProviderMock = $this->createMock(DataProvider::class);

        $this->responseHandler = $this->getMockBuilder(ResponseHandler::class)->setMethods(['getList'])->setConstructorArgs([$this->dataProviderMock])->getMock();
    }

    public function testIfClassWasNotFoundJsonWithErrorIsReturned(): void
    {
        $configurationMock = $this->createMock(Configuration::class);

        $this->responseHandler->method('getList')->willThrowException(new \Exception);

        $response = $this->responseHandler->getJsonResponse($configurationMock, 'GET', 'entityClassName');

        self::assertSame(404, $response->getStatusCode());
        self::assertSame([
            'success' => false,
            'message' => ''
        ], json_decode($response->getContent(), true));
    }

    public function testJsonIsSuccessfullyReturned(): void
    {
        $configurationMock = $this->createMock(Configuration::class);

        $this->dataProviderMock->method('getListAsArray')->willReturn(['DATA1', 'DATA2']);

        $response = $this->responseHandler->getJsonResponse($configurationMock, 'GET', 'entityClassName');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame([
            'success' => true,
            'total' => 2,
            'data' => [
                'DATA1',
                'DATA2'
            ]
        ], json_decode($response->getContent(), true));
    }
}
