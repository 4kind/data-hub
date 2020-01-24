<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Tests\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Helper\ConfigurationHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfigurationHelperTest extends TestCase
{
    /**
     * @var MockObject|ConfigurationHelper
     */
    private $configurationHelper;

    protected function setUp() : void
    {
        $this->configurationHelper = $this->createPartialMock(ConfigurationHelper::class, ['getConfiguration']);
    }

    public function testNoConfigurationThrowsException(): void
    {
        self::expectException(NotFoundHttpException::class);

        $this->configurationHelper->getConfigurationByName('clientName');
    }

    public function testReturnConfigurationFound()
    {
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->method('isActive')->willReturn(true);

        $this->configurationHelper->method('getConfiguration')->willReturn($configurationMock);

        $return = $this->configurationHelper->getConfigurationByName('clientName');

        self::assertSame($return, $configurationMock);
    }

    public function testInactiveConfigurationThrowsException()
    {
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->method('isActive')->willReturn(false);

        $this->configurationHelper->method('getConfiguration')->willReturn($configurationMock);

        self::expectException(NotFoundHttpException::class);

        $this->configurationHelper->getConfigurationByName('clientName');
    }
}
