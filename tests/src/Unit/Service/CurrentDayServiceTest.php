<?php

namespace Drupal\Tests\modifiedpageoftheday\Unit\Service;

use Drupal\modifiedpageoftheday\Service\CurrentDayService;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\node\NodeInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\Form\FormStateInterface;
use SebastianBergmann\PeekAndPoke\Proxy;
use Drupal\Core\Entity\Query\QueryInterface;

/**
 * @coversDefaultClass \Drupal\modifiedpageoftheday\Service\CurrentDayService
 * @group modifiedpageoftheday
 */
class CurrentDayServiceTest extends UnitTestCase {

    public function testConstruct() {

        $mock = $this->getMockBuilder(CurrentDayService::class)
                ->setMethods(NULL)
                ->disableOriginalConstructor()
                ->getMock();

        $mockEntityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
        $mockTime              = $this->createMock(TimeInterface::class);

        // assertions
        $mock->__construct($mockEntityTypeManager, $mockTime);

        $proxyMock = new Proxy($mock);

        $this->assertSame($proxyMock->entityTypeManager, $mockEntityTypeManager);
        $this->assertSame($proxyMock->time, $mockTime);
    }

    public function dataTestGetCurrentDayCutoffTimestamp() {
        return [
            [strtotime('2019-03-10 16:42:42'), strtotime('2019-03-10 00:00:00')],
            [strtotime('2019-03-11 00:00:00'), strtotime('2019-03-11 00:00:00')],
            [strtotime('2019-03-12 00:00:01'), strtotime('2019-03-12 00:00:00')],
            [strtotime('2019-03-13 23:59:59'), strtotime('2019-03-13 00:00:00')],
        ];
    }

    /**
     * 
     * @dataProvider dataTestGetCurrentDayCutoffTimestamp
     * @param type $requestTime
     * @param type $expected
     */
    public function testGetCurrentDayCutoffTimestamp($requestTime, $expected) {
        $mock = $this->getMockBuilder(CurrentDayService::class)
                ->setMethods(NULL)
                ->disableOriginalConstructor()
                ->getMock();

        $mockTime = $this->getMockBuilder(TimeInterface::class)
                ->setMethods([
                    'getRequestTime'
                ])
                ->getMockForAbstractClass();

        $proxyMock = new Proxy($mock);

        $proxyMock->time = $mockTime;

        // expectations

        $mockTime->expects($this->once())
                ->method('getRequestTime')
                ->willReturn($requestTime);

        // assertions

        $actual = $proxyMock->getCurrentDayCutoffTimestamp();

        $this->assertEquals($actual, $expected);
    }

}
