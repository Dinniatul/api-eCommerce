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


    public function store(Request $request)
{
    $validateData = $request->validate([
        'username' => 'required|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8', // Ensure the password is secure
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'role' => 'required',
    ]);

    // Hash the password before storing
    $validateData['password'] = bcrypt($validateData['password']);

    User::create($validateData);

    return redirect()->route('user.index')->with('success', 'Data Berhasil di Tambahkan');
}


    public function edit(User $user){

        return view('user.edit', ['user'=>$user]);
        
    }

    public function update(Request $request, User $user)
    {
        $validateData = $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8', // Password is optional during update
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'role' => 'required',
        ]);
    
        // Hash the password only if it is provided
        if ($request->filled('password')) {
            $validateData['password'] = bcrypt($request->password);
        } else {
            // Remove the password key from the validated data
            unset($validateData['password']);
        }
    
        // Update the user with the validated data
        $user->update($validateData);
    
        return redirect()->route('user.index')->with('success', 'Data Berhasil di Ubah');
    }

    public function destroy(User $user){

        $user->delete();
        return redirect()->route('user.index')->with('success','Data Berhasil di Hapus');
        
    }

}
