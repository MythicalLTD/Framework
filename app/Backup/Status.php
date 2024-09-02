<?php

namespace MythicalSystemsFramework\Backup;

interface Status
{
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const FAILED = 'FAILED';
    public const DONE = 'DONE';
}