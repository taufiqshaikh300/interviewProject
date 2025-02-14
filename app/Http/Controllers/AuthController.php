<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('registration');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|digits:10',
            'role' => 'required|in:organizer,attendee',
        ]);

        $emailHash = hash('sha256', strtolower($request->email));
        $phoneHash = hash('sha256', $request->phone);

        if (User::where('email_hash', $emailHash)->exists()) {
            return back()->withErrors(['email' => 'Email already taken.'])->withInput();
        }

        if (User::where('phone_hash', $phoneHash)->exists()) {
            return back()->withErrors(['phone' => 'Phone number already taken.'])->withInput();
        }

        User::create([
            'name' => Crypt::encryptString($request->name),
            'email' => Crypt::encryptString($request->email),
            'phone' => Crypt::encryptString($request->phone),
            'email_hash' => hash('sha256', strtolower($request->email)),
            'phone_hash' => hash('sha256', $request->phone),
            'password' => bcrypt($request->password),
            'role' => $request->role
        ]);

        // Fetch the user and check stored values


        return redirect()->route('auth.register')->with('success', 'Registration successful!');
    }



    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required',
            'password' => 'required',
        ]);

        $input = $request->email_or_phone;
        $password = $request->password;

        // Check if input is email or phone
        $emailHash = hash('sha256', strtolower($input));
        $phoneHash = hash('sha256', $input);


        $user = User::where('email_hash', $emailHash)
            ->orWhere('phone_hash', $phoneHash)
            ->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            return back()->with('error', 'Invalid credentials.');
        }

        // Store user info in session based on role
        session([
            'user_id' => $user->id,
            'user_name' => $user->name, // No need to decrypt (handled in model)
            'user_role' => $user->role,
        ]);

        // Redirect based on role
        return ($user->role === 'organizer') ?
            redirect()->route('organizer.dashboard') :
            redirect()->route('attendee.dashboard');
    }

    public function logout(Request $request)
    {
        // Clear session data
        Session::forget('user'); // Remove only the user session
        Session::flush(); // Clear all session data

        // Redirect to login page
        return redirect('/login');
    }
}
