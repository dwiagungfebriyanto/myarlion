<?php

namespace App\Http\Controllers;

use App\DataTables\RoleDatatable;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RoleDatatable $roleDatatable)
    {
        $data['pageTitle'] = 'Role';
        $data['permissions'] = Permission::orderByRaw("SUBSTRING_INDEX(name, ' ', -1) ASC")->get();

        return $roleDatatable->render('pages.role.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_name'     => 'required|string|unique:App\Models\Role,name',
            'permissions.*' => 'exists:App\Models\Permission,id|nullable',
        ]);

        $role = new Role;
        $role->name       = $request->role_name;
        $role->guard_name = 'web';

        if ($role->save()) {
            $role->givePermissionTo($request->permissions);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $permissionNames = '';

        foreach ($role->getPermissionNames() as $key => $value) {
            $permissionNames .= "<div class='custom-control custom-checkbox col-md-6 mt-1'>"
                                    ."<input type='checkbox' class='custom-control-input' checked disabled>"
                                    ."<label class='custom-control-label'>$value</label>"
                                ."</div>";
        }

        $data['role'] = $role;
        $data['rolePermissions'] = $permissionNames;

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderByRaw("SUBSTRING_INDEX(name, ' ', -1) ASC")->get();

        $permissionOptions = '';

        foreach ($permissions as $permission) {
            $isChecked = in_array($permission->name, $role->getPermissionNames()->toArray())
                        ? 'checked'
                        : '';

            $permissionOptions .= '<div class="custom-control custom-checkbox col-md-6 mt-1">
                                        <input type="checkbox" class="custom-control-input" name="permissions[]"
                                            id="permissions' .$permission->id .'" value="' .$permission->id .'" ' .$isChecked .'>
                                        <label class="custom-control-label"
                                            for="permissions' .$permission->id .'">' .$permission->name .'</label>
                                    </div>';
        }

        $data['role'] = $role;
        $data['permissionOptions'] = $permissionOptions;

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name'     => 'required|string|unique:App\Models\Role,name,' .$role->id,
            'permissions.*' => 'exists:App\Models\Permission,id|nullable',
        ]);

        $role->name       = $request->role_name;

        if ($role->save()) {
            $role->revokePermissionTo($role->permissions);

            $role->givePermissionTo($request->permissions);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role)
    {
        $role->delete();
    }
}
