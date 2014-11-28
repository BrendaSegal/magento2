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
namespace Magento\Catalog\Model\Product\Option\Type\File;

use Magento\Framework\App\Filesystem\DirectoryList;

abstract class Validator
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\File\Size
     */
    protected $fileSize;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Size $fileSize
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Size $fileSize
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->fileSize = $fileSize;
    }

    /**
     * Store Config value
     *
     * @param string $key Config value key
     * @return string
     */
    protected function getConfigData($key)
    {
        return $this->scopeConfig->getValue(
            'catalog/custom_options/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Error messages for validator Errors
     *
     * @param string[] $errors Array of validation failure message codes @see \Zend_Validate::getErrors()
     * @param array $fileInfo File info
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string[] Array of error messages
     * @see \Magento\Catalog\Model\Product\Option\Type\File::_getValidatorErrors
     */
    protected function getValidatorErrors($errors, $fileInfo, $option)
    {
        $result = array();
        foreach ($errors as $errorCode) {
            switch($errorCode) {
                case \Zend_Validate_File_ExcludeExtension::FALSE_EXTENSION:
                    $result[] = __(
                        "The file '%1' for '%2' has an invalid extension.",
                        $fileInfo['title'],
                        $option->getTitle()
                    );
                    break;
                case \Zend_Validate_File_Extension::FALSE_EXTENSION:
                    $result[] = __(
                        "The file '%1' for '%2' has an invalid extension.",
                        $fileInfo['title'],
                        $option->getTitle()
                    );
                    break;
                case \Zend_Validate_File_ImageSize::WIDTH_TOO_BIG:
                case \Zend_Validate_File_ImageSize::HEIGHT_TOO_BIG:
                    $result[] = __(
                        "Maximum allowed image size for '%1' is %2x%3 px.",
                        $option->getTitle(),
                        $option->getImageSizeX(),
                        $option->getImageSizeY()
                    );
                    break;
                case \Zend_Validate_File_FilesSize::TOO_BIG:
                    $result[] = __(
                        "The file '%1' you uploaded is larger than the %2 megabytes allowed by our server.",
                        $fileInfo['title'],
                        $this->fileSize->getMaxFileSizeInMb()
                    );
                    break;
            }
        }
        return $result;
    }

    /**
     * Parse file extensions string with various separators
     *
     * @param string $extensions String to parse
     * @return array|null
     * @see \Magento\Catalog\Model\Product\Option\Type\File::_parseExtensionsString
     */
    protected function parseExtensionsString($extensions)
    {
        if (preg_match_all('/(?<extension>[a-z0-9]+)/si', strtolower($extensions), $matches)) {
            return $matches['extension'] ?: null;
        }
        return null;
    }

    /**
     * @param \Zend_File_Transfer_Adapter_Http|\Zend_Validate $object
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param array $fileFullPath
     * @return \Zend_File_Transfer_Adapter_Http|\Zend_Validate $object
     * @throws NotImageException
     */
    protected function buildImageValidator($object, $option, $fileFullPath = null)
    {
        $dimensions = array();

        if ($option->getImageSizeX() > 0) {
            $dimensions['maxwidth'] = $option->getImageSizeX();
        }
        if ($option->getImageSizeY() > 0) {
            $dimensions['maxheight'] = $option->getImageSizeY();
        }
        if (count($dimensions) > 0) {
            if (!is_null($fileFullPath) && !$this->isImage($fileFullPath)) {
                throw new NotImageException();
            }
            $object->addValidator(new \Zend_Validate_File_ImageSize($dimensions));
        }

        // File extension
        $allowed = $this->parseExtensionsString($option->getFileExtension());
        if ($allowed !== null) {
            $object->addValidator(new \Zend_Validate_File_Extension($allowed));
        } else {
            $forbidden = $this->parseExtensionsString($this->getConfigData('forbidden_extensions'));
            if ($forbidden !== null) {
                $object->addValidator(new \Zend_Validate_File_ExcludeExtension($forbidden));
            }
        }

        $object->addValidator(
            new \Zend_Validate_File_FilesSize(array('max' => $this->fileSize->getMaxFileSize()))
        );
        return $object;
    }

    /**
     * Simple check if file is image
     *
     * @param array|string $fileInfo - either file data from \Zend_File_Transfer or file path
     * @return boolean
     * @see \Magento\Catalog\Model\Product\Option\Type\File::_isImage
     */
    protected function isImage($fileInfo)
    {
        // Maybe array with file info came in
        if (is_array($fileInfo)) {
            return strstr($fileInfo['type'], 'image/');
        }

        // File path came in - check the physical file
        if (!$this->rootDirectory->isReadable($this->rootDirectory->getRelativePath($fileInfo))) {
            return false;
        }
        $imageInfo = getimagesize($fileInfo);
        if (!$imageInfo) {
            return false;
        }
        return true;
    }
}
