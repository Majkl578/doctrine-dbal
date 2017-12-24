<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * The base interface for so-called Doctrine mapping types.
 */
interface TypeInterface
{
    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     * @param mixed $value The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform);

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     * @param mixed $value The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform);

    /**
     * Gets the default length of this type.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return integer|null
     * @todo Needed?
     */
    public function getDefaultLength(AbstractPlatform $platform);

    /**
     * Gets the SQL declaration snippet for a field of this type.
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform);

    /**
     * Gets the (preferred) binding type for values of this type that
     * can be used when binding parameters to prepared statements.
     * This method should return one of the PDO::PARAM_* constants, that is, one of:
     * PDO::PARAM_BOOL
     * PDO::PARAM_NULL
     * PDO::PARAM_INT
     * PDO::PARAM_STR
     * PDO::PARAM_LOB
     * @return integer
     */
    public function getBindingType();

    /**
     * Does working with this column require SQL conversion functions?
     * This is a metadata function that is required for example in the ORM.
     * Usage of {@link convertToDatabaseValueSQL} and
     * {@link convertToPHPValueSQL} works for any type and mostly
     * does nothing. This method can additionally be used for optimization purposes.
     * @return boolean
     */
    public function canRequireSQLConversion();

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

    /**
     * Gets an array of database types that map to this Doctrine type.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return array
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform);

    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't tell them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return boolean
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform);
}
