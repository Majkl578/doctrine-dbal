<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Types;

final class DefaultTypes
{
    public const TARRAY               = 'array';
    public const SIMPLE_ARRAY         = 'simple_array';
    public const JSON_ARRAY           = 'json_array';
    public const JSON                 = 'json';
    public const BIGINT               = 'bigint';
    public const BOOLEAN              = 'boolean';
    public const DATETIME             = 'datetime';
    public const DATETIME_IMMUTABLE   = 'datetime_immutable';
    public const DATETIMETZ           = 'datetimetz';
    public const DATETIMETZ_IMMUTABLE = 'datetimetz_immutable';
    public const DATE                 = 'date';
    public const DATE_IMMUTABLE       = 'date_immutable';
    public const TIME                 = 'time';
    public const TIME_IMMUTABLE       = 'time_immutable';
    public const DECIMAL              = 'decimal';
    public const INTEGER              = 'integer';
    public const OBJECT               = 'object';
    public const SMALLINT             = 'smallint';
    public const STRING               = 'string';
    public const TEXT                 = 'text';
    public const BINARY               = 'binary';
    public const BLOB                 = 'blob';
    public const FLOAT                = 'float';
    public const GUID                 = 'guid';
    public const DATEINTERVAL         = 'dateinterval';

    private function __construct()
    {
    }
}
