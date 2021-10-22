<?php

namespace App\Models\Traits;

trait Authorization {
    /**
     * Check if user has a permission by its name.
     *
     * @param array|string $permission Permission string or array of permissions.
     * @param bool         $requireAll All permissions in the array are required.
     * @return bool
     */
    public function hasPermission(array|string $permission, bool $requireAll = false): bool
    {
        if(is_array($permission)){
            foreach($permission as $permName){
                $hasPerm = $this->hasPermission($permName);

                if($hasPerm && !$requireAll){
                    return true;
                }elseif(!$hasPerm && $requireAll){
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        }else{
            $permissions = $this->mergedPermissions();
            if(isset($permissions['super-admin']) && $permissions['super-admin']){
                return $permissions['super-admin'];
            }elseif(isset($permissions[$permission])){
                return $permissions[$permission];
            }
        }
        return false;
    }

    public function hasRole(array|string $role, bool $requireAll = false)
    {
        if(is_array($role)){
            foreach($role as $roleName){
                $hasRole = $this->hasRole($roleName);

                if($hasRole && !$requireAll){
                    return true;
                }elseif(!$hasRole && $requireAll){
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        }else{
            if($this->roles->where('identity', $role)->first()){
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function mergedPermissions(): array
    {
        $permissions = [];

        foreach($this->roles as $role){
            foreach($role->permissions as $permission){
                $permissions[$permission->identity] = true;
            }
        }

        ksort($permissions);
        return $permissions;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasPermission('super-admin');
    }

}
