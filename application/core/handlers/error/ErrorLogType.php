<?php

namespace Application\Core\Handlers\Error;

abstract class ErrorLogType extends \SplEnum
{
    const __default = self::webError;

    const dbError = 0;
    const webError = 1;
}
