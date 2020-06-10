<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlacFile extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'bitrate' => 'float',
        'bits_per_sample' => 'int',
        'compression_ratio' => 'float',
        'file_size' => 'int',
        'is_lossless' => 'bool',
        'num_channels' => 'int',
        'play_time_seconds' => 'float',
        'sample_rate' => 'int',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
