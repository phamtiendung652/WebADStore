<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);

        $viewData = [
            'users' => $users
        ];

        return view('admin.user.index', $viewData);
    }

    public function transaction(Request $request, $id)
    {
        if ($request->ajax()) {
            $transactions = Transaction::where([
                'tst_user_id' => $id,
            ])->whereIn('tst_status', [1, 2])
                ->orderByDesc('id')
                ->paginate(10);

            $view = view('admin.user.transaction', compact('transactions'))->render();

            return response()->json(['html' => $view]);
        }
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) $user->delete();

        return redirect()->back();
    }
    public function changeStatus($id, $status)
    {
        $user = User::find($id);

        if (!$user) {
            \Session::flash('toastr', [
                'type'    => 'error',
                'message' => 'Không tìm thấy người dùng.'
            ]);
            return redirect()->back();
        }

        // Đảm bảo status là 0 hoặc 1
        if (!in_array($status, [0, 1])) {
            \Session::flash('toastr', [
                'type'    => 'error',
                'message' => 'Trạng thái không hợp lệ.'
            ]);
            return redirect()->back();
        }

        $user->status = $status;
        $user->save();

        $message = ($status == 0) ? 'Tài khoản người dùng đã được ngưng hoạt động.' : 'Tài khoản người dùng đã được kích hoạt lại.';
        \Session::flash('toastr', [
            'type'    => 'success',
            'message' => $message
        ]);

        return redirect()->back();
    }
}
