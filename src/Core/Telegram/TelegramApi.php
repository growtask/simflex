<?php

namespace Simflex\Core\Telegram;

use Curl\Curl;
use Simflex\Core\Container;
use Simflex\Core\DI\Service;

/**
 * Telegram API service
 */
class TelegramApi implements Service
{
    public static function getServiceName(): string
    {
        return 'telegram';
    }

    /**
     * Sanitize arbitrary input to be a valid Markdown text
     * @param string $in Input string
     * @return string Sanitized string
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
                '\\!'
            ],
            $in
        );
    }

    /**
     * Send message to a chat
     * @param string $chatId Chat ID
     * @param string $message Message (Markdown format)
     * @return bool "true" if sent successfully
     */
    public function send(string $chatId, string $message): bool
    {
        $cfg = Container::getConfig();

        $ch = new Curl();
        $ch->post('https://api.telegram.org/bot' . $cfg->telegram['token'] . '/sendMessage', [
            'chat_id' => $chatId,
            'parse_mode' => 'MarkdownV2',
            'text' => $md
        ]);

        $ret = json_decode($ch->getResponse() ?? '', true) ?? [];
        return $ret['ok'] ?? false;
    }
}