<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

interface SQLConvertedType
{
    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     * @param string $sqlExpr
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform);

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a PHP value.
     * @param string $sqlExpr
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function convertToPHPValueSQL($sqlExpr, $platform);
}
