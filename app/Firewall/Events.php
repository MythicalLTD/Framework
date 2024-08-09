<?php

namespace MythicalSystemsFramework\Firewall;

interface Events
{
    public const DROP = 'drop';
    public const ALLOW = 'allow';
    public const NONE = 'none';
}
