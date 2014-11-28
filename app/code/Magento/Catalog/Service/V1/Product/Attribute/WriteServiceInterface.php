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
namespace Magento\Catalog\Service\V1\Product\Attribute;

/**
 * Class WriteServiceInterface
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
interface WriteServiceInterface
{
    /**
     * Create attribute from data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     * @deprecated
     * @see \Magento\Catalog\Api\CategoryAttributeRepositoryInterface::save
     */
    public function create(\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attributeMetadata);

    /**
     * Update product attribute process
     *
     * @param  string $id
     * @param  \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attribute
     * @return string
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\CategoryAttributeRepositoryInterface::save
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attribute);

    /**
     * Delete Attribute
     *
     * @param  string $attributeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the entity was deleted (always true)
     * @deprecated
     * @see \Magento\Catalog\Api\CategoryAttributeRepositoryInterface::delete
     */
    public function remove($attributeId);
}
