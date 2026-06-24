<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        return response()->json(Karyawan::orderBy('id','desc')->get());
    }

    public function show($id)
    {
        $k = Karyawan::find($id);
        if (!$k) return response()->json(['error'=>'Not found'],404);
        return response()->json($k);
    }

    public function store(Request $r)
    {
        $data = $r->only(['nama','tgl_lahir','gaji']);
        $k = Karyawan::create($data);
        return response()->json(['id'=>$k->id],201);
    }

    public function update(Request $r, $id)
    {
        $k = Karyawan::find($id);
        if (!$k) return response()->json(['error'=>'Not found'],404);
        $k->update($r->only(['nama','tgl_lahir','gaji']));
        return response()->json(['updated'=>true]);
    }

    public function destroy($id)
    {
        $k = Karyawan::find($id);
        if (!$k) return response()->json(['error'=>'Not found'],404);
        $k->delete();
        return response()->json(['deleted'=>true]);
    }
}
