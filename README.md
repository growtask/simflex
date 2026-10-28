# Simflex CMS — ядро

Composer-пакет `growtask/simflex-core`: загрузка сайта, админка, ORM, миграции, консоль, cron, почта, компоненты
вёрстки. Namespace — `Simflex\Core\`. PHP 8.4, MySQL 8 / MariaDB 10.5.

Отдельно ядро не запускается: сайт создаётся из шаблона
[simflex-template](https://github.com/growtask/simflex-template), ядро ставится в него composer:

```bash
composer create-project -s dev growtask/simflex-template mysite
```

```json
"require": {
    "growtask/simflex-core": "4.0.x-dev"
}
```

## Версии

| Ветка | Версия | Пакет |
|-------|--------|-------|
| `master` | 4.0 (`4.0.x-dev`) | `growtask/simflex-core` |
| `v3` | 3.x (теги `3.*`) | `growtask/simflex` — для сайтов на 3.x |

## Устройство

| Каталог `src/` | Что внутри |
|----------------|------------|
| `Kernel.php` | загрузка и запуск сайта (точки входа сайта: `new Kernel(<корень>)->runHttp()`, `->runConsole()`) |
| `System/` | контейнер, события, конфиг, настройки сайта, реестры в коде, базовые модель и модуль |
| `Http/` | запрос и ответ, маршруты, страница сайта, SEO и статика, меню сайта, шаблоны |
| `Admin/` | админка: страница, меню, разделы, поля, тема |
| `DB/`, `Models/` | ORM, миграции, модели таблиц ядра |
| `Extensions/` | компоненты и модули ядра (страницы, формы, меню, хлебные крошки, сообщения) |
| `Console/`, `Cron/`, `Mail/`, `Sms/`, `Layout/`, … | подсистемы |

## Документация

Репозиторий [simflex-docs](https://git.et9.ru/growtask/simflex-docs): установка, устройство сайта, админка,
ORM, расширения. Изменения ядра — в [CHANGELOG.md](CHANGELOG.md). Автотесты ядра — в шаблоне сайта.

## Лицензия

MIT.
