<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Controller;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class AbstractSecurityController extends FrontendController
{
    /**
     * @param Request $request
     * @param Configuration $configuration
     *
     * @return void
     *
     * @throws AccessDeniedHttpException
     */
    protected function performSecurityCheck(Request $request, Configuration $configuration): void
    {
        $securityConfig = $configuration->getSecurityConfig();
        if ($securityConfig['method'] === 'datahub_apikey') {
            $apiKey = $request->get('apikey');
            if ($apiKey === $securityConfig['apikey']) {
                return;
            }
        }

        throw new AccessDeniedHttpException('Permission denied, apikey not valid');
    }
}
