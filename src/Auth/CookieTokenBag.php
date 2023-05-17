<?php


namespace Simflex\Auth;


use Simflex\Core\Cookie\CookieBag;
use Simflex\Core\Cookie\CookieItem;

class CookieTokenBag extends CookieBag
{

    const COOKIE_NAME = 'sft';

    /**
     * @return string
     */
    public static function defaultPrefix()
    {
        return SF_LOCATION_SITE == SF_LOCATION ? 's' : 'a';
    }

    public function __construct($prefix)
    {
        parent::__construct($prefix);
        $this->cookies['token'] = new CookieItem($this->prefix . self::COOKIE_NAME);
    }

    public function get()
    {
        return $this->cookies['token']->getValue();
    }

    public function set($token, \DateTime $expires)
    {
        $this->cookies['token']->setValue($token)->setExpires($expires)->save();
    }

    public function delete()
    {
        $this->set('', new \DateTime());
    }


}