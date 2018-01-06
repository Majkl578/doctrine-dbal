<?php

namespace Doctrine\Tests\DBAL\Functional\Ticket;

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\SmallIntType;

/**
 * @group DBAL-752
 */
class DBAL752Test extends \Doctrine\Tests\DbalFunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $platform = $this->_conn->getDatabasePlatform()->getName();

        if (!in_array($platform, array('sqlite'))) {
            $this->markTestSkipped('Related to SQLite only');
        }
    }

    public function testUnsignedIntegerDetection()
    {
        $this->_conn->exec(<<<SQL
CREATE TABLE dbal752_unsigneds (
    small SMALLINT,
    small_unsigned SMALLINT UNSIGNED,
    medium MEDIUMINT,
    medium_unsigned MEDIUMINT UNSIGNED,
    "integer" INTEGER,
    integer_unsigned INTEGER UNSIGNED,
    big BIGINT,
    big_unsigned BIGINT UNSIGNED
);
SQL
        );

        $schemaManager = $this->_conn->getSchemaManager();

        $fetchedTable = $schemaManager->listTableDetails('dbal752_unsigneds');

        self::assertInstanceOf(SmallIntType::class, $fetchedTable->getColumn('small')->getType());
        self::assertInstanceOf(SmallIntType::class, $fetchedTable->getColumn('small_unsigned')->getType());
        self::assertInstanceOf(IntegerType::class, $fetchedTable->getColumn('medium')->getType());
        self::assertInstanceOf(IntegerType::class, $fetchedTable->getColumn('medium_unsigned')->getType());
        self::assertInstanceOf(IntegerType::class, $fetchedTable->getColumn('integer')->getType());
        self::assertInstanceOf(IntegerType::class, $fetchedTable->getColumn('integer_unsigned')->getType());
        self::assertInstanceOf(BigIntType::class, $fetchedTable->getColumn('big')->getType());
        self::assertInstanceOf(BigIntType::class, $fetchedTable->getColumn('big_unsigned')->getType());

        self::assertTrue($fetchedTable->getColumn('small_unsigned')->getUnsigned());
        self::assertTrue($fetchedTable->getColumn('medium_unsigned')->getUnsigned());
        self::assertTrue($fetchedTable->getColumn('integer_unsigned')->getUnsigned());
        self::assertTrue($fetchedTable->getColumn('big_unsigned')->getUnsigned());

        self::assertFalse($fetchedTable->getColumn('small')->getUnsigned());
        self::assertFalse($fetchedTable->getColumn('medium')->getUnsigned());
        self::assertFalse($fetchedTable->getColumn('integer')->getUnsigned());
        self::assertFalse($fetchedTable->getColumn('big')->getUnsigned());
    }
}
