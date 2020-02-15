<?php 
namespace Vuravel\Catalog\Components\Traits;

trait DoesSorting {

    /**
     * Triggers a sort event of the catalog. The parameter is a pipe separated string of column:direction. Example: updated_at:DESC|last_name|first_name:ASC.
     *
     * @param string|null  $sortOrders  If field, the value will determine the sort. If trigger (link or th), we need to add a pipe serapated string of column:direction for ordering.
     *
     * @return self  
     */
    public function sortsCatalog($sortOrders = '')
    {
        return $this->updateDefaultTrigger(function($e) use($sortOrders) {
            $e->sortsCatalog($sortOrders);
        });
    }

    public function refreshCatalog($catalogId = null)
    {
        return $this->updateDefaultTrigger(function($e) use($catalogId) {
            $e->refreshCatalog($catalogId);
        });
    }

    public function refreshOnSuccess($catalogId = null)
    {
        $this->defaultTrigger()->refreshOnSuccess($catalogId);
        return $this;
    }

}