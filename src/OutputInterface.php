<?php

/*
 * This file is part of the AppMigrationMigrator\Phinx package.
 *
 * (c) Thomas Juhnke <dev@van-tomas.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DreadLabs\AppMigrationMigrator\Phinx;

/**
 * OutputInterface
 *
 * Wrapper around the Symfony OutputInterface in order to play nicely with
 * frameworks with limited support for injecting dependencies on a class level.
 *
 * @author Thomas Juhnke <dev@van-tomas.de>
 */
interface OutputInterface extends \Symfony\Component\Console\Output\OutputInterface
{
}
