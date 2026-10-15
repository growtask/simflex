<?php

namespace Simflex\Core\DB;

interface Migration
{
    public function up(Schema $schema): void;

    public function down(Schema $schema): void;
}
