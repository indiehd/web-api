<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    protected function log($msg)
    {
        return $this->command->getOutput()->writeln("<info>LOG:</info> $msg");
    }
}
