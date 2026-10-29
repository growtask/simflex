<?php

namespace Simflex\Core\Extensions\Form;

use Simflex\Core\Http\Request;
use Simflex\Core\System\Container;

/**
 * Защита формы от ботов без внешних сервисов: скрытая ловушка (honeypot) и подписанная метка времени.
 *
 * Форма выводит поле-ловушку (его человек не видит и не заполняет) и токен `{время}.{подпись}`; сервер отклоняет
 * заявку, если ловушка заполнена, подпись неверна, форма отправлена быстрее минимального времени (бот)
 * или токен устарел. Подпись - HMAC от времени с секретом сайта (параметры БД и корень сайта).
 */
class FormGuard
{
    /** Имя поля-ловушки */
    public const TRAP_FIELD = 'website';
    /** Имя поля с токеном */
    public const TOKEN_FIELD = 'form_token';

    /**
     * @param int $minSeconds Минимальное время между показом и отправкой формы
     * @param int $maxSeconds Время жизни токена
     */
    public function __construct(protected int $minSeconds = 3, protected int $maxSeconds = 172800)
    {
    }

    /**
     * Выпускает токен формы
     * @param int|null $time Время выдачи (по умолчанию - сейчас)
     */
    public function issueToken(?int $time = null): string
    {
        $time ??= time();
        return $time . '.' . $this->sign($time);
    }

    /**
     * Проверяет токен и ловушку из запроса
     * @param int|null $now Текущее время (по умолчанию - сейчас)
     * @return bool true, если запрос похож на отправку человеком
     */
    public function check(Request $request, ?int $now = null): bool
    {
        if ((string)$request->post(static::TRAP_FIELD, '') !== '') {
            return false;
        }

        return $this->checkToken((string)$request->post(static::TOKEN_FIELD, ''), $now);
    }

    /**
     * Проверяет подпись и возраст токена
     */
    public function checkToken(string $token, ?int $now = null): bool
    {
        $now ??= time();
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2 || !ctype_digit($parts[0])) {
            return false;
        }

        $time = (int)$parts[0];
        if (!hash_equals($this->sign($time), $parts[1])) {
            return false;
        }

        $age = $now - $time;
        return $age >= $this->minSeconds && $age <= $this->maxSeconds;
    }

    protected function sign(int $time): string
    {
        $cfg = Container::getConfig();
        $secret = implode('|', [$cfg->db['name'] ?? '', $cfg->db['password'] ?? '', Container::getPaths()->getRoot()]);
        return substr(hash_hmac('sha256', (string)$time, $secret), 0, 24);
    }
}
