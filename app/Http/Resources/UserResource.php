<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role?->name,

            // FE tinggal loop ini buat render sidebar/menu & cek hak akses per modul
            'modules' => $this->role?->permissions->map(function ($perm) {
                return [
                    'slug'       => $perm->module?->slug,
                    'name'       => $perm->module?->name,
                    'url'        => $perm->module?->url,
                    'sort_order' => $perm->module?->sort_order,
                    'permission' => $perm->permission, // {create, read, update, delete, ...}
                ];
            })->sortBy('sort_order')->values(),
        ];
    }
}