<?php

namespace App\Actions;

use Exception;
use MetaFramework\Traits\Ajax;
use ReflectionClass;
use Throwable;
use Illuminate\Database\Eloquent\Model;

class ModelActions
{
    use Ajax;

    protected string $model;
    protected string $column;
    protected mixed $id;
    protected mixed $value;
    protected string $updatable = 'id';

    public function setModel(string $model): self
    {
        if (!class_exists($model)) {
            throw new Exception("Model class {$model} not found.");
        }

        $reflection = new ReflectionClass($model);
        if (!$reflection->isSubclassOf(Model::class)) {
            throw new Exception("{$model} is not a valid Eloquent model.");
        }

        $this->model = $model;
        return $this;
    }

    public function setColumn(string $column): self
    {
        if (!isset($this->model)) {
            throw new Exception("Model must be set before setting column.");
        }
/*
        $instance = new $this->model();
        if (!array_key_exists($column, $instance->getAttributes())) {
            throw new Exception("Column {$column} does not exist in model {$this->model}.");
        }
*/
        $this->column = $column;
        return $this;
    }

    public function setId(mixed $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function
    updateModelAttribute(): self
    {
        try {
            if (!isset($this->model, $this->column, $this->id, $this->value)) {
                throw new Exception("Model, column, ID, and value must be set before updating.");
            }

            $modelInstance = $this->model::find($this->id);

            if (!$modelInstance) {
                throw new Exception("No record found with ID {$this->id} in {$this->model}.");
            }

            $modelInstance->{$this->column} = $this->value;
            $modelInstance->save();

            $this->responseSuccess(__('ui.record_updated'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }
        return $this;
    }
}
