# Журнал изменений

Все заметные изменения ядра Simflex CMS (`growtask/simflex`). Формат — [Keep a Changelog](https://keepachangelog.com/ru/1.1.0/),
версии — [SemVer](https://semver.org/lang/ru/). Журнал ведётся с версии 4.0; изменения шаблона сайта — в `CHANGELOG.md`
репозитория `simflex-template`.

## [4.0.0] — не выпущена

Изменения относительно 3.6.0. Главное: структура админки, привилегии, меню, шаблоны страниц, компоненты, модули
и задачи cron объявляются в коде, а не хранятся в базе; раздел админки — один класс; работа с базой — через ORM.

### Несовместимые изменения

- **PHP 8.4** (`"php": "^8.4"`), обязательно расширение `mbstring`.
- **Структура админки в коде.** Таблицы `struct_table`, `struct_table_right`, `struct_field`, `struct_field_param`,
  `struct_param`, `struct_data`, их модели и разделы «Структура» удалены. Раздел админки описывается классом
  (см. «Разделы админки» ниже).
- **Компоненты, модули, параметры модулей** объявляются в коде: списки классов в `provider/components.php`
  и `provider/modules.php` приложения (`\Simflex\Core\ExtensionRegistry`), настройки — статическими методами класса
  (`extensionTitle()`, `modulePostexec()`, `adminParams()`). Таблицы `component`, `component_param`, `module`,
  `module_param` и их модели удалены; `menu.component` и `module_item.module` хранят имя класса.
- **Привилегии в коде:** `\Simflex\Core\Privilege` с константами `DEV`, `ADMIN`; приложение расширяет реестр наследником
  (конфиг `privilegeClass`). Роли, пользователи, меню и виджеты ссылаются на привилегию по имени (колонка `privilege`),
  а не по id. Привилегии `admin` и `simplex_admin` объединены в `Privilege::ADMIN`; `dev` имеет все привилегии
  и не выключается. Таблица `user_privilege` необязательна — хранит только переключённую в админке активность.
  Нейминг `priv` заменён на `privilege` (модели `UserRolePrivilege`, `UserPrivilegePersonal`).
- **Меню админки в коде:** `\Simflex\Admin\Menu` (пункты `MenuItem`), приложение расширяет наследником
  (конфиг `adminMenuClass`). Таблица `admin_menu`, модель `AdminMenu` и раздел «Меню» в «Управлении» удалены.
- **Модули админки** (боковое меню, хлебные крошки) — `\Simflex\Admin\AdminModules` (конфиг `adminModulesClass`),
  в `module_item` хранятся только модули сайта; `ModuleType` и `moduleType()` удалены.
- **Шаблоны страниц в коде:** `provider/templates.php` приложения (`ContentTemplate`, `ContentTemplateBlock`,
  `ContentTemplates`); страница хранит ключ шаблона (`content.template`). Таблицы `content_template`,
  `content_template_param` и их модели удалены.
- **Задачи cron в коде:** `provider/cron.php` (`\Simflex\Core\Cron\CronRegistry`, `CronJob`); таблица `cron` хранит
  только активность и расписание.
- **Сидеров больше нет:** начальные данные пишет миграция, создающая таблицу (`$s->seed(callable)`). Удалены
  `Seeder`, `Cli\Seeder` (`./sf seed/run`), модель и таблица `seeder`, атрибут `Manual`.
- **Вывод возвращается строкой:** `content()` компонентов, модулей, полей и разделов админки возвращает HTML,
  а не печатает его (`render()` вместо `include`).
- **Разделы админки переименованы и переразложены:** `Admin\Base` → `Admin\Table\Table` (абстрактный),
  классы-драйверы `Admin\Base\*` и `Extensions\Content\Admin\AdminContent` влиты в разделы `Admin\Handlers\*`,
  модуль `Admin\Modules\Account` → раздел `Admin\Handlers\Account`, `Admin\Plugins\Pagecontrol` → `Pagination`.
- **Геттеры называются с `get`/`is`:** у `Table`, `Repository`, `Menu`, `AdminModules`, `Privilege`
  (`getFields()`, `getParams()`, `Menu::getItems()`, `Privilege::getAll()` и т.д.).
- **Удалён устаревший код:** `Core\User`, `Core\UserInstance` (и колонки `user.hash`, `user.hash_admin`),
  `Core\Auth`, `Core\Service` (→ `Helpers\Tree`, `Str::translite()`), `Core\Alert\Console\Alert`, `DB::bind()`,
  `@SITE_PATH`, `Core::ajax()/uri()/uri_r()/path()/getComponentAPI()/component*()`, `Profiler::start()/output()`,
  `Time::sub_time()`, `BaseMiddleware::setUserModelClass()` (→ `Factory::override()`), `AQ::selectColumn()`,
  `AQ::asScalar()` (→ `AQ::fetchColumn()`), `Admin\InstallBase`.
- **Удалены методы, на которые могли опираться сайты и расширения:** `Alert::output()` админки и сайта
  (→ `echo Alert::getHTML()`), конструктор `ExtensionBase` и свойство `$rootDir`
  (→ `getRootDir()`, не зависит от конструктора; наследникам компонентов и модулей не вызывать
  `parent::__construct()` — это фатальная ошибка), `Admin\Plugins\UI\UI::treeView()`, `FieldImage::loadUI()`,
  `UserRole::addPriv()`, `Chain::getUserModelClass()`, `ModelContent::aqModifyWithTemplate()` (шаблон страницы —
  ключ в `content.template`, см. `ContentTemplates`).
- **Корень веб-сервера — каталог `public/` сайта;** статика ядра публикуется в `public/cache/assets/<hash>/`
  (`\Simflex\Core\Assets`), исходники ядра и сайта снаружи недоступны.
- **Настройки расширений** лежат в `provider/extensions/` сайта (было `provider/extension/`); в пакете —
  `src/provider/extensions/`.
- **Адрес админки** задаётся конфигом `adminPath` (по умолчанию `/admin`), ссылки меню — относительно него.
- Лог по умолчанию пишется в `log/` (`ConfigBase::$logPath`), в файл — при `logToFile`.
- **Связи записи** (`FieldMultiKey`, `FieldRelation`) у новой записи до вставки ничего не пишут: `Table::save()`
  сохраняет их после вставки, по id записи (`Field::$isSavedById`). Обход `getPOST()` после `parent::save()`
  в разделах не нужен.
- **`FieldMultiKey` без особых случаев каталога:** колонка таблицы связей с id записи — параметр `relation_key`
  (было: `link_id`, если ключ раздела и выбираемых записей — `product_id`), подпись «Имя (Родитель)» — параметр
  `parent_key` (было: только у категорий на таблице `catalog_product`).
- **Загрузка файлов в поля-таблицы** (`FieldTable`, `FieldVirtualTable`) — действие раздела `upload_table_file`
  (`Table::uploadTableFile()`): белый список расширений, случайное имя, каталог `/uf/images|files/<$uploadDir>/`,
  в поле строки — адрес файла. Раньше изображения колонок уходили в `upload_img` (`/uf/images/content/source/`),
  файлы — в `upload_file` (в поле — только имя), а файлы `FieldVirtualTable` — на адрес формы, где их принимал
  `Handlers\Content::ajaxUpload()` (удалён вместе с переопределением `content()`).

### Добавлено

- **Разделы админки в коде.** `\Simflex\Admin\Handler` — раздел (`content()`), `\Simflex\Admin\Table\Table` —
  табличный раздел. Таблица объявляется в самом классе: `protected static string $model` (обязательно),
  `getFields()`, `getParams()`, `$defaultOrderBy`, `$defaultOrderDesc`, `$privilegeAdd/Edit/Delete`, `$perPage`;
  поведение — методы того же класса. Реестр разделов — `Admin/handlers.php` ядра и приложения.
- `FieldDefinition` с параметрами поля именованными аргументами, `ParamDefinition`, `ParamGroup`;
  пресеты полей `IdField`, `ActiveField`, `NppField`, `ParentField`, `ForeignKeyField`.
- Разделы без таблицы: «Привилегии» (список и переключатель), «Аккаунт», страница группы меню с виджетами
  (`Handlers\Group`).
- Разделы «Привилегии ролей» и «Персональные привилегии» (только для разработчика).
- **Расширения объявляют свои сущности:** `ExtensionConfig` — компоненты (`$components`), модули (`$modules`),
  разделы админки (`$handlers`), задачи cron (`getCronJobs()`), привилегии (`getPrivileges()`), пункты меню админки
  (`getAdminMenu()`); `ExtensionConfig::getAll()` — загруженные расширения в порядке `loadOrder`. Ядро объединяет их
  с объявлениями сайта, сайт главнее: его наследник заменяет класс расширения, задача с тем же ключом — задачу.
- Маршруты, сервисы, события и консольные команды расширений (`$routes`, `$services`, `$events`, `$commands`
  `ExtensionConfig`) подключаются: `ConfigBase::getRoutes()`, `getServices()`, `getEvents()`, `getCommands()`
  объединяют их с `provider/*.php` сайта; маршрут, команда и сервис сайта с тем же именем главнее, слушатели
  одного события складываются. Конфиг сайта больше не собирает их сам (в шаблоне убраны `loadExtensions()`,
  `loadFiles()` и кэш `cache/*.php`, установщик пакетов кэш не чистит).
- Установщик пакетов копирует миграции пакета в сайт следующими свободными номерами сайта и не дублирует уже
  скопированные (`PackageInstaller::copyMigrations()`).
- `Menu::add(..., parent:, before:)`, `remove()`, `setHandler()` — свой обработчик пункта меню ядра;
  `AdminModules` с привязкой модулей к разделам (`sections`).
- `\Simflex\Core\CodeRegistry` — общий базовый класс реестров в коде (`Privilege`, `Menu`, `AdminModules`).
- Поля выбора из реестров (привилегия, компонент, модуль, задача cron, шаблон страницы) — выпадающий список с поиском,
  как у внешнего ключа.
- `\Simflex\Core\View` — вывод шаблона в строку; HTML полей админки вынесен в `Admin/Fields/tpl/*.tpl`.
- `\Simflex\Core\Assets` — публикация статики ядра; `asset()` добавляет к адресу версию файла (`?v=mtime`).
- `env()`, `asset()`, `getSimflexVersion()` — в `src/functions.php` ядра (composer `autoload.files`).
- ORM: `AQ::fetchColumn()`, `AQ::getBinds()`, псевдоним таблицы в `join()`/`leftJoin()` (`alias:`),
  `ModelSettings::values()`, модели `Log`, `Seo`, `UserPrivilegePersonal`.
- Миграции: `$s->seed()` для начальных данных; `./sf migrate/create` нумерует миграции по порядку.
- `./sf layout/generate` печатает код полей компонентов страницы для `provider/templates.php`.
- Транслитерация алиасов по-русски (`я` → `ya`, `ж` → `zh`).
- `AGENTS.md` — правила работы с кодом ядра для агентов (комментарии на русском, геттеры с `get`, и др.).
- `ide-stubs.php` — подсказки IDE.
- `FieldDefinition` — аргумент `params`: параметры типа поля (`struct` у `FieldTable`, таблицы `FieldMultiKey`
  и `FieldRelation`, формат `FieldDouble`, `subquery` у `FieldVirtual`), попадают в `params['main']` поля.
- Пресеты полей `MultiKeyField` (связи «многие ко многим», `relationKey`, `parentKey`) и `PriceField` (цена
  с разделителем разрядов).
- `Table::$uploadDir`, `$uploadImageExtensions`, `$uploadFileExtensions` — каталог и белый список загрузки файлов
  полей-таблиц; `FieldVirtualTable::$binds` — параметры запросов строк; `FieldMultiKey::getValues()` (с поиском
  и лимитом), `getRelationKey()`.
- `DB::delete()` — удаление строк таблицы без модели (таблицы связей, кэши) по условию `Where`, пустое условие —
  исключение.

### Изменено

- Список раздела, количество и карточка записи собираются через `AQ`: значения фильтров передаются параметрами
  запроса, внешние ключи присоединяются `JOIN` с псевдонимом, ограничения по привилегиям — `Table::getScope()`.
- Запись раздела сохраняется через модель (`insertStatic()`, `update()`, `bulkUpdate()`): срабатывают хуки
  и события модели; простые действия списка (переключатель, порядок, копирование, удаление) — тоже через модель.
- Сортировка и страницы списка: `changeSorting()`, `getSortColumn()`, `isSortDesc()` поверх сессии, `Pagination`.
- Настройки сайта читаются через модель (`ModelSettings::values()`).
- Правая колонка формы группирует поля без группы по `group_name`, как левая; подсказка поля (`help`)
  показывается и в шапке списка.
- Значения по умолчанию «Показывать заголовок» и «Выводить обёртку» модуля в форме — как у модуля (выключено).
- Тема админки очищена от неиспользуемых библиотек (старая тема conquer, дубль TinyMCE, jstree, остатки TinyMCE 3,
  шрифты FontAwesome/Simple-Line-Icons), `theme/new` выпрямлена в `theme/{css,js,img}`.
- Комментарии в коде — на русском.
- `FieldMultiKey`, `FieldRelation`, поиск для фильтров (`Table::searchMultiKey()`, `searchInt()`) и фильтр списка
  по `FieldMultiKey` — запросы через `AQ`, значения параметрами, имена — `DB::wrapName()`.
- Форма выводит пустую (NULL) колонку и поле без колонки пустой строкой (`Table::formFieldInput()`, шаблоны полей).
- `FieldDouble` без параметров формата выводит значение с точкой и пробелами между разрядами в списке.

### Удалено

- Раздел «Меню» админки, разделы «Структура», «Компоненты», «Модули» (справочник), «Шаблоны страниц»
  и их параметры — всё это теперь в коде.
- `FieldTypeSelect`, `FieldTableSelect`, `typeLabel()`/`typeParams()` полей, `TableDefinition`, `FieldParams`.

### Исправлено

- Загрузка файлов админки: `upload_file` принимала любые файлы (в том числе `.php`), `upload_img` брала путь
  из запроса без проверки, обе — без проверки прав. Теперь нужно право добавлять или менять записи раздела,
  путь — без `..`, расширения `upload_file` — белый список раздела; `File::loadPost()` не сохраняет исполняемые
  файлы (`File::FORBIDDEN_EXTENSIONS`).
- Пустой экран при ошибке сохранения формы.
- Окно «Информация» строки показывало главную страницу; кнопки строки показывались без прав.
- Выпадающий список с поиском не сбрасывал введённый текст без выбора — форма не сохранялась без ошибки.
- Сохранение формы модуля без изменений добавляло заголовок и обёртку и ломало хлебные крошки админки.
- Сохранение модуля до загрузки его полей по ajax падало на отсутствующем поле.
- Раздел «Роли» у не-разработчика падал на подзапросе к `user_role`.
- Мобильная кнопка «Удалить» строки открывала форму вместо удаления.
- Копирование записи вставляло значения в SQL без экранирования; кавычка в фильтре списка ломала запрос.
- `AQ`: параметры дополнительных условий `ON` у `JOIN` не передавались в запрос.
- `FieldMultiKey` падал в форме новой записи; подписи связей в списке брались из общего на все поля буфера —
  второе поле раздела показывало связи первого.
- `FieldRelation`: пары связей новой записи записывались дважды, форма падала без id записи в запросе, при
  сохранении удалялись пары только в одном направлении.
- `FieldVirtualTable`: сортировка изображений `img_json` булевым компаратором, неверное значение по умолчанию.
- Шаблоны полей `input`, `textarea`, `input-hidden`, `color` и `FieldTable` падали на NULL.
- Загрузка файлов в разделе «Страницы» принимала любой файл (в том числе `.php`) с исходным именем и без проверки
  прав.

### Переход с 3.6

1. PHP 8.4, расширение `mbstring`; корень веб-сервера — `public/`.
2. Описания таблиц из `struct_*` — в классы разделов `App\Admin\Handlers\*` (`extends \Simflex\Admin\Table\Table`,
   `$model`, `getFields()`), классы — в `Admin/handlers.php` сайта.
3. Пункты `admin_menu` — в `App\Admin\Menu` (`static::add(new MenuItem(...))`), конфиг `adminMenuClass`.
4. Компоненты и модули — в `provider/components.php` и `provider/modules.php`; параметры модулей — `adminParams()`.
5. Привилегии проекта — в наследнике `\Simflex\Core\Privilege` (конфиг `privilegeClass`); в ролях и меню —
   имя привилегии вместо id.
6. Шаблоны страниц — в `provider/templates.php`, задачи cron — в `provider/cron.php`.
7. Начальные данные сидеров — в `$s->seed()` миграций.
8. Вывод компонентов, модулей и полей — `return $this->render(...)` вместо `echo`/`include`.

Подробнее — документация (`simflex-docs`): «Создание таблиц», «Навигация», «Привилегии», «Модели и запросы (ORM)»,
«Новый раздел в админке», «Подмена обработчика раздела ядра».
