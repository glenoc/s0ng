<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('superAdminSetup');
        $this->middleware('can:isSuperAdmin')->only(['index',  'create', 'store', 'destroy']);
    }

    function superAdminSetup(){
        if (User::count() == 0 && $_GET['password'] <> ''){
            $user = new User();
            $user->name = 'goc';
            $user->username = 'goc';
            $user->email = 'goc';
            $user->password = Hash::make($_GET['password']);
            $user->isSuperAdmin = 1;
            $user->save();
        }
        return abort(404);
    }

    function index(){
        if (Auth::user()->isSuperAdmin()){
            $users = User::all();
        }else{
            $users = User::where('super_admin', 0)->get();
        }
        return view('users.index')->with(['users' => $users]);
    }

    function show(User $user){
        return $user;
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:191|unique:users',
            'userLevel' => 'sometimes|integer|between:1,3',
            'password' => 'sometimes|nullable|string|min:6|confirmed']);

        $user = new User();
        $user->name = $request->username;
        $user->username = $request->username;
        $user->email = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('users.index')->with('notificationSuccess', 'User updated');
    }


    public function edit(User $user)
    {
        $isMe = (Auth::user()->id == $user->id);
        return view('users.edit', ['user' => $user, 'isMe' => $isMe]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:191|unique:users,username,' . $user->id,
            'userLevel' => 'sometimes|integer|between:1,3',
            'password' => 'sometimes|nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->username;
        $user->username = $request->username;
        $user->email = $request->username;
        if (isset($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('home');
    }

    public function destroy(User $user)
    {
        if (Auth::user() != $user) {
            $user->delete();
        }
        return redirect()->route('users.index');
    }
}
