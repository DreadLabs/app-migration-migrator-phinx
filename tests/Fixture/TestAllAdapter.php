<?php

/*
 * This file is part of the AppMigrationMigrator/Phinx package.
 *
 * (c) Thomas Juhnke <dev@van-tomas.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DreadLabs\AppMigrationMigrator\Phinx\Tests\Fixture;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table;
use Phinx\Db\Table\Column;
use Phinx\Db\Table\ForeignKey;
use Phinx\Db\Table\Index;
use Phinx\Migration\MigrationInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TestAllAdapter
 *
 * @author Thomas Juhnke <dev@van-tomas.de>
 */
class TestAllAdapter implements AdapterInterface
{

    /**
     * @var array
     */
    private $options = array();

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * {@inheritdoc
     */
    public function getVersions()
    {
        return array(
            23,
            42,
            1984,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        return $this->options[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function migrated(MigrationInterface $migration, $direction, $startTime, $endTime)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSchemaTable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createSchemaTable()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapterType()
    {
        return $this->getOption('adapter');
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasTransactions()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function commitTransaction()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute($sql)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function query($sql)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow($sql)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($sql)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function quoteTableName($tableName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function quoteColumnName($columnName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasTable($tableName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createTable(Table $table)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function renameTable($tableName, $newName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dropTable($tableName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($tableName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($tableName, $columnName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn(Table $table, Column $column)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function renameColumn($tableName, $columnName, $newColumnName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function changeColumn($tableName, $columnName, Column $newColumn)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dropColumn($tableName, $columnName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasIndex($tableName, $columns)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addIndex(Table $table, Index $index)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dropIndex($tableName, $columns)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dropIndexByName($tableName, $indexName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasForeignKey($tableName, $columns, $constraint = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addForeignKey(Table $table, ForeignKey $foreignKey)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dropForeignKey($tableName, $columns, $constraint = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnTypes()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function isValidColumnType(Column $column)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlType($type, $limit = null)
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    public function createDatabase($name, $options = array())
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasDatabase($name)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function dropDatabase($name)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function insert(Table $table, $columns, $data)
    {
    }
}
