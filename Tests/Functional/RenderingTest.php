<?php
namespace Helhum\ExtScaffold\Tests\Functional;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel <helmut.hummel@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\Response;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class RenderingTest
 */
class RenderingTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = array('typo3conf/ext/ext_scaffold');

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = array('fluid');

    public function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/Fixtures/Database/pages.xml');
        $this->setUpFrontendRootPage(1, array('EXT:ext_scaffold/Tests/Functional/Fixtures/Frontend/Basic.ts'));
    }

    /**
     * @test
     */
    public function emailViewHelperWorksWithSpamProtection()
    {
        $requestArguments = array('id' => '1');
        $expectedContent = '<a href="javascript:linkTo_UnCryptMailto(\'ocknvq,kphqBjgnjwo0kq\');">info(AT)helhum(DOT)io</a>' . chr(10);
        $this->assertSame($expectedContent, $this->fetchFrontendResponse($requestArguments)->getContent());
    }



    /* ***************
     * Utility methods
     * ***************/



    /**
     * @param array $requestArguments
     * @param bool $failOnFailure
     * @return Response
     */
    protected function fetchFrontendResponse(array $requestArguments, $failOnFailure = true)
    {
        if (!empty($requestArguments['url'])) {
            $requestUrl = '/' . ltrim($requestArguments['url'], '/');
        } else {
            $requestUrl = '/?' . GeneralUtility::implodeArrayForUrl('', $requestArguments);
        }

        $arguments = array(
            'documentRoot' => getenv('TYPO3_PATH_ROOT'),
            'requestUrl' => $requestUrl,
        );

        $vendor = ORIGINAL_ROOT . '/vendor/';
        $template = new \Text_Template($vendor . 'typo3/testing-framework/Resources/Core/Functional/Fixtures/Frontend/request.tpl');
        $template->setVar(
            array(
                'arguments' => var_export($arguments, true),
                'vendorPath' => $vendor,
            )
        );

        $php = \PHPUnit\Util\PHP\AbstractPhpProcess::factory();
        $response = $php->runJob($template->render());

        $result = json_decode($response['stdout'], true);

        if ($result === null) {
            $this->fail('Frontend Response is empty');
        }

        if ($failOnFailure && $result['status'] === Response::STATUS_Failure) {
            $this->fail('Frontend Response has failure:' . LF . $result['error']);
        }

        $response = new Response($result['status'], $result['content'], $result['error']);
        return $response;
    }
}
