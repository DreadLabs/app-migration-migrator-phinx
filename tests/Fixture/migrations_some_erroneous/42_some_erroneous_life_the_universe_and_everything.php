<?php

/*
 * This file is part of the AppMigrationMigrator/Phinx package.
 *
 * (c) Thomas Juhnke <dev@van-tomas.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

/**
 * SomeErroneousLifeTheUniverseAndEverything
 *
 * @author Thomas Juhnke <dev@van-tomas.de>
 */
class SomeErroneousLifeTheUniverseAndEverything extends AbstractMigration
{

    /**
     * Does nothing
     *
     * @return void
     */
    public function up()
    {
        throw new \PDOException('Life, the universe and everything.');
    }
}
