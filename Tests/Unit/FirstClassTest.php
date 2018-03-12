<?php
namespace Helhum\ExtScaffold\Tests\Unit;

use Helhum\ExtScaffold\Tests\Unit\Fixtures\LoadableClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class FirstClassTest extends UnitTestCase
{
    /**
     * @test
     */
    public function methodReturnsTrue()
    {
        $firstClassObject = new LoadableClass();
        $this->assertTrue($firstClassObject->returnsTrue());
    }

    /**
     * @test
     */
    public function viewHelperBaseClassIsLoadable()
    {
        $this->assertTrue(class_exists('TYPO3\\CMS\\Fluid\\Tests\\Unit\\ViewHelpers\\ViewHelperBaseTestcase'));
    }
}
