<?php

namespace Tests\Feature\Repositories;

use App\Contracts\CountryRepositoryInterface;

class CountryRepositoryTest extends RepositoryReadonlyTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(CountryRepositoryInterface::class);
    }
}
