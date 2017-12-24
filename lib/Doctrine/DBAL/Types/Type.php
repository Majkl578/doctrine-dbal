<?php

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\DBALException;

/**
 * The base class for so-called Doctrine mapping types.
 *
 * A Type object is obtained by calling the static {@link getType()} method.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @since  2.0
 */
abstract class Type implements TypeInterface
{
    public const TARRAY = 'array';
    public const SIMPLE_ARRAY = 'simple_array';
    public const JSON_ARRAY = 'json_array';
    public const JSON = 'json';
    public const BIGINT = 'bigint';
    public const BOOLEAN = 'boolean';
    public const DATETIME = 'datetime';
    public const DATETIME_IMMUTABLE = 'datetime_immutable';
    public const DATETIMETZ = 'datetimetz';
    public const DATETIMETZ_IMMUTABLE = 'datetimetz_immutable';
    public const DATE = 'date';
    public const DATE_IMMUTABLE = 'date_immutable';
    public const TIME = 'time';
    public const TIME_IMMUTABLE = 'time_immutable';
    public const DECIMAL = 'decimal';
    public const INTEGER = 'integer';
    public const OBJECT = 'object';
    public const SMALLINT = 'smallint';
    public const STRING = 'string';
    public const TEXT = 'text';
    public const BINARY = 'binary';
    public const BLOB = 'blob';
    public const FLOAT = 'float';
    public const GUID = 'guid';
    public const DATEINTERVAL = 'dateinterval';

    private const DEFAULT_TYPES_MAP = [
        self::TARRAY => ArrayType::class,
        self::SIMPLE_ARRAY => SimpleArrayType::class,
        self::JSON_ARRAY => JsonArrayType::class,
        self::JSON => JsonType::class,
        self::OBJECT => ObjectType::class,
        self::BOOLEAN => BooleanType::class,
        self::INTEGER => IntegerType::class,
        self::SMALLINT => SmallIntType::class,
        self::BIGINT => BigIntType::class,
        self::STRING => StringType::class,
        self::TEXT => TextType::class,
        self::DATETIME => DateTimeType::class,
        self::DATETIME_IMMUTABLE => DateTimeImmutableType::class,
        self::DATETIMETZ => DateTimeTzType::class,
        self::DATETIMETZ_IMMUTABLE => DateTimeTzImmutableType::class,
        self::DATE => DateType::class,
        self::DATE_IMMUTABLE => DateImmutableType::class,
        self::TIME => TimeType::class,
        self::TIME_IMMUTABLE => TimeImmutableType::class,
        self::DECIMAL => DecimalType::class,
        self::FLOAT => FloatType::class,
        self::BINARY => BinaryType::class,
        self::BLOB => BlobType::class,
        self::GUID => GuidType::class,
        self::DATEINTERVAL => DateIntervalType::class,
    ];

    /** @var TypeRegistry|null */
    private static $typeRegistry;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform);

    /**
     * {@inheritdoc}
     */
    abstract public function getName();

    private static function getRegistry() : TypeRegistry
    {
        if (self::$typeRegistry === null) {
            self::$typeRegistry = new TypeRegistry();

            foreach (self::DEFAULT_TYPES_MAP as $name => $class) {
                self::$typeRegistry->addType($name, $class);
            }
        }

        return self::$typeRegistry;
    }

    /**
     * Factory method to create type instances.
     * Type instances are implemented as flyweights.
     *
     * @param string $name The name of the type (as returned by getName()).
     *
     * @return \Doctrine\DBAL\Types\Type
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getType($name)
    {
        return self::getRegistry()->getType($name);
    }

    /**
     * Adds a custom type to the type map.
     *
     * @param string $name      The name of the type. This should correspond to what getName() returns.
     * @param string $className The class name of the custom type.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function addType($name, $className)
    {
        self::getRegistry()->addType($name, $className);
    }

    /**
     * Checks if exists support for a type.
     *
     * @param string $name The name of the type.
     *
     * @return boolean TRUE if type is supported; FALSE otherwise.
     */
    public static function hasType($name)
    {
        return self::getRegistry()->hasType($name);
    }

    /**
     * Overrides an already defined type to use a different implementation.
     *
     * @param string $name
     * @param string $className
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function overrideType($name, $className)
    {
        self::getRegistry()->overrideType($name, $className);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return \PDO::PARAM_STR;
    }

    /**
     * Gets the types array map which holds all registered types and the corresponding
     * type class
     *
     * @return array
     */
    public static function getTypesMap()
    {
        return self::getRegistry()->getTypesMap();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $e = explode('\\', get_class($this));

        return str_replace('Type', '', end($e));
    }

    /**
     * {@inheritdoc}
     */
    public function canRequireSQLConversion()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return $sqlExpr;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return $sqlExpr;
    }

    /**
     * {@inheritdoc}
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return false;
    }
}
