<?php

namespace Simflex\Telegram;

use Curl\Curl;
use Simflex\Core\Container;
use Simflex\Core\DI\Service;

/**
 * Сервис Telegram API
 */
class TelegramApi implements Service
{
    public static function getServiceName(): string
    {
        return 'telegram';
    }

    /**
     * Очищает произвольный текст, чтобы он был корректным Markdown
     * @param string $in Исходная строка
     * @return string Очищенная строка
     */
    public static function sanitize(string $in): string
    {
        return str_replace(
            ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            [
                '\\_',
                '\\*',
                '\\[',
                '\\]',
                '\\(',
                '\\)',
                '\\~',
                '\\`',
                '\\>',
                '\\#',
                '\\+',
                '\\-',
                '\\=',
                '\\|',
                '\\{',
                '\\}',
                '\\.',
                '\\!',
            ],
            $in
        );
    }

    /**
     * Отправляет сообщение в чат
     * @param string $chatId ID чата
     * @param string $message Сообщение (в формате Markdown)
     * @return bool true, если отправлено успешно
     */
    public function send(string $chatId, string $message): bool
    {
        $cfg = Container::getConfig();

        $ch = new Curl();
        $ch->post('https://api.telegram.org/bot' . $cfg->telegram['token'] . '/sendMessage', [
            'chat_id' => $chatId,
            'parse_mode' => 'MarkdownV2',
            'text' => $message,
        ]);

        $ret = json_decode($ch->getResponse() ?? '', true) ?? [];
        return $ret['ok'] ?? false;
    }
}
