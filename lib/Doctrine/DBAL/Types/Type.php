<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

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
    public const TARRAY = BuiltInTypes::TARRAY;
    public const SIMPLE_ARRAY = BuiltInTypes::SIMPLE_ARRAY;
    public const JSON_ARRAY = BuiltInTypes::JSON_ARRAY;
    public const JSON = BuiltInTypes::JSON;
    public const BIGINT = BuiltInTypes::BIGINT;
    public const BOOLEAN = BuiltInTypes::BOOLEAN;
    public const DATETIME = BuiltInTypes::DATETIME;
    public const DATETIME_IMMUTABLE = BuiltInTypes::DATETIME_IMMUTABLE;
    public const DATETIMETZ = BuiltInTypes::DATETIMETZ;
    public const DATETIMETZ_IMMUTABLE = BuiltInTypes::DATETIMETZ_IMMUTABLE;
    public const DATE = BuiltInTypes::DATE;
    public const DATE_IMMUTABLE = BuiltInTypes::DATE_IMMUTABLE;
    public const TIME = BuiltInTypes::TIME;
    public const TIME_IMMUTABLE = BuiltInTypes::TIME_IMMUTABLE;
    public const DECIMAL = BuiltInTypes::DECIMAL;
    public const INTEGER = BuiltInTypes::INTEGER;
    public const OBJECT = BuiltInTypes::OBJECT;
    public const SMALLINT = BuiltInTypes::SMALLINT;
    public const STRING = BuiltInTypes::STRING;
    public const TEXT = BuiltInTypes::TEXT;
    public const BINARY = BuiltInTypes::BINARY;
    public const BLOB = BuiltInTypes::BLOB;
    public const FLOAT = BuiltInTypes::FLOAT;
    public const GUID = BuiltInTypes::GUID;
    public const DATEINTERVAL = BuiltInTypes::DATEINTERVAL;

    private const BUILT_IN_TYPES_MAP = [
        BuiltInTypes::TARRAY => ArrayType::class,
        BuiltInTypes::SIMPLE_ARRAY => SimpleArrayType::class,
        BuiltInTypes::JSON_ARRAY => JsonArrayType::class,
        BuiltInTypes::JSON => JsonType::class,
        BuiltInTypes::OBJECT => ObjectType::class,
        BuiltInTypes::BOOLEAN => BooleanType::class,
        BuiltInTypes::INTEGER => IntegerType::class,
        BuiltInTypes::SMALLINT => SmallIntType::class,
        BuiltInTypes::BIGINT => BigIntType::class,
        BuiltInTypes::STRING => StringType::class,
        BuiltInTypes::TEXT => TextType::class,
        BuiltInTypes::DATETIME => DateTimeType::class,
        BuiltInTypes::DATETIME_IMMUTABLE => DateTimeImmutableType::class,
        BuiltInTypes::DATETIMETZ => DateTimeTzType::class,
        BuiltInTypes::DATETIMETZ_IMMUTABLE => DateTimeTzImmutableType::class,
        BuiltInTypes::DATE => DateType::class,
        BuiltInTypes::DATE_IMMUTABLE => DateImmutableType::class,
        BuiltInTypes::TIME => TimeType::class,
        BuiltInTypes::TIME_IMMUTABLE => TimeImmutableType::class,
        BuiltInTypes::DECIMAL => DecimalType::class,
        BuiltInTypes::FLOAT => FloatType::class,
        BuiltInTypes::BINARY => BinaryType::class,
        BuiltInTypes::BLOB => BlobType::class,
        BuiltInTypes::GUID => GuidType::class,
        BuiltInTypes::DATEINTERVAL => DateIntervalType::class,
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

            foreach (self::BUILT_IN_TYPES_MAP as $name => $class) {
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
