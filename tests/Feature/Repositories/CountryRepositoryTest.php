<?php

namespace Tests\Feature\Repositories;

use App\Contracts\CountryRepositoryInterface;

class CountryRepositoryTest extends RepositoryReadonlyTestCase
{
    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(CountryRepositoryInterface::class);
    }
}
