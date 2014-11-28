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
namespace Magento\Reports\Model\Resource\Report\Product\Viewed;

/**
 * @magentoAppArea adminhtml
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reports\Model\Resource\Report\Product\Viewed\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reports\Model\Resource\Report\Product\Viewed\Collection'
        );
        $this->_collection->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1));
    }

    /**
     * @magentoDataFixture Magento/Reports/_files/viewed_products.php
     */
    public function testGetItems()
    {
        $expectedResult = array(1 => 3, 2 => 1, 21 => 2);
        $actualResult = array();
        /** @var \Magento\Reports\Model\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[$reportItem->getData('product_id')] = $reportItem->getData('views_num');
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider tableForPeriodDataProvider
     *
     * @param $period
     * @param $expectedTable
     * @param $dateFrom
     * @param $dateTo
     * @param $isTotal
     */
    public function testTableSelection($period, $expectedTable, $dateFrom, $dateTo, $isTotal = false)
    {
        $dbTableName = $this->_collection->getTable($expectedTable);
        $this->_collection->setPeriod($period);
        if ($isTotal != false) {
            $this->_collection->setAggregatedColumns(array('id'));
            $this->_collection->isTotals(true);
        }
        $this->_collection->setDateRange($dateFrom, $dateTo);
        $this->_collection->load();
        $from = $this->_collection->getSelect()->getPart('from');

        if ($isTotal != false) {
            $this->assertArrayHasKey('t', $from);
            $this->assertArrayHasKey('tableName', $from['t']);
        } elseif (!empty($from) && is_array($from)) {
            $this->assertArrayHasKey($dbTableName, $from);
            $actualTable = $from[$dbTableName]['tableName'];
            $this->assertEquals($dbTableName, $actualTable);
            $this->assertArrayHasKey('tableName', $from[$dbTableName]);
        } else {
            $union = $this->_collection->getSelect()->getPart('union');
            if (!is_null($period) && !is_null($dateFrom) && !is_null($dateTo) && $period != 'month') {
                $count = count($union);
                if ($period == 'year') {
                    if ($dbTableName == "report_viewed_product_aggregated_daily") {
                        $this->assertEquals($count, 2);
                    }
                    if ($dbTableName == "report_viewed_product_aggregated_yearly") {
                        $this->assertEquals($count, 3);
                    }
                } else {
                    $this->assertEquals($count, 3);
                }
            } else {
                $this->assertEquals(count($union), 2);
            }
        }
    }


    /**
     * Data provider for testTableSelection
     *
     * @return array
     */
    public function tableForPeriodDataProvider()
    {
        $dateNow = date('Y-m-d', time());
        $dateYearAgo = date('Y-m-d', strtotime($dateNow . ' -1 year'));
        return array(
            [
                'period'    => 'year',
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => null,
                'date_to'   => null,
                'is_total'  => true,
            ],
            [
                'period'    => 'year',
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateNow,
            ],
            [
                'period'    => 'year',
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => $dateYearAgo,
                'date_to'   => null,
            ],
            [
                'period'    => 'month',
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => null,
                'date_to'   => $dateNow,
            ],
            [
                'period'    => 'year',
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => $dateYearAgo,
                'date_to'   => null,
            ],
            [
                'period'    => 'year',
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => null,
                'date_to'   => $dateNow,
            ],
            [
                'period'    => 'month',
                'table'     => 'report_viewed_product_aggregated_monthly',
                'date_from' => null,
                'date_to'   => null,
            ],
            [
                'period'    => 'month',
                'table'     => 'report_viewed_product_aggregated_monthly',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateYearAgo,
            ],
            [
                'period'    => 'month',
                'table'     => 'report_viewed_product_aggregated_monthly',
                'date_from' => null,
                'date_to'   => $dateYearAgo,
            ],
            [
                'period'    => 'month',
                'table'     => 'report_viewed_product_aggregated_monthly',
                'date_from' => $dateYearAgo,
                'date_to'   => null,
            ],
            [
                'period'    => 'day',
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => null,
                'date_to'   => null,
            ],
            [
                'period'    => 'undefinedPeriod',
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => null,
                'date_to'   => null,
            ],
            [
                'period'    => null,
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateNow,
            ],
            [
                'period'    => null,
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => $dateNow,
                'date_to'   => $dateNow,
            ],
            [
                'period'    => 'day',
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateYearAgo,
            ],
            [
                'period'    => 'year',
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateYearAgo,
            ],
            [
                'period'    => 'year',
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => null,
                'date_to'   => $dateYearAgo,
            ],
            [
                'period'    => null,
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => null,
                'date_to'   => null,
            ]
        );
    }
}
