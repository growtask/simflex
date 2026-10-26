<?php

namespace Simflex\Core\DB;

interface Adapter
{
    /**
     * Устанавливает соединение с базой
     *
     * @return bool Состояние соединения
     */
    public function connect(): bool;

    /**
     * Выполняет SQL-запрос
     *
     * @param string $q SQL-запрос
     * @param array $params Параметры подготовленного запроса
     * @return mixed False при ошибке, иначе объект результата в формате адаптера
     */
    public function query(string $q, array $params = []): mixed;

    /**
     * Получает текущую запись
     *
     * @param mixed $result Объект результата в формате адаптера
     * @return mixed False при ошибке, иначе запись в виде ассоциативного массива
     */
    public function fetch(mixed $result): mixed;

    /**
     * Перемещает указатель результата на заданный индекс
     *
     * @param mixed $result Объект результата в формате адаптера
     * @param int $index Индекс для перехода (0 <= $index <= rowCount)
     * @return bool True при успехе, false при ошибке
     */
    public function seek(&$result, int $index): bool;

    /**
     * Возвращает значение заданного поля
     *
     * @param mixed $result Объект результата в формате адаптера
     * @param int|string $field Имя или индекс столбца
     * @return mixed False при ошибке, иначе значение
     */
    public function result(mixed $result, int|string $field): mixed;

    /**
     * Строит ассоциативный массив
     *
     * @param mixed $result Объект результата в формате адаптера
     * @param bool|string $f1 Имя столбца для ключа или false, чтобы использовать индекс
     * @param bool|string $f2 Имя второго столбца для вложенных записей
     * @return bool|array Массив с результатами
     */
    public function assoc(mixed $result, bool|string $f1, bool|string $f2): bool|array;

    /**
     * Возвращает ID последней вставки
     *
     * @return string ID последней вставки
     */
    public function insertId(): string;

    /**
     * Возвращает код последней ошибки SQL
     *
     * @return int Код последней ошибки
     */
    public function errno(): int;

    /**
     * Возвращает текст ошибки или null, если адаптер не задал код ошибки
     *
     * @return string|null Сообщение об ошибке
     */
    public function error(): ?string;

    /**
     * Возвращает код ошибки SQLSTATE
     */
    public function errorCode(): ?string;

    /**
     * Возвращает отформатированное сообщение об ошибке
     *
     * @return string Сообщение об ошибке
     */
    public function errorPrepared(): string;

    /**
     * Экранирует строку
     *
     * @param string $str Строка для экранирования
     * @return string Экранированная строка
     */
    public function escape(string $str): string;

    /**
     * Возвращает число записей, затронутых последним запросом
     *
     * @return int Число записей
     */
    public function affectedRows(): int;

    /**
     * Начинает транзакцию
     */
    public function beginTransaction(): void;

    /**
     * Фиксирует транзакцию
     */
    public function commitTransaction(): void;

    /**
     * Откатывает транзакцию
     */
    public function rollbackTransaction(): void;
}
