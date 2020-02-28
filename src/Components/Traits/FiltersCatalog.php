<?php 
namespace Vuravel\Catalog\Components\Traits;

use Vuravel\Catalog\Exceptions\{NotFilteringComponentException, FilteringOperatorNotAllowedException};

trait FiltersCatalog {

    protected static $allowedOperators = [
        '',
        '=',
        '>',
        '<',
        '>=',
        '<=',
        'LIKE',
        'STARTSWITH',
        'ENDSWITH',
        'BETWEEN',
        'IN'
    ];

    /**
     * Filters a Catalog onChange for a Field. 
     * - If you wish to filter by an attribute, the filterKey will be the attribute name. 
     * - For a relationship, you may chain a dot-separated string to filter against a nested relationship.
     *
     * @param      string|null  $filterKey    The model "attribute" or "relationship.attribute" or "relationship.relationship.attribute".
     * @param string|null $operator A supported operator '=','>','<','>=','<=','LIKE','STARTSWITH','ENDSWITH','BETWEEN','IN'
     *
     * @return     self
     */
    public function filtersCatalog($filterKey = null, $operator = null)
    {
        $this->filterBy($filterKey, $operator);
        return $this->refreshCatalog(null, 1); //filtering works for it's own catalog only
    }

    /**
     * Performs a 500ms debounced request to filter a Catalog when the user inputs in a Field. 
     * - If you wish to filter by an attribute, the filterKey will be the attribute name. 
     * - For a relationship, you may chain a dot-separated string to filter against a nested relationship.
     *
     * @param      string|null  $filterKey    The model "attribute" or "relationship.attribute" or "relationship.relationship.attribute".
     * @param string|null $operator A supported operator '=','>','<','>=','<=','LIKE','STARTSWITH','ENDSWITH','BETWEEN','IN'
     *
     * @return     self
     */
    public function filtersOnInput($filterKey = null, $operator = null)
    {
        $this->filterBy($filterKey, $operator);
        return $this->onInput( function($e) {
            $e->refreshCatalog(null, 1)  //filtering works for it's own catalog only
               ->debounce();
        });
    }

    public function getFilterValue()
    {
        return request(str_replace('.', '_', $this->data('filterKey')));  //PHP replaces dots by _
    }

    public function filterBy($filterKey = null, $operator = null)
    {
        if(!$this->isField())
            throw (new NotFilteringComponentException)->setMessage(class_basename($this));

        if(!in_array($operator, self::$allowedOperators))
            throw (new FilteringOperatorNotAllowedException)->setMessage($operator);

        if($filterKey) //otherwise takes the snake cased name from the label
            $this->name(strtok($filterKey, '.'));

        return $this->data([
            'filterKey' => $filterKey ?: $this->name,
            'filterOperator' => $operator
        ]);
    }

}