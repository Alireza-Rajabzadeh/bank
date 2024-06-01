<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "origin_card_number"=>"required|string|card_number",
            "destination_card_number"=>"required|string|card_number|different:origin_card_number",
            "ammount"=>"required|integer|between:1000,50000000"
        ];
    }
}
