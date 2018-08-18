<?php

declare(strict_types=1);

namespace Doctrine\Tests\DBAL\Types;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use PHPUnit\Framework\MockObject\MockObject;
use function get_class;

class DateImmutableTypeTest extends TestCase
{
    /** @var AbstractPlatform|MockObject */
    private $platform;

    /** @var DateImmutableType */
    private $type;

    protected function setUp() : void
    {
        $this->type     = Type::getType('date_immutable');
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    public function testFactoryCreatesCorrectType()
    {
        self::assertSame(DateImmutableType::class, get_class($this->type));
    }

    public function testReturnsName()
    {
        self::assertSame('date_immutable', $this->type->getName());
    }

    public function testReturnsBindingType()
    {
        self::assertSame(ParameterType::STRING, $this->type->getBindingType());
    }

    public function testConvertsDateTimeImmutableInstanceToDatabaseValue()
    {
        $date = $this->createMock(DateTimeImmutable::class);

        $this->platform->expects($this->once())
            ->method('getDateFormatString')
            ->willReturn('Y-m-d');
        $date->expects($this->once())
            ->method('format')
            ->with('Y-m-d')
            ->willReturn('2016-01-01');

        self::assertSame(
            '2016-01-01',
            $this->type->convertToDatabaseValue($date, $this->platform)
        );
    }

    public function testConvertsNullToDatabaseValue()
    {
        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testDoesNotSupportMutableDateTimeToDatabaseValueConversion()
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue(new DateTime(), $this->platform);
    }

    public function testConvertsDateTimeImmutableInstanceToPHPValue()
    {
        $date = new DateTimeImmutable();

        self::assertSame($date, $this->type->convertToPHPValue($date, $this->platform));
    }

    public function testConvertsNullToPHPValue()
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertsDateStringToPHPValue()
    {
        $this->platform->expects($this->once())
            ->method('getDateFormatString')
            ->willReturn('Y-m-d');

        $date = $this->type->convertToPHPValue('2016-01-01', $this->platform);

        self::assertInstanceOf(DateTimeImmutable::class, $date);
        self::assertSame('2016-01-01', $date->format('Y-m-d'));
    }

    public function testResetTimeFractionsWhenConvertingToPHPValue()
    {
        $this->platform->expects($this->any())
            ->method('getDateFormatString')
            ->willReturn('Y-m-d');

        $date = $this->type->convertToPHPValue('2016-01-01', $this->platform);

        self::assertSame('2016-01-01 00:00:00.000000', $date->format('Y-m-d H:i:s.u'));
    }

    public function testThrowsExceptionDuringConversionToPHPValueWithInvalidDateString()
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToPHPValue('invalid date string', $this->platform);
    }

    public function testRequiresSQLCommentHint()
    {
        self::assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }
}
