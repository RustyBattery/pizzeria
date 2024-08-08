<?php

namespace App\Builders;

use Exception;
use Illuminate\Database\Eloquent\Builder;

class BaseBuilder extends Builder
{
    public function get($data = [], $columns = ['*'])
    {
        $this->filter($data['filters'] ?? null);
        $this->search($data['search'] ?? null);
        $this->sort($data['sort'] ?? null);
        if (isset($data['pagination'])) {
            return $this->paginate($data['pagination']['per_page'] ?? 10, ['*'], 'page', $data['pagination']['current_page'] ?? null);
        }
        return parent::get($columns);
    }

    private function filter($filters = null): void
    {
        if (!$filters) {
            return;
        }
        $model = $this->getModel();
        foreach ($filters as $filter) {
            try {
                preg_match('/(.+)\.(.+)/', $filter['field'], $matches);
                $relation_filter = isset($matches[0]) ? (object)['relation' => $matches[1], 'field' => $matches[2]] : null;

                if (count($filter['values']) > 1) {
                    if ($relation_filter) {
                        $relation = $model->getRelationList()[$relation_filter->relation] ?? null;
                        if ($relation) {
                            $relation_class = new $relation->class;
                            if (in_array($relation_filter->field, $relation_class->getFieldList())) {
                                $field = $relation->type == 'belongsToMany' ? $relation_filter->relation . '.' . $relation_filter->field : $relation_filter->field;
                                $this->whereHas($relation_filter->relation, function ($query) use ($filter, $field) {
                                    $query->whereIn($field, $filter['values']);
                                });
                            }
                        }
                    } else {
                        if (in_array($filter['field'], $model->getFieldList())) {
                            $this->whereIn($filter['field'], $filter['values']);
                        }
                    }
                } else {
                    if ($relation_filter) {
                        $relation = $model->getRelationList()[$relation_filter->relation] ?? null;
                        if ($relation) {
                            $relation_class = new $relation->class;
                            if (in_array($relation_filter->field, $relation_class->getFieldList())) {
                                $field = $relation->type == 'belongsToMany' ? $relation_filter->relation . '.' . $relation_filter->field : $relation_filter->field;
                                $this->whereHas($relation_filter->relation, function ($query) use ($filter, $field) {
                                    $query->where($field, $filter['operator'], $filter['values'][0]);
                                });
                            }
                        }
                    } else {
                        if (in_array($filter['field'], $model->getFieldList())) {
                            $this->where($filter['field'], $filter['operator'], $filter['values'][0]);
                        }
                    }
                }
            } catch (\Exception) {
                continue;
            }
        }

    }

    private function search($search = null): void
    {
        if (!$search) {
            return;
        }
        $model = $this->getModel();
        try {
            $this->where(function ($query) use ($search, $model) {
                foreach ($search['fields'] as $field) {
                    preg_match('/(.+)\.(.+)/', $field, $matches);
                    $relation_search = isset($matches[0]) ? (object)['relation' => $matches[1], 'field' => $matches[2]] : null;
                    if ($relation_search) {
                        $relation = $model->getRelationList()[$relation_search->relation] ?? null;
                        if ($relation) {
                            $relation_class = new $relation->class;
                            if (in_array($relation_search->field, $relation_class->getFieldList())) {
                                $field = $relation->type == 'belongsToMany' ? $relation_search->relation . '.' . $relation_search->field : $relation_search->field;
                                $query->orWhereHas($relation_search->relation, function ($query) use ($search, $field) {
                                    $query->where($field, 'ilike', '%' . $search['value'] . '%');
                                });
                            }
                        }
                    } else {
                        if (in_array($field, $model->getFieldList())) {
                            $query->orWhere($field, 'ilike', '%' . $search['value'] . '%');
                        }
                    }
                }
            });
        } catch (\Exception) {
            return;
        }
    }

    private function sort($sort = null): void
    {
        if (!$sort) {
            return;
        }
        $model = $this->getModel();
        try {
            if (in_array($sort['field'], $model->getFieldList())) {
                $this->orderBy($sort['field'], $sort['order_by']);
            }
        } catch (\Exception) {
            return;
        }
    }
}
