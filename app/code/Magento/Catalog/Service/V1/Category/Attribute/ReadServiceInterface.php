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
namespace Magento\Catalog\Service\V1\Category\Attribute;

/**
 * Class ReadServiceInterface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @param string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option[]
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     */
    public function options($id);

    /**
     * Get full information about a required attribute with the list of options
     *
     * @param  string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Api\CategoryAttributeRepositoryInterface::get
     */
    public function info($id);

    /**
     * Retrieve the list of product attributes
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults containing Data\Eav\Attribute objects
     * @see \Magento\Catalog\Api\CategoryAttributeRepositoryInterface::getList
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
