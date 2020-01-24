<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Tests\Rest;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Rest\DataProvider;
use Pimcore\Bundle\DataHubBundle\Rest\WorkspaceValidator;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Listing\AbstractListing;

class DataProviderTest extends TestCase
{
    private $dataProvider;
    private $workspaceValidatorMock;
    private $abstractListingMock;
    private $configurationMock;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->workspaceValidatorMock = $this->createMock(WorkspaceValidator::class);
        $this->abstractListingMock = $this->getMockForAbstractClass(AbstractListing::class);
        $this->configurationMock = $this->createMock(Configuration::class);

        $this->dataProvider = new DataProvider($this->workspaceValidatorMock);
    }

    public function testReturnDataAsArrayWithConfiguredAttributes(): void
    {
        $columnConfig = [
            'columns' => [
                [
                    'attributes' => [
                        'attribute' => 'type'
                    ]
                ]
            ]
        ];

        $this->configurationMock->method('getQueryColumnConfig')->willReturn($columnConfig);
        $this->workspaceValidatorMock->method('isInWorkspace')->willReturn(true);

        $concrete = new Concrete();
        $concrete->setType('specialCustom');
        $concrete->setId(123);

        $this->abstractListingMock->setData([$concrete]);

        $data = $this->dataProvider->getListAsArray($this->configurationMock, $this->abstractListingMock, 'entityName', 'GET');

        self::assertSame([
            [
                'id' => 123,
                'type' => 'specialCustom'
            ]
        ], $data);
    }

    public function testReturnDataWithIdIfNoAttributesAreConfigured(): void
    {
        $columnConfig = [
            'columns' => [
                [

                ]
            ]
        ];

        $this->configurationMock->method('getQueryColumnConfig')->willReturn($columnConfig);
        $this->workspaceValidatorMock->method('isInWorkspace')->willReturn(true);

        $concrete = new Concrete();
        $concrete->setId(123);

        $this->abstractListingMock->setData([$concrete]);

        $data = $this->dataProvider->getListAsArray($this->configurationMock, $this->abstractListingMock, 'entityName', 'GET');

        self::assertSame([
            [
                'id' => 123
            ]
        ], $data);
    }
}
