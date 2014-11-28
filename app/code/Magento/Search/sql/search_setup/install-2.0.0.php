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

$installer = $this;
/* @var $installer \Magento\Framework\Module\Setup */

$installer->startSetup();

/**
 * Create table 'search_query'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('search_query'))
    ->addColumn(
        'query_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Query ID'
    )
    ->addColumn(
        'query_text',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'Query text'
    )
    ->addColumn(
        'num_results',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Num results'
    )
    ->addColumn(
        'popularity',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Popularity'
    )
    ->addColumn(
        'redirect',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'Redirect'
    )
    ->addColumn(
        'synonym_for',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'Synonym for'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Store ID'
    )
    ->addColumn(
        'display_in_terms',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['nullable' => false, 'default' => '1'],
        'Display in terms'
    )
    ->addColumn(
        'is_active',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['default' => '1'],
        'Active status'
    )
    ->addColumn(
        'is_processed',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['default' => '0'],
        'Processed status'
    )
    ->addColumn(
        'updated_at',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false],
        'Updated at'
    )
    ->addIndex(
        $installer->getIdxName('search_query', ['query_text', 'store_id', 'popularity']),
        ['query_text', 'store_id', 'popularity']
    )
    ->addIndex(
        $installer->getIdxName('search_query', 'store_id'),
        'store_id'
    )
    ->addIndex(
        $installer->getIdxName('search_query', 'synonym_for'),
        'synonym_for'
    )
    ->addForeignKey(
        $installer->getFkName('search_query', 'store_id', 'store', 'store_id'),
        'store_id',
        $installer->getTable('store'),
        'store_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Search query table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
