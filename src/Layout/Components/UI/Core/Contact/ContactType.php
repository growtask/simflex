<?php

namespace Simflex\Core\Layout\Components\UI\Core\Contact;

enum ContactType: string
{
    case Phone = 'phone';
    case Email = 'email';
    case Custom = 'custom';
}
