<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabel extends FormRequest
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
            'moniker' => 'max:255',
            'alt_moniker' => 'max:255',
            'email' => 'email',
            'city' => 'max:255',
            'territory' => 'max:255',
            'country_code' => 'exists:countries,code',
            'official_url' => 'url',
            'profile_url' => 'max:64', // TODO This requires further validation, to prevent tomfoolery, profanity, etc..
        ];
    }
}
