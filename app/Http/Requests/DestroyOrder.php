<?php

namespace App\Http\Requests;

use App\Contracts\OrderRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class DestroyOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $repository = resolve(OrderRepositoryInterface::class);

        $model = $repository->findById($this->route('id'));

        return ! is_null($model);
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
