<?php

namespace Simflex\Admin\Structure;

interface FieldType
{
    public static function typeLabel(): string;

    public static function typeParams(): array;
}
