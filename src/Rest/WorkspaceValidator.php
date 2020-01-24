<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Rest;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\HttpFoundation\Request;

class WorkspaceValidator
{
    private const CRUD_MAPPING = [
        Request::METHOD_POST => 'create',
        Request::METHOD_GET => 'read',
        Request::METHOD_PUT => 'update',
        Request::METHOD_DELETE => 'delete'
    ];

    public function isInWorkspace(Concrete $dataObject, array $workspaces, string $requestMethod): bool
    {
        $isInWorkspace = false;
        $type = $dataObject->getType();
        $dataObjectPath = $dataObject->getFullPath();
        $workspaces = $workspaces[$type] ?? [];

        foreach ($workspaces as $workspace) {
            if ($this->dataObjectIsInWorkspacePath($dataObjectPath, $workspace['cpath'])
                && $this->dataObjectHasWorkspaceCrudMethod($workspace, $requestMethod)) {
                $isInWorkspace = true;
                break;
            }
        }

        return $isInWorkspace;
    }

    private function dataObjectIsInWorkspacePath(string $dataObjectPath, string $cPath): bool
    {
        return substr($dataObjectPath, 0, strlen($cPath)) === $cPath;
    }

    private function dataObjectHasWorkspaceCrudMethod(array $workspace, string $requestMethod): bool
    {
        $crudMethod = self::CRUD_MAPPING[$requestMethod] ?? false;
        return $crudMethod && isset($workspace[$crudMethod]) && $workspace[$crudMethod] === true;
    }
}
