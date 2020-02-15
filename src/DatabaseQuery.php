<?php 

namespace Vuravel\Catalog;

use Vuravel\Form\Components\Input;

class DatabaseQuery extends Paginator{

    /**
     * Constructs a DatabaseQuery Object
     *
     * @return void
     */
    public function __construct($query, $catalog)
    {
        parent::__construct($catalog);

        $this->prepareBuilder($query, $catalog);
    }

    protected function prepareBuilder($query, $catalog)
    {
        $this->query = $query;

        if($catalog->with)
            $this->query = $this->query->with($catalog->with);

        if($catalog->withCount)
            $this->query = $this->query->withCount($catalog->withCount);

        //Issue for multiple order columns: the order of columns wouldn't be respected because it is currently defined as an associative array...
        foreach ($catalog->orderBy as $key => $value) {
            $this->query = $this->query->orderBy($key, $value);
        }
    }

    public function handleFilter($field)
    {
        $name = $field->name;
        $operator = $this->inferBestOperator($field);
        $value = request($field->name);

        $this->applyWhere($this->query, $name, $operator, $value);
    }

    protected function inferBestOperator($field)
    {
        return $field->data('filterOperator') ?: (
            ($field->multiple ?? false) ? 'IN' : ($field instanceOf Input ? 'LIKE' : '=')
        );
    }

    public function applyWhere($q, $name, $operator, $value)
    {
        if($operator == 'IN'){
            $q = $q->whereIn($name, $value);
        }elseif($operator == 'LIKE'){
            $q = $q->where($name, 'LIKE', '%'.$value.'%');
        }elseif($operator == 'STARTSWITH'){
            $q = $q->where($name, 'LIKE', $value.'%');
        }elseif($operator == 'ENDSWITH'){
            $q = $q->where($name, 'LIKE', '%'.$value);
        }elseif($operator == 'BETWEEN'){
            $q = $q->whereBetween($name, $value);
        }else{
            $q = $q->where($name, $operator, $value);
        }
    }

    public function handleSort($sort)
    {
        //clearing orderBy from query, should give the user the ability to do so or not
        $temp = $this->query->getQuery();
        $temp->orders = [];
        $this->query->setQuery($temp);
        foreach(explode('|', $sort) as $colDir)
        {
            $this->sortBy($colDir);
        }
    }

    public function sortBy($sort)
    {
        $this->attributeSort($sort);
    }

    public function attributeSort($sort)
    {
        $sort = explode(':', $sort);
        $this->query->orderBy($sort[0], count($sort) == 2 ? $sort[1] : 'ASC');
    }

    protected function getItemCard($item)
    {
        $defaultItems = array_merge(
            $this->catalog->orderable ? [
                'id' => $item->{$this->getKeyName()},
                'order' => $item->{$this->catalog->orderable}
            ] : []
        );

        $card = $this->getCardDefaultFallback($item);
        $card->components = array_merge($defaultItems, $card->components);
        return $card;
    }

    public function reorderItems($order)
    {
        foreach($order as $v)
        {
            with(clone $this->query)->where($this->getKeyName(), $v['id'])->update([
                $this->catalog->orderable => $v['order']
            ]);
        }
    }

    protected function getKeyName()
    {
        return $this->catalog->paginateKey;
    }


}