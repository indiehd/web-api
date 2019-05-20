<?php

namespace App\Console\Commands;

use App\Contracts\ArtistRepositoryInterface;
use Illuminate\Console\Command;

use App\Contracts\FeaturedRepositoryInterface;

class Feature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feature:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queries featurable entities for eligibility and features them as appropriate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        FeaturedRepositoryInterface $featured
    ) {
        parent::__construct();

        $this->featured = $featured;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $artist = resolve(ArtistRepositoryInterface::class);

        $this->featured->makeFeatured($artist);
    }
}
