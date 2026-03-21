<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSaleRequest extends FormRequest
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
        $rules = [
            'formRadios' => 'required|in:yes,no',
            'product_id' => 'required|array',
            'product_id.*' => 'exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'numeric|regex:/[0-9]/i'
        ];

        if ($this->input('formRadios') == 'yes') {
            $rules['customer_id'] = 'required|exists:customers,id';
        } else {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'nullable|email';
            $rules['phone'] = 'nullable|string|max:10';
            $rules['address'] = 'nullable|string';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'You are required to pick a customer.',
            'customer_id.exists' => 'Customer does not exists.',
            'product_id.required' => 'You are required to pick at least one product.',
            'product_id.array' => 'Your product selection is invalid.',
            'product_id.*.exists' => 'Your selected product already exists',
            'quantity.required' => 'You must provide a quantity for the products.',
            'quantity.array' => 'The quantity format is invalid.',
            'quantity.*.numeric' => 'Every quantity provided must be a number.',
            'quantity.*.regex' => 'The quantity must contain only valid digits.',
            'name.required' => 'Customer name is required',
            'name.string' => 'Customer name must be a string',
            'name.max' => 'Customer name cannot exceed 255 characters',
            'email.email' => 'Email must follow the email format',
            'phone.max' => 'Phone Number cannot exceed 9 number. Exclude the "-" sign',
            'address.string' => 'String must be a string',
        ];
        
    }
}
