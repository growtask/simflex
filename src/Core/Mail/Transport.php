<?php
namespace Simflex\Core\Mail;

/**
 * Mail transport interface
 */
interface Transport
{
    /**
     * Sends the mail
     * @param Mail $mail Mail object
     * @return bool "true" if sent successfully
     */
    public function send(Mail $mail): bool;
}