<?php

namespace Drupal\Tests\modifiedpageoftheday\Unit\Plugin\Block;

use Drupal\modifiedpageoftheday\Plugin\Block\CurrentModifiedBlock;
use Drupal\modifiedpageoftheday\Service\CurrentDayService;
use Drupal\node\NodeInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\Form\FormStateInterface;
use SebastianBergmann\PeekAndPoke\Proxy;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * @coversDefaultClass \Drupal\modifiedpageoftheday\Plugin\Block\CurrentModifiedBlock
 * @group modifiedpageoftheday
 */
class CurrentModifiedBlockTest extends UnitTestCase {

    /**
     * Data provider for testBuild.
     * @return array 
     */
    public function dataTestBuild() {
        return [
            [['page_limit' => 1], 1],
            [['page_limit' => null], 5],
            [[], 5],
            [['foo' => 'bar'], 5],
        ];
    }

    /**
     * Tests build().
     * 
     * @dataProvider dataTestBuild
     * @param array $mockConfig
     * @param int $expectedLimit
     */
    public function testBuild(array $mockConfig, $expectedLimit) {

        /** @var \PHPUnit\Framework\MockObject\Builder\InvocationMocker $mock */
        $mock                  = $this->getMockBuilder(CurrentModifiedBlock::class)
                ->disableOriginalConstructor()
                ->setMethods([
                    'getConfiguration',
                    'getCurrentDayService',
                ])
                ->getMock();
        /** @var \PHPUnit\Framework\MockObject\Builder\InvocationMocker $mockCurrentDayService */
        $mockCurrentDayService = $this->getMockBuilder(CurrentDayService::class)
                ->disableOriginalConstructor()
                ->setMethods([
                    'fetchCurrentlyModifiedNodes'
                ])
                ->getMock();

        $mockPosts = [
            $this->createMock(NodeInterface::class),
        ];

        // expectations

        $mock->expects($this->once())
                ->method('getConfiguration')
                ->willReturn($mockConfig);
        $mock->expects($this->once())
                ->method('getCurrentDayService')
                ->willReturn($mockCurrentDayService);

        $mockCurrentDayService->expects($this->once())
                ->method('fetchCurrentlyModifiedNodes')
                ->with($expectedLimit)
                ->willReturn($mockPosts);

        // run & assert

        $actual = $mock->build();

        $this->assertArrayEquals([
            '#theme' => 'block_modifiedpageoftheday',
            '#cache' => [
                'max-age' => 3, // lower value for demo.
                'context' => [
                    // some nodes may be not be accessible to all users.
                    'user.permissions'
                ],
            ],
            '#posts' => $mockPosts
                ], $actual);
    }

    /**
     * Tests blockForm().
     * 
     * @dataProvider dataTestBuild
     * @param array $mockConfig
     * @param int $expectedLimit
     */
    public function testBlockForm(array $mockConfig, $expectedLimit) {
        /** @var \PHPUnit\Framework\MockObject\Builder\InvocationMocker $mock */
        $mock = $this->getMockBuilder(CurrentModifiedBlock::class)
                ->disableOriginalConstructor()
                ->setMethods([
                    'getConfiguration',
                    'parent',
                ])
                ->getMock();

        $mockFormState = $this->createMock(FormStateInterface::class);
        $form          = [];

        // expectations
        $mock->expects($this->once())
                ->method('getConfiguration')
                ->willReturn($mockConfig);
        $mock->expects($this->once())
                ->method('parent')
                ->with('blockForm', $form, $mockFormState)
                ->willReturn($form); // no change
        // assert
        $actual = $mock->blockForm($form, $mockFormState);

        $this->assertArrayHasKey('page_limit', $actual);
        $this->assertArrayEquals([
            '#type'          => 'number',
            '#default_value' => $expectedLimit,
            '#min'           => 1,
            '#max'           => 50,
            '#step'          => 1,
            '#title'         => 'Page Limit',
            '#title'         => 'Max number of currently day posts to show',
                ], $actual['page_limit']);
    }

    /**
     * Tests blockSumbit()
     */
    public function testBlockSubmit() {
        /** @var \PHPUnit\Framework\MockObject\Builder\InvocationMocker $mock */
        $mock = $this->getMockBuilder(CurrentModifiedBlock::class)
                ->disableOriginalConstructor()
                ->setMethods([
                    'parent',
                ])
                ->getMock();

        $mockFormState = $this->getMockBuilder(FormStateInterface::class)
                ->setMethods(['getValues'])
                ->getMockForAbstractClass();
        $form          = [];
        $mockValues    = [
            'page_limit' => 42,
        ];

        // expectations
        $mock->expects($this->once())
                ->method('parent')
                ->with('blockSubmit', $form, $mockFormState);
        $mockFormState->expects($this->once())
                ->method('getValues')
                ->willReturn($mockValues);

        // assert
        $mock->blockSubmit($form, $mockFormState);
        $proxyMock = new Proxy($mock);
        $this->assertEquals(42, $proxyMock->configuration['page_limit']);
    }

    public function testGetCurrentDayService() {

        $fakeContainer         = new ContainerBuilder();
        $mockCurrentDayService = $this->createMock(CurrentDayService::class);
        $fakeContainer->set('modifiedpageoftheday.currentday', $mockCurrentDayService);
        \Drupal::setContainer($fakeContainer);
        $mock                  = $this->getMockBuilder(CurrentModifiedBlock::class)
                ->disableOriginalConstructor()
                ->setMethods(NULL)
                ->getMock();

        $mockProxy = new Proxy($mock);

        $actual = $mockProxy->getCurrentDayService();

        $this->assertSame($actual, $mockCurrentDayService);
    }

}
