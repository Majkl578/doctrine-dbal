<?php

namespace Doctrine\Tests\DBAL\Schema;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Schema\SQLServerSchemaManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Doctrine\DBAL\Schema\SQLServerSchemaManager
 */
final class SQLServerSchemaManagerTest extends TestCase
{
    public function testListTableNames()
    {
        $driverMock = $this->createMock(Driver::class);
        $platform   = $this->createMock(SQLServerPlatform::class);
        $connection = $this
            ->getMockBuilder(Connection::class)
            ->setMethods(['fetchAll'])
            ->setConstructorArgs([['platform' => $platform], $driverMock, new Configuration()])
            ->getMock();

        $connection->expects($this->once())->method('fetchAll')->will(
            $this->returnValue(
                [
                    [
                        'name'        => 'myTable',
                        'schema_name' => 'mySchema',
                    ],
                    [
                        'name'        => 'mySecondTable',
                        'schema_name' => 'mySchema',
                    ],
                    [
                        'name'        => 'myThirdTable',
                        'schema_name' => 'dbo',
                    ],
                    ['name' => 'myFourthTable'],
                ]
            )
        );

        $manager = new SQLServerSchemaManager($connection);

        self::assertSame(
            [
                'mySchema.myTable',
                'mySchema.mySecondTable',
                'myThirdTable',
                'myFourthTable',
            ],
            $manager->listTableNames()
        );
    }
}
