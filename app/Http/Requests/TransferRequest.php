<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class TransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from_user_id' => ['required', 'integer', 'different:to_user_id', 'exists:users,id'],
            'to_user_id'   => ['required', 'integer', 'different:from_user_id', 'exists:users,id'],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'comment' => ['nullable', 'string', 'max:255'],
        ];
    }
    public function authorize(): bool
    {
        return true;
    }
    public function messages(): array
    {
        return [
            'from_user_id.different' => 'from_user_id и to_user_id не могут совпадать.',
            'to_user_id.different'   => 'from_user_id и to_user_id не могут совпадать.',
        ];
    }
}
