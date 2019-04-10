<?php

namespace Verstaerker\I18nl10nBundle\Exception;

/**
 * Class NoRootPageException
 */
class NoRootPageException extends \Exception
{
    public function __construct(
        $message = "The i18nl10n extension requires at least one existing root page.",
        $code = 500,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
