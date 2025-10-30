<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class DepositRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
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
}
