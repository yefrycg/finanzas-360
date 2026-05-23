<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Category::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');
        if (is_string($name)) {
            $this->merge(['name' => Str::of($name)->squish()->toString()]);
        }
    }

    public function rules(): array
    {
        $allowedIcons = array_map(
            static fn(array $icon) => $icon[0],
            config('categories.icons', [])
        );

        $iconRules = ['required', 'string', 'max:100', 'regex:/^(fas|far|fab)\s+fa-[a-z0-9-]+$/i'];
        if (count($allowedIcons) > 0) {
            $iconRules[] = Rule::in($allowedIcons);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => $iconRules,
        ];
    }
}
