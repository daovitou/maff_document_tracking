<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DepartmentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function any()
    {
        $user = Auth::guard('admin')->user();
        return $user ? $user->is_system : false;
    }
    public function create()
    {
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("create-department", $permissions);
        }
    }
    public function edit()
    {
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("edit-department", $permissions);
        }
    }
    public function delete()
    {
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("delete-department", $permissions);
        }
    }
    public function view()
    {
        // return Auth::guard('admin')->user()->is_system ? true : false;
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("view-department", $permissions);
        }
    }
    public function forceDelete()
    {
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("soft-delete-department", $permissions);
        }
    }
}
