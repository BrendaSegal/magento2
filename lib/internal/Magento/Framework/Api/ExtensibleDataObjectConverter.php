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

namespace Magento\Framework\Api;

use Magento\Framework\Convert\ConvertArray;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class to convert Extensible Data Object array to flat array
 */
class ExtensibleDataObjectConverter
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(DataObjectProcessor $dataObjectProcessor)
    {
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Convert AbstractExtensibleObject into a nested array.
     *
     * @param ExtensibleDataInterface $dataObject
     * @param string[] $skipCustomAttributes
     * @return array
     */
    public function toNestedArray(ExtensibleDataInterface $dataObject, $skipCustomAttributes = array())
    {
        $dataObjectType = get_class($dataObject);
        $dataObjectArray = $this->dataObjectProcessor->buildOutputDataArray($dataObject, $dataObjectType);
        //process custom attributes if present
        if (!empty($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY])) {
            /** @var AttributeValue[] $customAttributes */
            $customAttributes = $dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY];
            unset ($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY]);
            foreach ($customAttributes as $attributeValue) {
                if (!in_array($attributeValue[AttributeValue::ATTRIBUTE_CODE], $skipCustomAttributes)) {
                    $dataObjectArray[$attributeValue[AttributeValue::ATTRIBUTE_CODE]]
                        = $attributeValue[AttributeValue::VALUE];
                }
            }
        }
        return $dataObjectArray;
    }

    /**
     * Convert AbstractExtensibleObject into flat array.
     *
     * @param ExtensibleDataInterface $dataObject
     * @param string[] $skipCustomAttributes
     * @return array
     */
    public function toFlatArray(ExtensibleDataInterface $dataObject, $skipCustomAttributes = array())
    {
        $dataObjectArray = $this->toNestedArray($dataObject, $skipCustomAttributes);
        return ConvertArray::toFlatArray($dataObjectArray);
    }

    /**
     * Convert AbstractExtensibleObject into flat array.
     *
     * @param AbstractExtensibleObject $dataObject
     * @param string[] $skipCustomAttributes
     * @return array
     * @deprecated use toFlatArray instead. Should be removed once all references are refactored.
     */
    public static function toFlatArrayStatic(AbstractExtensibleObject $dataObject, $skipCustomAttributes = array())
    {
        $dataObjectArray = $dataObject->__toArray();
        //process custom attributes if present
        if (!empty($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY])) {
            /** @var AttributeValue[] $customAttributes */
            $customAttributes = $dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY];
            unset ($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY]);
            foreach ($customAttributes as $attributeValue) {
                if (!in_array($attributeValue[AttributeValue::ATTRIBUTE_CODE], $skipCustomAttributes)) {
                    $dataObjectArray[$attributeValue[AttributeValue::ATTRIBUTE_CODE]]
                        = $attributeValue[AttributeValue::VALUE];
                }
            }
        }
        return ConvertArray::toFlatArray($dataObjectArray);
    }

    /**
     * Convert Extensible Data Object custom attributes in sequential array format.
     *
     * @param array $extensibleObjectData
     * @return array
     */
    public static function convertCustomAttributesToSequentialArray($extensibleObjectData)
    {
        $extensibleObjectData[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY] = array_values(
            $extensibleObjectData[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY]
        );
        return $extensibleObjectData;
    }
}
