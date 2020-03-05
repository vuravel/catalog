<?php

namespace Vuravel\Catalog;

use Vuravel\Form\Model;
use Vuravel\Form\Component;
use Vuravel\Core\Contracts\Routable;
use Vuravel\Form\Traits\HasValidationRules;
use Vuravel\Catalog\Components\PaginationLinks;
use Vuravel\Core\Traits\{IsRoutable, HasMetaTags};
use Vuravel\Catalog\Exceptions\BadQueryDefinitionException;
use Vuravel\Catalog\Traits\{HandlesBrowsing, PersistsInSession};
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Support\Collection as LaravelCollection;
use Illuminate\Database\Query\Builder as LaravelQueryBuilder;
use Illuminate\Database\Eloquent\Builder as LaravelEloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as LaravelRelation;

class Catalog extends Component implements Routable
{
    use IsRoutable, HasMetaTags, HasValidationRules, PersistsInSession, HandlesBrowsing;

    /**
     * The card component where an item will be displayed
     */
    public $card;

    /**
     * The catalog's layout component.
     */
    public $layout = 'Horizontal';


    public $component = 'FormCatalog';
    public $partial = 'VlCatalog';
    
    public $hasPagination = true; //Whether to display pagination links or not
    public $topPagination = true; //Whether to display pagination links above the cards
    public $bottomPagination = false; //Whether to display pagination links below the cards
    public $leftPagination = false; //Whether to align pagination links to the left or to the right
    public $paginationStyle = 'Links';

    public $noItemsFound = 'No items found';
    public $perPage = 50;
    
    public $name;
    protected $currentPage = 1;

    public $paginator;
    
    public $filters;
    public $filtersPlacement = [ 'top', 'left', 'bottom', 'right' ];
    
    /**
     * Default order of the items[ col1 => ASC|DESC, col2 => ASC|DESC ]
     * @var array
     */
    public $orderBy = [];
    public $record = false; //To allow using ->prepareComponents($catalog) for filtering...
    public static $model; //I would use in query() instead. Will deprecate.
    public $with; //If catalog needs pre-loading relations with $model
    public $withCount; //If catalog needs pre-loading relation count with $model

    /**
     * Ordering column and direction of draggable ordering feature
     * @var boolean|string
     */
    public $orderable = false;
    public $paginateKey = 'id';

    public $browseUrl;
    public $editUrl;
    public $deleteUrl;

    public function query()
    {
        if( $model = static::$model )
            return new $model();
    }

    public function card($item)
    {
        return [];
    }

    //Catalog filters
    public function top(){ return []; }
    public function bottom(){ return []; }
    public function left(){ return []; }
    public function right(){ return []; }


    /**
     * Get the filter/browse request's validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Construct a Catalog object.
     *
     * @return Vuravel\Catalog\Catalog
     */
    public function __construct($dontBoot = false)
    {
        if(!$dontBoot)
            $this->bootToSession();
    }

    /**
     * Boot a Catalog object.
     *
     * @return Vuravel\Catalog\Catalog
     */
    public function vlBoot()
    {
        return $this->createdHook()
                    ->prepareCatalog()
                    ->preparePaginator()
                    ->prepareFilters()
                    ->prepareCards()
                    ->addValidationRules($this->rules())
                    ->bootedHook();
    }

    public function startReboot()
    {
        return $this->createdHook()
                    ->prepareCatalog()
                    ->preparePaginator();
    }

    public function finishReboot()
    {
        return $this->prepareFilters()
                    ->handleBrowse()
                    ->prepareCards()
                    ->addValidationRules($this->rules())
                    ->bootedHook();
    }

    /**
     * Initialize Catalog attributes.
     *
     * @return Collection
     */
    protected function prepareCatalog()
    {
        //$this->name = class_basename($this); //necessary? to delete

        $this->setBootableId();

        $this->browseUrl = route('vuravel-catalog.browse');

        $this->noItemsFound = __($this->noItemsFound);

        $this->configureSorting();

        if(method_exists($this, 'columns'))
            $this->columns = collect($this->columns())->filter();

        return $this;
    }

    /**
     * Create the query for the catalog paginated items.
     *
     * @return void
     */
    protected function preparePaginator()
    {
        $q = $this->query();

        if($q instanceOf LaravelModel || $q instanceOf LaravelEloquentBuilder  || $q instanceOf LaravelRelation){

            $this->record = Model::create($q->getModel(), null);
            $this->paginator = new EloquentQuery($q, $this);

        }elseif($q instanceOf LaravelQueryBuilder){
            
            $this->paginator = new DatabaseQuery($q, $this);
            
        }elseif($q instanceOf LaravelCollection || is_array($q) || $q === null){

            $this->paginator = new CollectionQuery($q, $this);

        }else{
            throw (new BadQueryDefinitionException)->setMessage(class_basename($this));
        }
        return $this;
    }

    /**
     * Prepare the filters'.
     *
     * @return Collection
     */
    public function prepareFilters()
    {
        foreach ($this->filtersPlacement as $placement) {

            if( !(($filters = $this->{$placement}()) instanceOf LaravelCollection) )
                $filters = collect(is_array($filters) ? $filters : [$filters]);

            $this->filters[$placement] = $filters->filter()->each(function($filter){
                    $filter->prepareComponent($this);
                });
                
        }
        return $this;
    }

    /**
     * Prepare the components' attributes and values.
     *
     * @param  array  $components
     * @return void
     */
    protected function prepareCards()
    {
        $this->paginator->transformItems();

        return $this;
    }

    /**
     * Prepare the components' attributes and values.
     *
     * @param  array  $components
     * @return void
     */
    public function reorder($newOrder)
    {
        $this->paginator->reorderItems($newOrder);

        return $this;
    }


    public function currentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set default drag ordering functionnality 
     *
     * 2 formats:
     * 'column' -> set ordering column 
     * true  -> ordering column is 'order' by default
     */
    public function configureSorting()
    {
        $this->orderable = is_string($this->orderable) ? $this->orderable : 
                                ($this->orderable === true ? 'order' : false);
        if($this->orderable){
            $this->orderBy = array_merge([$this->orderable => 'ASC'], $this->orderBy);
            $this->data(['orderableRoute' => route('vuravel-catalog.order')]);
        }
    }


    /**
     * Convert the model to its string representation in JSON
     * Mostly, useful when echoing in blade for example.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * Shortcut method to render a catalog into it's Vue component.
     *
     * @return     string
     */
    public static function vueRender($catalog)
    {
        return '<vl-catalog :vcomponent="'.htmlspecialchars($catalog).'"></vl-catalog>';
    }

    public static function duplicateStaticMethods()
    {
        return ['store', 'render'];
    }
}