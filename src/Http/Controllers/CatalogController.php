<?php

namespace Vuravel\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;

use Vuravel\Core\Http\Requests\SessionAuthorizationRequest;
use Vuravel\Form\Http\Requests\FormValidationRequest;

class CatalogController extends Controller
{
    /**
     * Browse the items of the Catalog.
     *
     * @return \Illuminate\Http\Response
     */
    public function browse(FormValidationRequest $request)
    {
        return $request->vlObject();
    }

    /**
     * Sets a new order for the catalog's items.
     *
     * @return \Illuminate\Http\Response
     */
    public function setOrder(SessionAuthorizationRequest $request)
    {
        $catalog = $request->vlObject();
        if($catalog->orderable){
            $catalog->reorder(request('order'));
        }
        return responseInErrorModal('Catalog is not orderable.');
    }

    /**
     * Deletes a database record
     * 
     * @param  string|integer $id [Object's key]
     * @return \Illuminate\Http\Response     [redirects back to current page]
     */
    public function deleteRecord($id)
    {
        $object = request('objectClass');
        $object = new $object();
        $object = $object->findOrFail($id);

        if( 
            (method_exists($object, 'deletable') && 
                $object->deletable()) 
            || 
            (defined(get_class($object).'::DELETABLE_BY') && $object::DELETABLE_BY &&
                optional(auth()->user())->hasRole($object::DELETABLE_BY))
            
            /* Controversial...
            || optional(auth()->user())->hasRole('super-admin')*/
        ){
            $object->delete();
            return redirect()->back();
        }

        return abort(403, __('Sorry, you are not authorized to delete this item.'));
    }

}
