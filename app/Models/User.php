<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Cek apakah user (lewat role-nya) punya izin `$action` di module `$moduleSlug`.
     * Pakai relasi yang sudah di-load (lewat LoadUserPermissions middleware) supaya
     * tidak query ulang ke DB tiap kali dipanggil -> hindari N+1.
     */
    public function hasPermission(string $moduleSlug, string $action): bool
{
    if (!$this->relationLoaded('role')) {
        $this->load('role.permissions.module');
    }

    $permissionRow = $this->role?->permissions
        ->first(fn ($p) => $p->module?->slug === $moduleSlug);

    if (!$permissionRow) {
        return false;
    }

    return (bool) ($permissionRow->permission[$action] ?? false);  // 
}

    /**
     * Return peta permission lengkap milik role user, bentuknya:
     * ['flows' => ['create' => true, 'read' => true, ...], ...]
     */
    public function permissionMap(): array
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role.permissions.module');
        }

        $map = [];

        foreach ($this->role?->permissions ?? [] as $permissions) {
            if ($permissions->module) {
                $map[$permissions->module->slug] = $permissions->permission;
            }
        }

        return $map;
    }
}