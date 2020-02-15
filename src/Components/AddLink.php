<?php

namespace Vuravel\Catalog\Components;

class AddLink extends \VlLink
{
    public $component = 'EditLink';
    public $linkTag = 'vlLink';

    protected function vlInitialize($label)
    {
    	parent::vlInitialize($label);

    	$this->emitsDirectOnSuccess('insertForm');
    }

}