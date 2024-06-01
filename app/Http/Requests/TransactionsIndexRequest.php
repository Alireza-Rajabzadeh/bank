<?php

namespace App\Http\Requests;

use App\Http\Requests\Trait\ShowRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class TransactionsIndexRequest extends FormRequest
{
    use ShowRequestTrait;
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
        $rule = [
            "id" => "nullable|integer",
            "origin_card_id" => "nullable|integer",
            "parrent_transaction_id" => "nullable|integer",
            "type_id" => "nullable|integer",
            "status_id" => "nullable|integer",
            "destination_card_id" => "nullable|integer",
            "ammount" => "nullable|integer",
            "description" => "nullable|string",
        ];

        return array_merge($rule, $this->show_rules);
    }
}
