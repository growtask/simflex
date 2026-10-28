<?php

namespace Simflex\Core\Http;

use ReflectionClass;
use Simflex\Core\Http\Controller\Action;
use Simflex\Core\Http\Controller\ActionMatcher;

/**
 * Автоматический контроллер
 *
 * Действия объявляются в дочернем классе атрибутом Action.
 * Методы действий возвращают HTML строкой, а не выводят его
 * @see Action
 */
class ControllerBase extends ComponentBase
{
    /**
     * @var array|ActionMatcher[] Действия
     */
    protected array $actions = [];

    public function __construct(protected Request $request, protected Response $response)
    {
        $this->collectActions();
    }

    /**
     * Запасной метод, возвращает HTML, когда ни одно действие не подошло
     */
    protected function fallback(): string
    {
        // отдаем 404
        $this->response->redirectNow('/404');
    }

    /**
     * Собирает действия
     */
    protected function collectActions(): void
    {
        $class = new ReflectionClass($this);
        foreach ($class->getMethods() as $method) {
            $attribs = $method->getAttributes(Action::class);
            if (!$attribs) {
                continue;
            }

            /** @var Action $action */
            $action = $attribs[0]->newInstance();
            $this->actions[] = new ActionMatcher($action, $method->getName());
        }
    }

    /**
     * Пытается подобрать действие контроллера
     */
    protected function resolve(): ?ActionMatcher
    {
        foreach ($this->actions as $action) {
            if ($action->match()) {
                return $action;
            }
        }

        return null;
    }

    protected function content(): string
    {
        $maybeAction = $this->resolve();
        if (!$maybeAction) {
            return $this->fallback();
        }

        $vars = $maybeAction->getVars();
        $request = $this->request->request();

        // получаем метод
        $class = new ReflectionClass($this);
        $method = $class->getMethod($maybeAction->methodName);

        // подбираем аргументы
        $varPos = [];
        foreach ($method->getParameters() as $param) {
            if (isset($vars[$param->name])) {
                $varPos[] = $vars[$param->name];
                continue;
            }

            if (isset($request[$param->name])) {
                $varPos[] = $request[$param->name];
            }
        }

        // вызываем метод, действия возвращают HTML
        return (string)$method->invoke($this, ...$varPos);
    }
}
