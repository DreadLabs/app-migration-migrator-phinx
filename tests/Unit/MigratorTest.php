<?php

/*
 * This file is part of the AppMigrationMigrator/Phinx package.
 *
 * (c) Thomas Juhnke <dev@van-tomas.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DreadLabs\AppMigrationMigrator\Phinx\Tests\Unit;

use DreadLabs\AppMigration\Exception\MigrationException;
use DreadLabs\AppMigration\Exception\TopologyViolationException;
use DreadLabs\AppMigrationMigrator\Phinx\Migrator;
use DreadLabs\AppMigrationMigrator\Phinx\OutputInterface;
use DreadLabs\AppMigrationMigrator\Phinx\Tests\Fixture\TestAllAdapter;
use DreadLabs\AppMigrationMigrator\Phinx\Tests\Fixture\TestInvalidDirectionAdapter;
use DreadLabs\AppMigrationMigrator\Phinx\Tests\Fixture\TestNoneAdapter;
use DreadLabs\AppMigrationMigrator\Phinx\Tests\Fixture\TestSomeAdapter;
use Phinx\Config\Config;
use Phinx\Config\ConfigInterface;
use Phinx\Db\Adapter\AdapterFactory;

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

    public function testItDoesNotNeedToRunIfAllMigrationsAreExecuted()
    {
        $this->registerTestAdapter(TestAllAdapter::class);

        $config = $this->getConfiguration('phinx_all.yml');

        $migrator = new Migrator($config, $this->output);

        $this->assertFalse($migrator->needsToRun());
    }

    public function testItNeedsToRunIfThereAreSomeUnmigratedMigrations()
    {
        $this->registerTestAdapter(TestSomeAdapter::class);

        $config = $this->getConfiguration('phinx_some.yml');

        $migrator = new Migrator($config, $this->output);

        $this->assertTrue($migrator->needsToRun());
    }

    public function testItAdheresToATopologicalOrder()
    {
        $this->setExpectedException(TopologyViolationException::class);

        $this->registerTestAdapter(TestInvalidDirectionAdapter::class);

        $config = $this->getConfiguration('phinx_all.yml');

        $migrator = new Migrator($config, $this->output);
        $migrator->needsToRun();
        $migrator->migrate();
    }

    public function testItTransformsAdapterExceptionsIntoMigrationException()
    {
        $this->setExpectedException(MigrationException::class, 'Life, the universe and everything.');

        $this->registerTestAdapter(TestSomeAdapter::class);

        $config = $this->getConfiguration('phinx_some_erroneous.yml');

        $migrator = new Migrator($config, $this->output);
        $migrator->needsToRun();
        $migrator->migrate();
    }

    public function testItReturnsTheLatestVersionToMigrateTo()
    {
        $this->registerTestAdapter(TestSomeAdapter::class);

        $config = $this->getConfiguration('phinx_some.yml');

        $migrator = new Migrator($config, $this->output);
        $migrator->needsToRun();

        $this->assertSame(42, $migrator->migrate());
    }
}