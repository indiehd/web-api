<?php

namespace Tests\Feature\Repositories;

use App\Contracts\CountryRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
