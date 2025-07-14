<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $data = User::all();

        return response($data);
    }

    public function show(Request $request){ #req or id ?
        $id = $request->id;
        $user = User::find($id);

        return response($user);
    }

    public function edit(Request $request){
        $data = $request->validate([

        ]);

        $originalData = User::find($request->id);

        
    }

    public function destroy(){ #delete

    }
}
