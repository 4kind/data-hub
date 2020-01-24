<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Helper;

use Pimcore\Bundle\DataHubBundle\Configuration;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfigurationHelper
{
    public function getConfigurationByName(string $clientName): Configuration
    {
        $configuration = $this->getConfiguration($clientName);

        if (!$configuration || !$configuration->isActive()) {
            throw new NotFoundHttpException(
                sprintf('No active configuration found for "%s"', $clientName)
            );
        }
        return $configuration;
    }

    protected function getConfiguration(string $clientName): Configuration
    {
        return Configuration::getByName($clientName);
    }
}
