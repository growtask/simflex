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
     * Arguments: string $data - unmodified page content
     */
    case PrePrepareOutput;

    /**
     * Called after page output is modified
     * Arguments: string $data - possibly modified page content
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
}