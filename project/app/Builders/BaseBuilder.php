<?php

namespace App\Builders;

use App\DTO\Base\FilterDTO;
use App\DTO\Base\PaginationDTO;
use App\DTO\Base\RelationDTO;
use App\DTO\Base\SearchDTO;
use App\DTO\Base\SortDTO;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class BaseBuilder extends Builder
{
    /**
     * @param array<FilterDTO>|null $filters
     * @param SearchDTO|null $search
     * @param SortDTO|null $sort
     * @param PaginationDTO|null $pagination
     * @return Collection|LengthAwarePaginator|array|Builder[]
     */
    public function getAdvanced(
        array $filters = null, SearchDTO $search = null, SortDTO $sort = null, PaginationDTO $pagination = null
    ): Collection|LengthAwarePaginator|array
    {
        $this->filter($filters);
        $this->search($search);
        $this->sort($sort);
        if ($pagination) {
            return $this->paginate($pagination->per_page, ['*'], 'page', $pagination->current_page);
        }
        return $this->get();
    }

    /**
     * @param array<FilterDTO>|null $filters
     * @return BaseBuilder
     */
    public function filter(array|null $filters): BaseBuilder
    {
        if (!$filters) {
            return $this;
        }
        foreach ($filters as $filter) {
            try {
                //check there is a filter for relation (relation_name.relation_field)
                preg_match('/(.+)\.(.+)/', $filter->field, $matches);

                if (count($filter->values) > 1) {
                    $this->multipleFilter($filter);
                    continue;
                }

                if (isset($matches[0])) {
                    $this->filterForRelation(
                        $matches[2],
                        $filter->values[0],
                        $filter->operator,
                        getModelRelations($this->getModel())[$relation_filter->relation] ?? null
                    );
                    continue;
                }

                if (in_array($filter->field, getModelFields($this->getModel()))) {
                    $this->where($filter->field, $filter->operator, $filter->values[0]);
                }

            } catch (Exception $e) {
                Log::error($e->getMessage(), ['exception' => $e]);
                continue;
            }
        }
        return $this;
    }

    /**
     * @param SearchDTO|null $search
     * @return BaseBuilder
     */
    public function search(SearchDTO|null $search): BaseBuilder
    {
        if (!$search) {
            return $this;
        }
        $model = $this->getModel();
        try {
            $this->where(function ($query) use ($search, $model) {
                foreach ($search->fields as $field) {
                    //check there is a search for relation (relation_name.relation_field)
                    preg_match('/(.+)\.(.+)/', $field, $matches);

                    if (isset($matches[0])) {
                        $this->searchForRelation(
                            $query,
                            $matches[2],
                            $search->value,
                            getModelRelations($model)[$matches[1]] ?? null
                        );
                        continue;
                    }

                    if (in_array($field, getModelFields($model), true)) {
                        $query->orWhere($field, 'ilike', '%' . $search->value . '%');
                    }
                }
            });
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return $this;
        }
        return $this;
    }


    /**
     * @param SortDTO|null $sort
     * @return BaseBuilder
     */
    public function sort(SortDTO|null $sort): BaseBuilder
    {
        if (!$sort) {
            return $this;
        }

        try {
            if (in_array($sort->field, getModelFields($this->getModel()), true)) {
                $this->orderBy($sort->field, $sort->order_by);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return $this;
        }

        return $this;
    }


    /**
     * @param FilterDTO $filter
     * @return void
     */
    private function multipleFilter(FilterDTO $filter): void
    {
        preg_match('/(.+)\.(.+)/', $filter->field, $matches);
        if (isset($matches[0])) {
            $this->multipleFilterForRelation(
                $matches[2],
                $filter->values,
                getModelRelations($this->getModel())[$matches[1]] ?? null
            );
            return;
        }
        if (in_array($filter->field, getModelFields($this->getModel()))) {
            $this->whereIn($filter->field, $filter->values);
        }
    }

    /**
     * @param string $field
     * @param array $values
     * @param RelationDTO|null $relation
     * @return void
     */
    private function multipleFilterForRelation(string $field, array $values, RelationDTO|null $relation): void
    {
        if (!$relation) {
            return;
        }
        $relation_model = new $relation->model;
        if (in_array($field, getModelFields($relation_model), true)) {
            $field = $relation->type === 'belongsToMany' ? $relation->name . '.' . $field : $field;
            $this->whereHas($relation->name, function ($query) use ($field, $values) {
                $query->whereIn($field, $values);
            });
        }
    }

    /**
     * @param string $field
     * @param array $value
     * @param string $operator
     * @param RelationDTO|null $relation
     * @return void
     */
    private function filterForRelation(string $field, array $value, string $operator, RelationDTO|null $relation): void
    {
        if (!$relation) {
            return;
        }
        $relation_model = new $relation->model;
        if (in_array($field, getModelFields($relation_model), true)) {
            $field = $relation->type === 'belongsToMany' ? $relation->name . '.' . $field : $field;
            $this->whereHas($relation->name, function ($query) use ($field, $value, $operator) {
                $query->where($field, $operator, $value);
            });
        }

    }

    /**
     * @param Builder $query
     * @param string $field
     * @param string $value
     * @param RelationDTO|null $relation
     * @return void
     */
    private function searchForRelation(Builder $query, string $field, string $value, RelationDTO|null $relation): void
    {
        if (!$relation) {
            return;
        }
        $relation_model = new $relation->model;
        if (in_array($field, getModelFields($relation_model), true)) {
            $field = $relation->type === 'belongsToMany' ? $relation->name . '.' . $field : $field;
            $query->orWhereHas($relation->name, function ($query) use ($field, $value) {
                $query->where($field, 'ilike', '%' . $value . '%');
            });
        }
    }

}
