<?php

namespace Vuravel\Catalog\Exceptions;

use RuntimeException;

class FilteringOperatorNotAllowedException extends RuntimeException
{
	public function setMessage($operator)
    {
        $this->message = "The {$operator} operator is either not allowed or not supported yet. Please refer to the docs for the list of supported WHERE operators.";
        return $this;
    }
}
