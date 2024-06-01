<?php

namespace App\Http\Controllers\AdminController;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){

        $users = User::latest()->get();

        return view('user.index',['users'=>$users]);

    }


    public function create(){

        return view('user.create');
    }


    public function store(Request $request){

        $validateData = $request->validate([
            'username'=>'required',
            'email'=>'required',
            'password'=>'required',
            'first_name'=>'required',
            'last_name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'role'=>'required',
        ]);

        User::create($validateData);

        return redirect()->route('user.index')->with('success','Data Berhasil di Tambahkan');

    }

    public function edit(User $user){

        return view('user.edit', ['user'=>$user]);
        
    }

    public function update(Request $request, User $user){

        $validateData = $request->validate([
            'username'=>'required',
            'email'=>'required',
            'password'=>'required',
            'first_name'=>'required',
            'last_name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'role'=>'required',
        ]);

        $user->update($validateData);

        return redirect()->route('user.index')->with('success','Data Berhasil di Ubah');
    }

    public function destroy(User $user){

        $user->delete();
        return redirect()->route('user.index')->with('success','Data Berhasil di Hapus');
        
    }

}
