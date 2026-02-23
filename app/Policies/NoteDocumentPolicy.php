<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NoteDocumentPolicy
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
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("create-note-document", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("create-note-document", $permissions);
        }
    }
    public function edit()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("edit-note-document", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("edit-note-document", $permissions);
        }
    }
    public function delete()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("delete-note-document", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("delete-note-document", $permissions);
        }
    }
    public function view()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("view-note-document", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("view-note-document", $permissions);
        }
    }
    public function forceDelete()
    {
        // $permissions = Auth::guard('admin')->user()->role->permissions ?? [];
        // return in_array("soft-delete-note-document", $permissions);
        if (Auth::guard('admin')->user()->is_system) {
            return true;
        } else {
            $permissions = Auth::guard('admin')->user()->role->permissions;
            return in_array("soft-delete-note-document", $permissions);
        }
    }
}
