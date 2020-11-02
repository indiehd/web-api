<?php

namespace App\Http\Requests;

use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAlbum extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (is_null($this->user())) {
            return false;
        }

        $albumRepository = resolve(AlbumRepositoryInterface::class);

        $album = $albumRepository->findById($this->route('id'));

        return $album && $this->user()->can('update', $album);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|max:255',
            'alt_title' => 'string|max:255',
            'year' => 'integer|min:1900|max:'.(string) (date('Y') + 1),
            'description' => 'max:4096',
            'has_explicit_lyrics' => 'boolean',
            'full_album_price' => 'between:0,99999',
            'is_active' => 'boolean',
        ];
    }
}
