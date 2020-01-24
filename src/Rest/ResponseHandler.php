<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Rest;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Listing\AbstractListing;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseHandler
{
    private $dataProvider;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    private const DATA_OBJECT_NAMESPACE = "\\Pimcore\\Model\\DataObject\\";

    public function getJsonResponse(Configuration $configuration, string $requestMethod, string $entity): JsonResponse
    {
        try {
            $listing = $this->getList($entity);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $this->dataProvider->getListAsArray($configuration, $listing, $entity, $requestMethod);

        return new JsonResponse([
            'success' => true,
            'total' => count($data),
            'data' => $data
        ]);
    }

    /**
     * @param string $entity
     * @return AbstractListing
     * @throws \Exception
     */
    protected function getList(string $entity): AbstractListing
    {
        return Concrete::getList([
            'class' => self::DATA_OBJECT_NAMESPACE . $entity
        ]);
    }
}
