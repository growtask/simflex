<?php

namespace Simflex\Core;

/**
 * The panel a module is displayed in, see ModuleBase::moduleType()
 */
enum ModuleType: string
{
    case Site = 'site';
    case Admin = 'admin';
}
