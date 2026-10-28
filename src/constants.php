<?php

/*
 * Окружения приложения. Константу SF_LOCATION (одно из них) задаёт Application::init() до загрузки сайта:
 * код ядра, расширений и сайта может на неё полагаться без проверки defined()
 */
const SF_LOCATION_SITE = 'site';
const SF_LOCATION_CLI = 'cli';
const SF_LOCATION_ADMIN = 'admin';
const SF_LOCATION_API = 'api';

const SF_CORE_ROOT_PATH = __DIR__;
