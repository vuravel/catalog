<?php

namespace Vuravel\Catalog\Exceptions;

use RuntimeException;

class NotFilteringComponentException extends RuntimeException
{
	public function setMessage($component)
    {
        $this->message = "This {$component} component does not perform filtering. Only Fields do. If you wish to filter with a Link or Button, please use SelectLinks or SelectButtons instead.";
        return $this;
    }
}
