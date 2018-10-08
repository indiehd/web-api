<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
    public function rules(/*StoreAccount $account*/)
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8|max:4096',
            'account.email' => 'required|email',
            'account.first_name' => 'max:64',
            'account.last_name' => 'max:64',
            'account.address_one' => 'max:255',
            'account.address_two' => 'max:255',
            'account.city' => 'max:64',
            'account.territory' => 'max:64',
            'account.country_code' => 'max:3',
            'account.postal_code' => 'max:64',
            'account.phone' => 'max:64',
            'account.alt_phone' => 'max:64',
        ];
    }
}
