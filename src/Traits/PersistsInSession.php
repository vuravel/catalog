<?php 
namespace Vuravel\Catalog\Traits;

use Vuravel\Core\Traits\PersistsInSession as Sessionable;

trait PersistsInSession {

    use Sessionable;
    
    /**
     * Get a booted instance of the Catalog class from a request.
     *
     * @return \Vuravel\Catalog\Catalog
     */
    public function bootFromRequest()
    {
        $this->currentPage = request('page') ?: $this->currentPage;
        $this->store(request('store'));
        return $this->bootToSession();
    }

    public function rebootFromSession($sessionObject)
    {
        $this->setCommonRebootAttributes($sessionObject);
        $this->currentPage = request()->header('X-Vuravel-Page');
        return $this;
    }

}