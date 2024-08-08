<?php

namespace App\Models;

use App\Builders\BaseBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use stdClass;

class BaseModel extends Model
{
    public function getFieldList(){
        $fields = $this->fillable;
        $fields[] = $this->primaryKey;
        return $fields;
    }

    public function getRelationList()
    {
        $instance = new static;
        $class = get_class($instance);
        $allMethods = (new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array_filter(
            $allMethods,
            function ($method) use ($class) {
                return $method->class === $class && !$method->getParameters(); // relationships have no parameters
            }
        );

        \DB::beginTransaction();
        $relations = [];
        foreach ($methods as $method) {
            $methodName = $method->getName();
            try {
                $methodReturn = $instance->$methodName();
            } catch (\Throwable $th) {
                continue;
            }
            if ($methodReturn instanceof Relation) {
                $type = lcfirst((new \ReflectionClass($methodReturn))->getShortName());
                $class = get_class($methodReturn->getRelated());
                $relations[$methodName] = (object)['type' => $type, 'class' => $class];
            }
        }
        \DB::rollBack();

        return $relations;
    }

    public function newEloquentBuilder($query)
    {
        return new BaseBuilder($query);
    }
}
