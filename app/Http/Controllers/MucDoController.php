<?php
namespace App\Http\Controllers;

use App\Models\MucDo;
use Illuminate\Http\Request;

class MucDoController extends Controller
{
    public function ajaxCreate(Request $request)
    {
        $request->validate([
            'ten_muc_do' => 'required|string|unique:muc_do,ten_muc_do',
        ]);

        $mucDo = MucDo::create([
            'ten_muc_do' => $request->ten_muc_do
        ]);

        return response()->json([
            'status' => 'success',
            'id' => $mucDo->id,
            'ten_muc_do' => $mucDo->ten_muc_do
        ]);
    }

    public function ajaxDelete(Request $request)
    {
        $mucDo = MucDo::find($request->id);

        if (!$mucDo) return response()->json(['status'=>'error']);

        if ($mucDo->thongBaos()->count()) {
            return response()->json(['status'=>'error']);
        }

        $mucDo->delete();
        return response()->json(['status'=>'success']);
    }
}
