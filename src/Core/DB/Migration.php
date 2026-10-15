<?php

namespace Simflex\Core\DB;

use Simflex\Core\DB\Schema;

interface Migration
{
    public function up(Schema $schema): void;

    public function down(Schema $schema): void;
}
