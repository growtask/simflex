<?php
namespace Simflex\Core\DB;

use Simflex\Core\DB\Schema;

interface Migration
{
    public function up(Schema $schema): bool;
    public function down(Schema $schema): bool;
}