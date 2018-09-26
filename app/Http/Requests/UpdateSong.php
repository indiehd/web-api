<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSong extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'alt_name' => 'string|max:255',
            'flac_file' => 'file|max:2000000',
            'track_number' => 'integer|max:99',
            'preview_start' => 'between:0.000,9999.999',
            'is_active' => 'boolean',
            'album_id' => 'exists:albums,id',
        ];
    }
}
