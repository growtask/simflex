<?php
namespace Simflex\Core\DB\Schema;

interface Element
{
    /**
     * Builds output SQL
     */
    public function toString(): string;

    /**
     * Returns element name
     */
    public function getName(): string;
}