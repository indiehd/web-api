<?php

namespace Tests\Feature\Controllers;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    use RefreshDatabase;

    public function factory(BaseRepository $repo = null): Factory
    {
        $modelName = $repo ? $repo->class() : $this->guessModel();

        return Factory::factoryForModel($modelName);
    }

    protected function guessModel()
    {
        $className = Str::of(class_basename($this))
            ->before('ControllerTest')
            ->prepend('\\App\\');

        return (string) $className;
    }
}
