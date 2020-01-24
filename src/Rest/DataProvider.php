<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Rest;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Listing\AbstractListing;

class DataProvider
{
    private $workspaceValidator;

    public function __construct(WorkspaceValidator $workspaceValidator)
    {
        $this->workspaceValidator = $workspaceValidator;
    }

    public function getListAsArray(Configuration $configuration, AbstractListing $listing, string $entity, string $requestMethod): array
    {
        $data = [];

        $columnsConfigs = $configuration->getQueryColumnConfig($entity)['columns'] ?? [];
        $workspaces = $configuration->getConfiguration()['workspaces'] ?? [];

        /** @var Concrete $dataObject */
        foreach ($listing as $dataObject) {
            if ($this->workspaceValidator->isInWorkspace($dataObject, $workspaces, $requestMethod)) {
                $object = [];
                foreach ($columnsConfigs as $columnsConfig) {
                    $object['id'] = $dataObject->getId();

                    $attribute = $columnsConfig['attributes']['attribute'] ?? '';

                    if ($attribute) {
                        $object[$attribute] = $this->getValue($dataObject, $attribute);
                    }


                }
                $data[] = $object;
            }
        }

        return $data;
    }

    /**
     * @return mixed|null
     */
    private function getValue(Concrete $dataObject, string $attribute)
    {
        $value = null;
        $getter = sprintf('get%s', ucfirst($attribute));
        if (method_exists($dataObject, $getter)) {
            $value = $dataObject->$getter();
        }
        return $value;
    }
}
