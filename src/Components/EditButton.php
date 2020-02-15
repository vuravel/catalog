<?php

namespace Vuravel\Catalog\Components;

use Vuravel\Catalog\Components\EditLink;

class EditButton extends EditLink
{
    public $linkTag = 'vlButton';
    
	protected function vlInitialize($label)
    {
    	parent::vlInitialize($label);
		$this->outlined();
	}
}