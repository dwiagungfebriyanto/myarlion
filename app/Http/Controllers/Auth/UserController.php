<?php

namespace App\Http\Controllers\Auth;

use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('pages.account.index');
    }

    /**
     * Show the form for creating a new resource.
     */

    //  create user
    public function create(): View
    {
        $roles = Role::all();

        return view('pages.account.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()],
        ];
        $request->validate($rules);

        $user = new User;
        $user->name = $request->name;
        $user->position = $request->position;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role_id = $request->role;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            $role = Role::find($request->role);
            $user->assignRole($role->name);

            $request->session()->flash('success', 'New User data successfully added.');
        } else {
            $request->session()->flash('danger', 'Change a few things up and try submitting again.');
        }

        return redirect(route('account.createSign', $user));
    }

    // create signature
    public function createSign($id)
    {
        $data['user'] = User::find($id);
        // dd(gettype($user->password));
        return view('pages.account.create-signature', $data);
    }


    public function uploadSign(Request $request, $id)
    {
        $rules = [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $request->validate($rules);

        $user = User::find($id);

        $file = $request->file('file');
        $filename = $user->username . '-' . time() . '.' . $file->extension();
        $file->move(public_path('images/signature_photo'), $filename);

        $user->signature = $filename;
        $user->save();
    }


    /**
     * Show the form for editing the specified resource.
     */
    // Edit user
    public function edit($id)
    {
        $data['roles'] = Role::all();
        $data['user'] = User::find($id);

        // dd($data);
        return view('pages.account.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $rules = [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required',
            'password' => ['nullable', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()],
        ];
        $request->validate($rules);

        $user->name = $request->name;
        $user->position = $request->position;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role_id = $request->role;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }


        if ($user->save()) {
            $user->syncRoles($request->role);

            if ($request->password) {
                activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->useLog('user')
                ->event('password_changed')
                ->log('password_changed');
            }

            $request->session()->flash('success', 'User data successfully updated.');
        }

        return redirect(route('account.editSign', $user));
    }

    public function editSign($id)
    {
        $data['user'] = User::find($id);
        return view('pages.account.edit-signature', $data);
    }

    public function editPermission(User $user)
    {
        $permissions = Permission::orderByRaw("SUBSTRING_INDEX(name, ' ', -1) ASC")->get();

        $permissionOptions = '';

        foreach ($permissions as $permission) {
            $isChecked = in_array($permission->name, $user->getPermissionNames()->toArray())
                        ? 'checked'
                        : '';

            $permissionOptions .= '<div class="custom-control custom-checkbox col-md-6 mt-1">'
                                        .'<input type="checkbox" class="custom-control-input" name="permissions[]"'
                                            .'id="permissions' .$permission->id .'" value="' .$permission->id .'" ' .$isChecked .'>'
                                        .'<label class="custom-control-label"'
                                            .'for="permissions' .$permission->id .'">' .$permission->name .'</label>'
                                    .'</div>';
        }

        $data['updatePermissionUrl'] = route('account.update_permission', $user);
        $data['user']                = $user;
        $data['permissionOptions']   = $permissionOptions;

        return response()->json($data);
    }

    public function updatePermission(Request $request, User $user)
    {
        $request->validate([
            'permissions.*' => 'exists:App\Models\Permission,id|nullable',
        ]);

        $oldPermissions = $user->permissions->pluck('name', 'id');

        $user->revokePermissionTo($user->permissions);
        $user->givePermissionTo($request->permissions);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties([
                'old' => $oldPermissions,
                'new' => $request->permissions,
            ])
            ->useLog('user')
            ->event('user_permission_changed')
            ->log('user_permission_changed');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('account.index')->with('success', 'User successfully deleted');
    }
}
