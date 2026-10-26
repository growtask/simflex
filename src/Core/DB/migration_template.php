<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        // $s->createTable('blog', function (Schema\Table $c) {
        //     $c->id('blog_id');
        //     $c->string('name');
        // });
        // начальные данные, записываются после создания таблиц:
        // $s->seed(fn () => \App\Models\Blog::bulkInsert([['name' => 'First']]));
    }

    public function down(Schema $s): void
    {
    }
};
