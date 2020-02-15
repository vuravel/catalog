<?php 

namespace Vuravel\Catalog;

class EloquentQuery extends DatabaseQuery{

    protected $record;

    /**
     * Constructs a Vuravel\Catalog\EloquentQuery object
     *
     * @param  array $components
     * @return void
     */
    public function __construct($query, $catalog)
    {
        parent::__construct($query, $catalog);

        $this->record = $this->catalog->record;
    }

    public function handleFilter($field)
    {
        if($field->isAttribute()){
            parent::handleFilter($field);
        }else{
            $this->eloquentFilter($field);
        }
    }

    public function eloquentFilter($field)
    {
        $relation = $this->record->getRelation($field->name);
        $value = $field->getFilterValue();
        $operator = $this->inferBestOperator($field);

        if($this->record->isBelongsTo($field->name)){
            $this->applyWhere($this->query, $relation->getForeignKeyName(), $operator, $value);
        }else{
            $filterKey = explode('.', $field->data('filterKey'), 2);

            $name = count($filterKey) == 2 ? $filterKey[1] : $relation->getRelated()->getKeyName();
            $table = $relation->getRelated()->getTable();

            $this->applyEloquentWhere($this->query, $field->name, $name, $operator, $value, $table);
        }
    }

    protected function applyEloquentWhere($q, $relation, $name, $operator, $value, $table = null)
    {
        $q = $q->whereHas($relation, function($subquery) use($name, $operator, $value, $table){
            $name = explode('.', $name);
            if(count($name) == 1){
                $this->applyWhere($subquery, ($table? ($table.'.') : '').$name[0], $operator, $value);
            }else{
                $this->applyEloquentWhere($subquery, $key[0], $key[1], $operator, $value);
            }
        });
    }

    public function sortBy($sort)
    {
        $sort = explode(':', $sort);
        $sortBy = explode('.', $sort[0]);

        if($this->record->methodExists($sortBy[0])){
            if(count($sortBy) == 1)
                abort(500, 'Please define the column you want to sort on relationship '.$sortBy[0].' using the syntax: relationship.column_name:direction');
            $this->eloquentSort($sortBy[0], $sortBy[1], count($sort) == 2 ? $sort[1] : 'ASC');
        }else{
            $this->attributeSort(implode(':', $sort));
        }
    }

    public function eloquentSort($relation, $column, $direction)
    {
        if($this->record->isBelongsTo($relation))
        {
            $relation = $this->record->getRelation($relation);
            $modelTable = $this->record->getTable();
            $relationTable = $relation->getRelated()->getTable();

            $modelKey = $relation->getForeignKeyName();
            $relationKey = $relation->getRelated()->getKeyName();

            $this->query->selectRaw($modelTable.'.*')
                ->leftJoin($relationTable, $relationTable.'.'.$relationKey, '=', $modelTable.'.'.$modelKey)->orderBy($relationTable.'.'.$column, $direction);

        }else if($this->record->isHasOne($relation)){

        }else if($this->record->isBelongsToMany($relation)){
            $relation = $this->record->getRelation($relation);
            $modelTable = $this->record->getTable();
            $pivotTable = $relation->getTable();
            $relationTable = $relation->getRelated()->getTable();

            $modelKey = $this->record->getKeyName();
            $foreignPivotKey = $relation->getForeignPivotKeyName();
            $relatedPivotKey = $relation->getRelatedPivotKeyName();
            $relationKey = $relation->getRelatedKeyName();

            $this->query->selectRaw($modelTable.'.*')
                ->leftJoin($pivotTable, $pivotTable.'.'.$foreignPivotKey, '=', $modelTable.'.'.$modelKey)
                ->leftJoin($relationTable, $relationTable.'.'.$relationKey, '=', $pivotTable.'.'.$relatedPivotKey)->orderBy($relationTable.'.'.$column, $direction);
        }
    }

    protected function getKeyName()
    {
        return $this->record->getKeyName();
    }


}