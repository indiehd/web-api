<?php

namespace App\Http\Requests;

use App\Contracts\ArtistRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class DestroyArtist extends FormRequest
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

        $repository = resolve(ArtistRepositoryInterface::class);

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
