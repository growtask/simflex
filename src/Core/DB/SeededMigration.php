<?php

namespace Simflex\Core\DB;

abstract class SeededMigration implements Migration
{
    public abstract function seed();
}