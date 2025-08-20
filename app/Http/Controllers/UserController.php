<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(){
        $data = User::paginate(10);

        return $data->toResourceCollection();
    }

    public function show($id){
        $user = User::find($id);

        return response($user);
    }

    public function store(Request $request)
    {   
        $validated = $request->validate([
            'username' => ['required',Rule::unique('users','username')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'min:8',],
            'role' => ['required', 'exists:roles,name'],
        ],[
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role harus dipilih.',
            'role.exists' => 'Role tidak valid.',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'status' => 'User berhasil ditambahkan',
        ]);
    }

    public function update(Request $request, $id)
    {   
        // return $request->all();
        $rules = [
            'username' => ['required',Rule::unique('users','username')->ignore($id)->whereNull('deleted_at')],
            'role' => ['required', 'exists:roles,name'],
        ];

        if($request->filled('current_password') || $request->filled('password')) {
            $rules['current_password'] = ['required'];
            $rules['password'] = ['required', Password::defaults()];
        }

        $request->validate($rules, [
            'username.unique' => 'Username sudah terdaftar.',
            'current_password.required' => 'Password lama harus diisi.',
            'password.required' => 'Password baru harus diisi.',
        ]);

        $user = User::findOrFail($id);

        // cek pw lama
        if ($request->filled('current_password') || $request->filled('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Password lama tidak sesuai.'],
                ]);
            }
            $user->password = Hash::make($request->password);
        }

        // update username kalau ada
        if ($request->filled('username')) {
            $user->username = $request->username;
        }

        if ($request->filled('role')) {
            $user->syncRoles($request->role);
        }

        $user->save();
        

        return response()->json([
            'status' => 'Data berhasil diubah',
        ]);
    }

    public function destroy($id) #delete
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus',
        ]);
    }

    public function getRoles()
    {
        $role = Role::all()->pluck('name');

        return $role;
    }

    public function getUserRole($id)
    {
        $user = User::findOrFail($id);

        $role = $user->getRoleNames();

        return response()->json([
            'role' => $role[0]
        ],200);
    }
}
