<?php
namespace Simflex\Core\Events;

enum Events
{
    /**
     * Called before core/page init
     */
    case PreInit;

    /**
     * Called after core/page init
     */
    case PostInit;

    /**
     * Called before core exec
     */
    case PreExecute;

    /**
     * Called after core exec
     */
    case PostExecute;

    /**
     * Called before page output is modified
     *
     * Arguments:
     *  - string $data - unmodified page content
     */
    case PrePrepareOutput;

    /**
     * Called after page output is modified
     *
     * Arguments:
     *  - string $data - possibly modified page content
     */
    case PostPrepareOutput;

    /**
     * Called before auth init
     */
    case PreAuthInit;

    /**
     * Called after auth init
     */
    case PostAuthInit;

    /**
     * Called before core and page are added to Container
     */
    case PreCoreInit;

    /**
     * Called after core and page are added to Container
     */
    case PostCoreInit;

    /**
     * Called before logger init
     */
    case PreLoggerInit;

    /**
     * Called after logger init
     */
    case PostLoggerInit;

    /**
     * Called before model's data is filled
     *
     * Arguments:
     *  - mixed $model - model instance
     */
    case PreModelFill;

    /**
     * Called after model's data is filled
     *
     * Arguments:
     *  - mixed $model - model instance
     */
    case PostModelFill;

    /**
     * Called before model is saved
     *
     * Arguments:
     * - mixed $model - model instance
     */
    case PreModelSave;

    /**
     * Called after model is saved
     *
     * Arguments:
     * - mixed $model - model instance
     * - bool $result - save result
     */
    case PostModelSave;

    /**
     * Called before model is inserted
     *
     * Arguments:
     * - mixed $model - model instance
     */
    case PreModelInsert;

    /**
     * Called after model is inserted
     *
     * Arguments:
     * - mixed $model - model instance
     * - bool $result - insert result
     */
    case PostModelInsert;

    /**
     * Called before model is updated
     *
     * Arguments:
     * - mixed $model - model instance
     */
    case PreModelUpdate;

    /**
     * Called after model is updated
     *
     * Arguments:
     * - mixed $model - model instance
     * - bool $result - update result
     */
    case PostModelUpdate;

    /**
     * Called before model is deleted
     *
     * Arguments:
     * - mixed $model - model instance
     */
    case PreModelDelete;

    /**
     * Called after model is deleted
     *
     * Arguments:
     * - array $oldData - old model data
     * - bool $result - delete result
     */
    case PostModelDelete;
}