<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Ramsey\Uuid\Uuid;

class UuidModel extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            $model->access_code = Uuid::uuid4()->toString();
        });
    }
}
