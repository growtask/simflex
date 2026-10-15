<?php

namespace Simflex\Core;

use ReflectionClass;
use Simflex\Core\Controller\Action;
use Simflex\Core\Controller\ActionMatcher;

/**
 * Automatic controller
 *
 * Use Action attribute to create attributes within child class.
 * Action methods return HTML as a string, they do not output it
 * @see Action
 */
class ControllerBase extends ComponentBase
{
    /**
     * @var array|ActionMatcher[] Actions
     */
    protected array $actions = [];

    public function __construct(protected Request $request, protected Response $response)
    {
        parent::__construct();
        $this->collectActions();
    }

    /**
     * Fallback method, returns HTML when no action was matched
     */
    protected function fallback(): string
    {
        // redirect to 404
        $this->response->redirectNow('/404');
    }

    /**
     * Collects actions
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
     * Try to resolve controller's actions
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

        // invoke the method, actions return HTML
        return (string)$method->invoke($this, ...$varPos);
    }
}
