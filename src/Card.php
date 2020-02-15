<?php
namespace Vuravel\Catalog;

use Vuravel\Elements\Element;
use Vuravel\Elements\Traits\IsLayout;

class Card extends Element
{
    use IsLayout;

    public $component = 'Card';

    public function __construct(...$args)
    {
        $this->vlInitialize( $this->getNormalizedLabel( $args ) );

        $this->components = $this->getFilteredComponents( $args )->all();
    }

    public function prop($key)
    {
        return $this->components[$key];
    }

}