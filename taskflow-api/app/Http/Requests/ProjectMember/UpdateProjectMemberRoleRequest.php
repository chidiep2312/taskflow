<?php

namespace App\Http\Requests\ProjectMember;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectMemberRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'in:member,viewer'],
        ];
    }
}