<?php

namespace Simflex\DB\Schema;

interface Element
{
    /**
     * Строит итоговый SQL
     */
    public function toString(): string;

    /**
     * Возвращает имя элемента
     */
    public function getName(): string;
}
