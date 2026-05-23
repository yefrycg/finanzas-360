<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('category');

        if (! $category instanceof Category) {
            return false;
        }

        return $this->user()?->can('update', $category) ?? false;
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

    public function after(): array
    {
        return [function ($validator) {
            $category = $this->route('category');

            if (! $category instanceof Category) {
                return;
            }

            $newType = $this->input('type');
            if (! is_string($newType) || $newType === $category->type) {
                return;
            }

            $isInUse = $category->operations()->exists() || $category->goals()->exists();
            if ($isInUse) {
                $validator->errors()->add('type', 'No puedes cambiar el tipo de una categoría que ya está en uso.');
            }
        }];
    }
}
