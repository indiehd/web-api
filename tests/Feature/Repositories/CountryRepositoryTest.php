<?php

namespace Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Contracts\CountryRepositoryInterface;

class CountryRepositoryTest extends RepositoryReadonlyTestCase
{
    use RefreshDatabase;

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(CountryRepositoryInterface::class);
    }
}
