<?php

namespace Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Contracts\CountryRepositoryInterface;

class CountryRepositoryTest extends RepositoryReadonlyTestCase
{
    use DatabaseTransactions;

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(CountryRepositoryInterface::class);
    }
}
