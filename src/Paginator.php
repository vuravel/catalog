<?php 

namespace Vuravel\Catalog;

use Vuravel\Catalog\Card;

class Paginator{

	protected $query;

	public $pagination;

	protected $catalog;

	public function __construct($catalog)
    {
        $this->catalog = $catalog;
    }

    public function executePagination()
    {
        $this->pagination = $this->query->paginate($this->catalog->perPage, ['*'], 'page', $this->catalog->currentPage());
    }

    public function transformItems()
    {
        $this->executePagination();

        $this->pagination->getCollection()->transform(function($item){

            return $this->getItemCard($item);

        });
    }

    protected function getItemCard($item)
    {
        return $this->getCardDefaultFallback($item);
    }

    protected function getCardDefaultFallback($item)
    {
        if(is_array($temp = $this->catalog->card($item))){
            $defaultCard = $this->catalog->card ?: Card::class;
            return $defaultCard::form($this->catalog->card($item));
        }else{
            return $this->catalog->card($item);
        }
    }

}