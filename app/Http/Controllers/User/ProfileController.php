<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->user();
        return view('Template::user.profile_setting', compact('pageTitle', 'user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'date_of_birth' => 'nullable|date_format:d/m/Y',
            'id_number' => 'nullable|string|max:50',
            'id_issue_date' => 'nullable|date_format:d/m/Y',
            'id_issue_place' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_account_holder' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required',
            'date_of_birth.date_format' => 'The date of birth must be in dd/mm/yyyy format',
            'id_issue_date.date_format' => 'The issue date must be in dd/mm/yyyy format'
        ]);

        $user = auth()->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->date_of_birth = $request->date_of_birth ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->format('Y-m-d') : null;
        $user->id_number = $request->id_number;
        $user->id_issue_date = $request->id_issue_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->id_issue_date)->format('Y-m-d') : null;
        $user->id_issue_place = $request->id_issue_place;
        $user->bank_account_number = $request->bank_account_number;
        $user->bank_name = $request->bank_name;
        $user->bank_branch = $request->bank_branch;
        $user->bank_account_holder = $request->bank_account_holder;
        $user->tax_number = $request->tax_number;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view('Template::user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changes successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
