<?php

namespace Database\Factories;

use App\FlacFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlacFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FlacFile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = $this->faker;

        return [
            'file_size' => $faker->numberBetween(10000, 100000000),
            'file_format' => 'flac',
            'data_format' => 'flac',
            'bitrate_mode' => 'vbr',
            'is_lossless' => $faker->numberBetween(0, 1),
            'num_channels' => 2,
            'sample_rate' => $faker->randomElement([44100, 48000, 96000]),
            'bits_per_sample' => $faker->randomElement([16, 24]),
            'bitrate' => $faker->randomFloat(7, 80000, 3000000),
            'encoder' => $faker->randomElement([
                'reference libFLAC 1.1.4 20070213',
                'reference libFLAC 1.2.1 20070917',
                'reference libFLAC 1.1.1 20041001',
                'reference libFLAC 1.1.2 20050205',
                'reference libFLAC 1.1.0 20030126',
                'libFLAC 1.2.1 20070917',
                'reference libFLAC 1.2.1 win64 20080709',
                'Lavf53.19.0',
                'libFLAC 1.3.0 20130526',
                'Lavf52.68.0',
                'Lavf54.29.105',
                'reference libFLAC 1.3.0 20130526',
                'libFLAC 1.1.0 20030126',
                'libFLAC 1.3.1 20141125',
            ]),
            'channel_mode' => 'stereo',
            'compression_ratio' => $faker->randomFloat(14, 0, 1),
            'encoding' => $faker->randomElement(['ISO-8859-1', 'UTF-8']),
            'mime_type' => 'audio/x-flac',
            'play_time_seconds' => $faker->randomFloat(7, 0, 9999),
            'md5_data_source' => $faker->md5(),
            'sha256' => $faker->sha256(),
        ];
    }
}
