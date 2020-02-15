<?php

namespace Vuravel\Catalog\Components;

class EditLink extends \VlLink
{    
    public $component = 'EditLink';
    public $linkTag = 'vlLink';

	protected function vlInitialize($label)
    {
    	parent::vlInitialize($label);

		if(!$label) //just an icon
			$this->icon('icon-edit');

		$this->emitsDirectOnSuccess('insertForm');
	}

}