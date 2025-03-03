<?php

namespace Simflex\Core;

use ReflectionClass;
use Simflex\Core\ComponentBase;
use Simflex\Core\Controller\Action;
use Simflex\Core\Controller\ActionMethod;
use Simflex\Core\Core;

/**
 * Automatic controller
 *
 * Use Action attribute to create attributes within child class
 * @see Action
 */
class ControllerBase extends ComponentBase
{
    /**
     * @var array|ActionMethod[] Actions
     */
    protected array $actions = [];

    public function __construct(protected Request $request, protected Response $response)
    {
        parent::__construct();
        $this->collectActions();
    }

    /**
     * Fallback method
     * @return void
     */
    protected function fallback(): void
    {
        // redirect to 404
        $this->response->redirectNow('/404');
    }

    /**
     * Collects actions
     * @return void
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
            $this->actions[] = new ActionMethod($action, $method->getName());
        }
    }

    /**
     * Try to resolve controller's actions
     * @return ActionMethod|null
     */
    protected function resolve(): ?ActionMethod
    {
        foreach ($this->actions as $action) {
            if ($action->match()) {
                return $action;
            }
        }

        return null;
    }

    protected function content()
    {
        $maybeAction = $this->resolve();
        if (!$maybeAction) {
            $this->fallback();
            return;
        }

        $vars = $maybeAction->getVars();
        $request = $this->request->request();

        // get the method
        $class = new ReflectionClass($this);
        $method = $class->getMethod($maybeAction->methodName);

        // resolve arguments
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

        // invoke the method
        $method->invoke($this, ...$varPos);
    }
}
