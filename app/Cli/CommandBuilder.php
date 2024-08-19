<?php

namespace MythicalSystemsFramework\Cli;

interface CommandBuilder
{
    /**
     * Execute the command.
     *
     * @param bool $isFrameworkCommand Is it a framework command?
     * @param array $args the arguments passed to the command
     *
     * @return void it should return nothing as it is a void function
     */
    public static function execute(bool $isFrameworkCommand, array $args): void;
}
