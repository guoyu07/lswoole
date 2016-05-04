<?php

namespace Pauldo\Lswoole\Services;

use Exception as Ect;

class Exception extends Ect
{
    
    public function __construct($message, $code = -1)
    {
        parent::__construct($message, $code);
    }

}

