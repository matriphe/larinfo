<?php

namespace Matriphe\Larinfo\Entities;

interface OperatingSystemContract
{
    public const OS_UNKNOWN = 'Unknown';
    public const OS_MAC = 'MacOS';
    public const OS_WINDOWS = 'Windows';

    public const NAME_WINDOWS = 'Microsoft Windows';

    public const DISTRO_NAME = 'name';
    public const DISTRO_VERSION = 'version';
}
