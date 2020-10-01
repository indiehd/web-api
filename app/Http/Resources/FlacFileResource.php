<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlacFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'file_size' => $this->file_size,
            'file_format' => $this->file_format,
            'data_format' => $this->data_format,
            'bitrate_mode' => $this->bitrate_mode,
            'is_lossless' => $this->is_lossless,
            'channels_count' => $this->num_channels,
            'sample_rate' => $this->sample_rate,
            'bits_per_sample' => $this->bits_per_sample,
            'bitrate' => $this->bitrate,
            'channel_mode' => $this->channel_mode,
            'encoder' => $this->encoder,
            'compression_ratio' => $this->compression_ratio,
            'encoding' => $this->encoding,
            'mime_type' => $this->mime_type,
            'play_time_seconds' => $this->play_time_seconds,
            'md5_data_source' => $this->md5_data_source,
            'sha256' => $this->sha256,
        ];
    }
}
