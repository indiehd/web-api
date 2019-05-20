<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Contracts\AlbumRepositoryInterface;

class DestroyAlbum extends FormRequest
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

        $repository = resolve(AlbumRepositoryInterface::class);

        $model = $repository->findById($this->route('id'));

        return $model && $this->user()->can('delete', $model);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
