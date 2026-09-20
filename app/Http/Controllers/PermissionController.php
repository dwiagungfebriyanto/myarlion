<?php

namespace App\Http\Controllers;

use App\DataTables\PermissionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PermissionDataTable $permissionDataTable)
    {
        $data['pageTitle'] = 'Permission';

        return $permissionDataTable->render('pages.permission.index', $data);
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
            'permission_name' => 'required|string|unique:App\Models\Permission,name'
        ]);

        $permission = new Permission;
        $permission->name = $request->permission_name;
        $permission->guard_name = 'web';
        $permission->save();

        Artisan::call('cache:clear');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return response()->json($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'permission_name' => 'required|string|unique:App\Models\Permission,name,' .$permission->id
        ]);

        $permission->name = $request->permission_name;
        $permission->guard_name = 'web';
        $permission->save();

        Artisan::call('cache:clear');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Permission $permission)
    {
        if ($permission->delete()) {
            return response()->json('success');
        }

        return response()->json('failed', 422);
    }
}
