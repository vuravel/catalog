<?php

namespace Vuravel\Catalog\Exceptions;

use RuntimeException;

class BadQueryDefinitionException extends RuntimeException
{
	public function setMessage($catalog)
    {
        $this->message = "The query is not well defined on {$catalog}. Please refer to the documentation for the possible ways of defining catalog queries.";
        return $this;
    }
}
