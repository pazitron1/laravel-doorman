<?php

namespace Redsnapper\LaravelDoorman\Models\Traits;

use Exception;
use Redsnapper\LaravelDoorman\Models\Contracts\PermissionContract;
use Redsnapper\LaravelDoorman\PermissionsRegistrar;

trait IsRole
{
    use HasPermissions, HasUsers;

    /**
     * @param  PermissionContract|string  $permission
     * @throws Exception
     */
    public function givePermissionTo($permission)
    {
        $permissionId = $this->getPermissionId($permission);

        $this->permissions()->syncWithoutDetaching([$permissionId]);

        $this->forgetCachedPermissions();
    }

    /**
     * @param  PermissionContract|string  $permission
     * @return string
     * @throws Exception
     */
    public function getPermissionId($permission): string
    {
        if (is_string($permission)) {
            $permission = (app(PermissionsRegistrar::class)->getPermissionClass())
              ->findByName($permission)->getKey();
        }

        if ($permission instanceof PermissionContract) {
            $permission = $permission->getKey();
        }


        return $permission;
    }

    /**
     * Forget the cached permissions.
     */
    private function forgetCachedPermissions()
    {
        app(PermissionsRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param $permission
     * @throws Exception
     */
    public function removePermissionTo($permission)
    {
        $permissionId = $this->getPermissionId($permission);

        $this->permissions()->detach([$permissionId]);

        $this->forgetCachedPermissions();
    }

    /**
     * Does this role have this permission
     *
     * @param  PermissionContract|string  $permission
     * @return bool
     * @throws Exception
     */
    public function hasPermission($permission): bool
    {
        return $this->permissions->contains(app(PermissionsRegistrar::class)->getPermissionClass()->getKeyName(),
          $this->getPermissionId($permission));
    }
}
