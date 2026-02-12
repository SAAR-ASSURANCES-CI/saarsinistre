<?php

namespace App\Console;

use Illuminate\Console\Application as BaseApplication;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class FixedArtisanApplication extends BaseApplication
{
    /**
     * Override addCommand to fix the bug where setLaravel() is not called
     * when commands are lazy-loaded via CommandLoader in Symfony 7.4+
     *
     * @param  callable|\Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command|null
     */
    public function addCommand(callable|SymfonyCommand $command): ?SymfonyCommand
    {
        if ($command instanceof Command) {
            $command->setLaravel($this->laravel);
        }

        return parent::addCommand($command);
    }
}
