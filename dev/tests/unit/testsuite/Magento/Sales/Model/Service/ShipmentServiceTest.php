<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Sales\Model\Service;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ShipmentServiceTest
 */
class ShipmentServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Repository
     *
     * @var \Magento\Sales\Api\ShipmentCommentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentRepositoryMock;

    /**
     * Search Criteria Builder
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaBuilderMock;

    /**
     * Filter Builder
     *
     * @var \Magento\Framework\Api\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * Repository
     *
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * Shipment Notifier
     *
     * @var \Magento\Shipping\Model\ShipmentNotifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $notifierMock;

    /**
     * @var \Magento\Sales\Model\Service\ShipmentService
     */
    protected $shipmentService;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $objectManager = new ObjectManagerHelper($this);

        $this->commentRepositoryMock = $this->getMockForAbstractClass(
            'Magento\Sales\Api\ShipmentCommentRepositoryInterface',
            ['getList'],
            '',
            false
        );
        $this->criteriaBuilderMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            ['create', 'addFilter'],
            [],
            '',
            false
        );
        $this->filterBuilderMock = $this->getMock(
            'Magento\Framework\Api\FilterBuilder',
            ['setField', 'setValue', 'create'],
            [],
            '',
            false
        );
        $this->repositoryMock = $this->getMockForAbstractClass(
            'Magento\Sales\Api\ShipmentRepositoryInterface',
            ['get'],
            '',
            false
        );
        $this->notifierMock = $this->getMock(
            'Magento\Shipping\Model\ShipmentNotifier',
            ['notify'],
            [],
            '',
            false
        );

        $this->shipmentService = $objectManager->getObject(
            'Magento\Sales\Model\Service\ShipmentService',
            [
                'commentRepository' => $this->commentRepositoryMock,
                'criteriaBuilder' => $this->criteriaBuilderMock,
                'filterBuilder' => $this->filterBuilderMock,
                'repository' => $this->repositoryMock,
                'notifier' => $this->notifierMock,
            ]
        );
    }

    /**
     * Run test getLabel method
     */
    public function testGetLabel()
    {
        $id = 145;
        $returnValue = 'return-value';

        $shipmentMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment',
            ['getShippingLabel'],
            [],
            '',
            false
        );

        $this->repositoryMock->expects($this->once())
            ->method('get')
            ->with($id)
            ->will($this->returnValue($shipmentMock));
        $shipmentMock->expects($this->once())
            ->method('getShippingLabel')
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->shipmentService->getLabel($id));
    }

    /**
     * Run test getCommentsList method
     */
    public function testGetCommentsList()
    {
        $id = 25;
        $returnValue = 'return-value';

        $filterMock = $this->getMock(
            'Magento\Framework\Api\Filter',
            [],
            [],
            '',
            false
        );
        $searchCriteriaMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteria',
            [],
            [],
            '',
            false
        );

        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with('parent_id')
            ->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with($id)
            ->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($filterMock));
        $this->criteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(['eq' => $filterMock]);
        $this->criteriaBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($searchCriteriaMock));
        $this->commentRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->shipmentService->getCommentsList($id));
    }

    /**
     * Run test notify method
     */
    public function testNotify()
    {
        $id = 123;
        $returnValue = 'return-value';

        $modelMock = $this->getMockForAbstractClass(
            'Magento\Sales\Model\AbstractModel',
            [],
            '',
            false
        );

        $this->repositoryMock->expects($this->once())
            ->method('get')
            ->with($id)
            ->will($this->returnValue($modelMock));
        $this->notifierMock->expects($this->once())
            ->method('notify')
            ->with($modelMock)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->shipmentService->notify($id));
    }
}
