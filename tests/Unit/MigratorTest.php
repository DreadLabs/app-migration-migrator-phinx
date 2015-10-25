<?php

/*
 * This file is part of the AppMigration\Migrator\Phinx package.
 *
 * (c) Thomas Juhnke <dev@van-tomas.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DreadLabs\AppMigration\Migrator\Phinx\Tests\Unit;

use DreadLabs\AppMigration\Exception\MigrationException;
use DreadLabs\AppMigration\Exception\TopologyViolationException;
use DreadLabs\AppMigration\Migrator\Phinx\Migrator;
use DreadLabs\AppMigration\Migrator\Phinx\OutputInterface;
use DreadLabs\AppMigration\Migrator\Phinx\Tests\Fixture\TestAllAdapter;
use DreadLabs\AppMigration\Migrator\Phinx\Tests\Fixture\TestNoneAdapter;
use DreadLabs\AppMigration\Migrator\Phinx\Tests\Fixture\TestSomeAdapter;
use DreadLabs\AppMigration\Migrator\Phinx\Tests\Fixture\TestTopologyViolationAdapter;
use Phinx\Config\Config;
use Phinx\Config\ConfigInterface;
use Phinx\Db\Adapter\AdapterFactory;
use Phinx\Migration\Manager;

/**
 * MigratorTest
 *
 * @author Thomas Juhnke <dev@van-tomas.de>
 */
class MigratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var OutputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $output;

    public function setUp()
    {
        $this->output = $this->getMock(OutputInterface::class);
    }

    /**
     * ItDoesNotNeedToRunIfMigratedVersionsAndAvailableVersionsAreEmpty
     *
     * @return void
     */
    public function testItDoesNotNeedToRunIfMigratedVersionsAndAvailableVersionsAreEmpty()
    {
        $this->registerTestAdapter(TestNoneAdapter::class);

        $config = $this->getConfiguration('phinx_none.yml');

        $migrator = new Migrator($config, $this->output);

        $this->assertFalse($migrator->needsToRun());
    }

    /**
     * Registers a test adapter
     *
     * @param string $className
     *
     * @return void
     */
    private function registerTestAdapter($className)
    {
        AdapterFactory::instance()->registerAdapter(
            'test',
            $className
        );
    }

    /**
     * Loads and returns a phinx configuration
     *
     * @param string $fileName
     *
     * @return ConfigInterface
     */
    private function getConfiguration($fileName)
    {
        return Config::fromYaml(
            __DIR__ . '/../Fixture/' . $fileName
        );
    }

    /**
     * ItDoesNotNeedToRunIfAllMigrationsAreExecuted
     *
     * @return void
     */
    public function testItDoesNotNeedToRunIfAllMigrationsAreExecuted()
    {
        $this->registerTestAdapter(TestAllAdapter::class);

        $config = $this->getConfiguration('phinx_all.yml');

        $migrator = new Migrator($config, $this->output);

        $this->assertFalse($migrator->needsToRun());
    }

    /**
     * ItNeedsToRunIfThereAreSomeUnmigratedMigrations
     *
     * @return void
     */
    public function testItNeedsToRunIfThereAreSomeUnmigratedMigrations()
    {
        $this->registerTestAdapter(TestSomeAdapter::class);

        $config = $this->getConfiguration('phinx_some.yml');

        $migrator = new Migrator($config, $this->output);

        $this->assertTrue($migrator->needsToRun());
    }

    /**
     * ItAdheresToATopologicalOrder
     *
     * @return void
     *
     * @throws MigrationException
     * @throws TopologyViolationException
     */
    public function testItAdheresToATopologicalOrder()
    {
        $this->setExpectedException(TopologyViolationException::class);

        $this->registerTestAdapter(TestTopologyViolationAdapter::class);

        $config = $this->getConfiguration('phinx_all.yml');

        $migrator = new Migrator($config, $this->output);

        $migrator->needsToRun();
        $migrator->migrate();
    }

    /**
     * ItTransformsAdapterExceptionsIntoMigrationException
     *
     * @return void
     *
     * @throws MigrationException
     * @throws TopologyViolationException
     */
    public function testItTransformsAdapterExceptionsIntoMigrationException()
    {
        $this->setExpectedException(MigrationException::class, 'Life, the universe and everything.');

        $this->registerTestAdapter(TestSomeAdapter::class);

        $config = $this->getConfiguration('phinx_some_erroneous.yml');

        $migrator = new Migrator($config, $this->output);

        $migrator->needsToRun();
        $migrator->migrate();
    }

    /**
     * ItReturnsTheLatestVersionToMigrateTo
     *
     * @return void
     *
     * @throws MigrationException
     * @throws TopologyViolationException
     */
    public function testItReturnsTheLatestVersionToMigrateTo()
    {
        $config = $this->getConfiguration('phinx_some.yml');

        $this->registerTestAdapter(TestSomeAdapter::class);

        $migrator = new Migrator($config, $this->output);

        $migrator->needsToRun();

        $this->assertSame(42, $migrator->migrate());
    }
}
