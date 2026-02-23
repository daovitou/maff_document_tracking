<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
        // if(Auth::guard('admin')->user()->is_system == null){
        //     return redirect()->route('admin.signin');
        // }
    }
    public function any()
    {
        $user = Auth::guard('admin')->user();
        return $user ? $user->is_system : false;
    }
    public function create()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("create-user", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("create-user", $permissions);
        }
    }
    public function edit()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("edit-user", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("edit-user", $permissions);
        }
    }
    public function delete()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("delete-user", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("delete-user", $permissions);
        }
    }
    public function view()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("view-user", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("view-user", $permissions);
        }
    }
    public function forceDelete()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("soft-delete-user", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("soft-delete-user", $permissions);
        }
    }
}
