<?php
namespace App\Http\Controllers;

use App\Models\TieuDe;
use Illuminate\Http\Request;

class TieuDeController extends Controller
{
    public function ajaxCreate(Request $request)
{
    $request->validate([
        'ten_tieu_de' => 'required|string|unique:tieu_de,ten_tieu_de'
    ]);

    $tieude = TieuDe::create(['ten_tieu_de' => $request->ten_tieu_de]);

    return response()->json([
        'status' => 'success',
        'id' => $tieude->id,
        'ten_tieu_de' => $tieude->ten_tieu_de
    ]);
}


    public function ajaxDelete(Request $request)
    {
        $tieude = TieuDe::find($request->id);
        if (!$tieude || $tieude->thongbaos()->count()) return response()->json(['status'=>'error']);
        $tieude->delete();
        return response()->json(['status'=>'success']);
    }
}
