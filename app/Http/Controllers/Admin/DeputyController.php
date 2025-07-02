<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DeputyController extends Controller
{
    /**
     * Hiển thị trang thêm phó tổng giám đốc
     */
    public function create()
    {
        $pageTitle = "Thêm Phó Tổng Giám Đốc";
        return view('admin.deputy.create', compact('pageTitle'));
    }

    /**
     * Xử lý tạo mới tài khoản phó tổng giám đốc
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:40',
            'username' => 'required|string|max:40|unique:admins',
            'email' => 'required|email|max:40|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Tạo tài khoản phó tổng giám đốc
        $deputy = new Admin();
        $deputy->name = $request->name;
        $deputy->username = $request->username;
        $deputy->email = $request->email;
        $deputy->password = Hash::make($request->password);
        $deputy->role = 'deputy'; // Gán vai trò là phó tổng giám đốc
        $deputy->save();

        $notify[] = ['success', 'Tạo tài khoản phó tổng giám đốc thành công'];
        return back()->withNotify($notify);
    }

    /**
     * Hiển thị danh sách phó tổng giám đốc
     */
    public function index()
    {
        $pageTitle = "Danh Sách Phó Tổng Giám Đốc";
        $deputies = Admin::where('role', 'deputy')->paginate(getPaginate());
        return view('admin.deputy.index', compact('pageTitle', 'deputies'));
    }

    /**
     * Xóa tài khoản phó tổng giám đốc
     */
    public function delete($id)
    {
        $deputy = Admin::where('role', 'deputy')->findOrFail($id);
        $deputy->delete();

        $notify[] = ['success', 'Xóa tài khoản phó tổng giám đốc thành công'];
        return back()->withNotify($notify);
    }
}
