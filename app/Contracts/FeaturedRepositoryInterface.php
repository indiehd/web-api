<?php

namespace App\Contracts;

interface FeaturedRepositoryInterface extends RepositoryShouldCrud
{
    public function makeFeatured(FeaturableRepositoryInterface $featurable): void;
}
