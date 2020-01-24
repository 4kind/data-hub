<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Controller;

use Pimcore\Bundle\DataHubBundle\Helper\ConfigurationHelper;
use Pimcore\Bundle\DataHubBundle\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RestController extends AbstractSecurityController
{
    /**
     * @Route("/pimcore-rest-webservices/{clientname}/{entity}", name="admin_pimcoredatahub_webservice_rest")
     */
    public function restFulAction(Request $request, ResponseHandler $responseHandler, ConfigurationHelper $configurationHelper): JsonResponse
    {
        $clientName = $request->get('clientname');
        $entity = ucfirst($request->get('entity'));
        $requestMethod = $request->getMethod();
        $configuration = $configurationHelper->getConfigurationByName($clientName);

        $this->performSecurityCheck($request, $configuration);

        return $responseHandler->getJsonResponse($configuration, $requestMethod, $entity);
    }
}
