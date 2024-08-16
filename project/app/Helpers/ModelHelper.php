<?php

use App\DTO\Base\RelationDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

if (!function_exists('getModelRelations')) {
    /**
     * @param Model $model
     * @return array<RelationDTO>
     */
    function getModelRelations(Model $model): array
    {
        $modelClass = new ReflectionClass($model);
        $allMethods = $modelClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = array_filter(
            $allMethods,
            static function ($method) use ($modelClass) {
                return $method->class === $modelClass->getName() && !$method->getParameters();
            }
        );

        DB::beginTransaction();
        $relations = [];
        foreach ($methods as $method) {
            $methodName = $method->getName();
            try {
                $methodReturn = $model->$methodName();
                if ($methodReturn instanceof Relation) {
                    $type = lcfirst((new ReflectionClass($methodReturn))->getShortName());
                    $class = (new ReflectionClass($methodReturn->getRelated()))->getName();
                    $relations[$methodName] = RelationDTO::fromArray(['name' => $methodName, 'type' => $type, 'model' => $class]);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage(), ['exception' => $e]);
                continue;
            }
        }
        DB::rollBack();

        return $relations;
    }
}

if (!function_exists('getModelFields')) {
    /**
     * @param Model $model
     * @return array<string>
     */
    function getModelFields(Model $model): array
    {
        $fields = $model->getFillable();
        $fields[] = $model->getKeyName();
        return $fields;
    }
}
