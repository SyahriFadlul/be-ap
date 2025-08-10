<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

    public function update(Request $request, $id){ 
        $data = $request->validate([

        ]);

        $originalData = User::find($request->id);

        
    }

    public function destroy(){ #delete

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
