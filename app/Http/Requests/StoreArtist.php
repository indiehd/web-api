<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtist extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'moniker' => 'required|max:255',
            'alt_moniker' => 'sometimes|required|max:255',
            'email' => 'sometimes|required|email',
            'city' => 'sometimes|required|max:255',
            'territory' => 'sometimes|required|max:255',
            'country_code' => 'sometimes|required|max:3',
            'official_url' => 'sometimes|required:max:255',
            'profile_url' => 'max:255', // This requires further validation, to prevent tomfoolery, profanity, etc...
        ];
    }
}
