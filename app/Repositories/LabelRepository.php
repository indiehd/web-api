<?php

namespace App\Repositories;

use App\Contracts\LabelRepositoryInterface;
use App\Label;
use App\Traits\IsProfilable;

class LabelRepository extends CrudRepository implements LabelRepositoryInterface
{
    use IsProfilable;

    /**
     * @var string
     */
    protected $class = Label::class;

    /**
     * @var Label
     */
    protected $label;

    public function __construct(Label $label)
    {
        $this->label = $label;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->label;
    }

    public function create(array $data)
    {
        $model = $this->model()->create([]);

        $this->createProfile(
            $model,
            $data['moniker'],
            $data['city'],
            $data['territory'],
            $data['country_code'],
            $data['profile_url']
        );

        return $model;
    }

    public function update($id, array $data)
    {
        $model = $this->findById($id);

        $this->updateProfile($model->profile->id, $data);

        return $model;
    }

    public function profile()
    {
        $this->label->profile();
    }
}
