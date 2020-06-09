<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlacFile extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'bitrate' => 'decimal:7',
        'bits_per_sample' => 'int',
        'compression_ratio' => 'decimal:14',
        'file_size' => 'int',
        'is_lossless' => 'bool',
        'num_channels' => 'int',
        'play_time_seconds' => 'decimal:7',
        'sample_rate' => 'int',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
