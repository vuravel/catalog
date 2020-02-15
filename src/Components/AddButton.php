<?php

namespace Vuravel\Catalog\Components;

use Vuravel\Catalog\Components\AddLink;

class AddButton extends AddLink
{
    public $linkTag = 'vlButton';
    
	protected function vlInitialize($label)
    {
    	parent::vlInitialize($label);
		$this->outlined();
	}
}