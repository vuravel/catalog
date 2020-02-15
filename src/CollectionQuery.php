<?php 

namespace Vuravel\Catalog;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CollectionQuery extends Paginator{

    public function __construct($query, $catalog)
    {
        parent::__construct($catalog);

        $this->query = $query instanceOf Collection ? $query : collect($query);

    }

    public function executePagination()
    {
        $slice = $this->query->slice(
                                ($this->catalog->currentPage() - 1)* $this->catalog->perPage, 
                                $this->catalog->perPage)->values();

        $this->pagination = new LengthAwarePaginator(
                                $slice, 
                                $this->query->count(), 
                                $this->catalog->perPage, 
                                $this->catalog->currentPage());
    }




}