<?php declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\Tests\Rest;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\Rest\WorkspaceValidator;
use Pimcore\Model\DataObject\Concrete;

class WorkspaceValidatorTest extends TestCase
{
    private $workspaceValidator;

    protected function setUp(): void
    {
        $this->workspaceValidator = new WorkspaceValidator();
    }

    public function testIfDataObjectIsInWorkSpacePathAndHasDefinedCrudMethodItWillReturnTrue(): void
    {
        $dataObject = $this->createMock(Concrete::class);
        $dataObject->method('getType')->willReturn('customType');
        $dataObject->method('getFullPath')->willReturn('/path/for/data/object1');

        $workspaces = [
            'customType' => [
                [
                    'cpath' => '/path/for/data',
                    'read' => true
                ]
            ]
        ];

        self::assertTrue($this->workspaceValidator->isInWorkspace($dataObject, $workspaces, 'GET'));
    }

    public function testIfDataObjectIsInWorkSpacePathButAndHasNoDefinedCrudMethodItWillReturnFalse(): void
    {
        $dataObject = $this->createMock(Concrete::class);
        $dataObject->method('getType')->willReturn('customType');
        $dataObject->method('getFullPath')->willReturn('/path/for/data/object1');

        $workspaces = [
            'customType' => [
                [
                    'cpath' => '/path/for/data',
                    'read' => false
                ]
            ]
        ];

        self::assertFalse($this->workspaceValidator->isInWorkspace($dataObject, $workspaces, 'GET'));
    }

    public function testIfDataObjectHasDefinedCrudMethodButIsNotInWorkspacePathItWillReturnFalse(): void
    {
        $dataObject = $this->createMock(Concrete::class);
        $dataObject->method('getType')->willReturn('customType');
        $dataObject->method('getFullPath')->willReturn('/path/for/data/object1');

        $workspaces = [
            'customType' => [
                [
                    'cpath' => '/path/for/data/object2',
                    'read' => true
                ]
            ]
        ];

        self::assertFalse($this->workspaceValidator->isInWorkspace($dataObject, $workspaces, 'GET'));
    }
}
