<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' =>['required','unique:categories','min:3'], 
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|nullable'
        ];

        
    }
    public function messages()
    {
        return [
            'name.required' => 'The category name is required.',
            'name.min' => 'The category name must be at least 3 characters.',
            'name.unique' => 'The category name must be unique.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a type of jpeg, png, jpg, or gif.',
            'image.max' => 'The image size must not exceed 2MB.',
        ];
    }
}
