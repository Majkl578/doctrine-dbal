<?php

namespace Doctrine\Tests\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MySqlPointType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return strtoupper(self::class);
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return array('point');
    }
}
