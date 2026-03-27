<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'source'           => 'nullable|string|max:255',
            'location'         => 'nullable|string|max:255',
            'cookingHabit'     => 'nullable|string|max:255',
            'groceryFrequency' => 'nullable|string|max:255',
            'painPoints'       => 'nullable|string',
            'hassleScore'      => 'nullable|integer|min:0|max:10',
            'likelihoodScore'  => 'nullable|integer|min:0|max:10',
            'feePref'          => 'nullable|string|max:255',
            'deliveryPref'     => 'nullable|string|max:255',
        ];
    }

    /**
     * Return validated data with keys converted to snake_case
     * so they map directly onto the model's $fillable.
     */
    public function validatedSnake(): array
    {
        return collect($this->validated())
            ->mapWithKeys(fn ($value, $key) => [Str::snake($key) => $value])
            ->toArray();
    }
}
