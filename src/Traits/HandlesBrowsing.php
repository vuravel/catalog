<?php 
namespace Vuravel\Catalog\Traits;

trait HandlesBrowsing {

    /**
     * Handles the catalog browsing (filtering and sorting)
     * 
     * @return self
     */
    public function handleBrowse()
    {
        collect($this->getFieldComponents($this))->each(function($field) {

            if($field->getFilterValue() && $field->name !== 'vuravelSort')
                $this->paginator->handleFilter($field);
        });

        if($sort = request('vuravelSort'))
            $this->paginator->handleSort($sort);

        return $this;
    }

    public function getFieldComponents($catalog)
    {
        //double flatMap
        return collect($this->filters)->flatMap->flatMap( function($component) use ($catalog) {

            return $component->getFieldComponents($catalog);

        })->filter();
    }


}