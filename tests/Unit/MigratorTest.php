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
use DreadLabs\AppMigration\MigratorInterface;
use Phinx\Config\Config;
use Phinx\Config\ConfigInterface;
use Phinx\Db\Adapter\AdapterFactory;
use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\Manager;
use Phinx\Migration\Manager\Environment;

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
        $config = $this->getConfiguration('phinx_none.yml');

        $migrator = new Migrator($config, $this->output);

        $this->registerTestAdapter(TestNoneAdapter::class, $migrator);

        $this->assertFalse($migrator->needsToRun());
    }

    /**
     * Registers a test adapter
     *
     * @param string $className
     * @param MigratorInterface $migrator
     *
     * @return void
     */
    private function registerTestAdapter($className, MigratorInterface $migrator)
    {
        $reflectedMigrator = new \ReflectionClass($migrator);
        $reflectedManager = $reflectedMigrator->getProperty('manager');
        $reflectedManager->setAccessible(true);

        /* @var $manager Manager */
        $manager = $reflectedManager->getValue($migrator);

        /* @var $environment Environment */
        $environment = $manager->getEnvironment('default');

        $environment->registerAdapter('test', function (Environment $environment) use ($className) {
            /* @var $adapter AdapterInterface */
            $adapter = new $className();
            $adapter->setOptions($environment->getOptions());
            $adapter->setOutput($environment->getOutput());

            return $adapter;
        });
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
        $config = $this->getConfiguration('phinx_all.yml');

        $migrator = new Migrator($config, $this->output);

        $this->registerTestAdapter(TestAllAdapter::class, $migrator);

        $this->assertFalse($migrator->needsToRun());
    }

    /**
     * ItNeedsToRunIfThereAreSomeUnmigratedMigrations
     *
     * @return void
     */
    public function testItNeedsToRunIfThereAreSomeUnmigratedMigrations()
    {
        $config = $this->getConfiguration('phinx_some.yml');

        $migrator = new Migrator($config, $this->output);

        $this->registerTestAdapter(TestSomeAdapter::class, $migrator);

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

        $config = $this->getConfiguration('phinx_all.yml');

        $migrator = new Migrator($config, $this->output);

        $this->registerTestAdapter(TestTopologyViolationAdapter::class, $migrator);

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

        $config = $this->getConfiguration('phinx_some_erroneous.yml');

        $migrator = new Migrator($config, $this->output);

        $this->registerTestAdapter(TestSomeAdapter::class, $migrator);

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

        $migrator = new Migrator($config, $this->output);

        $this->registerTestAdapter(TestSomeAdapter::class, $migrator);

        $migrator->needsToRun();

        $this->assertSame(42, $migrator->migrate());
    }
}
