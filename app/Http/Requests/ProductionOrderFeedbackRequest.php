<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionOrderFeedbackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fg_completed_qty' => [
                'required',
                'numeric',
                'min:0.000001',
            ],
        ];
    }

    public function messages()
    {
        return [
            'fg_completed_qty.required' => 'Feedback quantity is required.',
            'fg_completed_qty.numeric' => 'Feedback quantity must be numeric.',
            'fg_completed_qty.min' => 'Feedback quantity must be greater than 0.',
        ];
    }
}
