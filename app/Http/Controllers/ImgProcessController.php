<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImgProcessController extends Controller
{
    public function process(Request $request){

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2000',
            'operation'=> 'required|string  ',
        ]);

        $response=Http::attach('image',
        $request->file('image')->getContent(),
        $request->file('image')->getClientOriginalName()
        )->post('http://127.0.0.1:5000/process-image', ['operation' =>$request->input('operation')]);

        if($response->failed()){
            return response()->json(['error' => 'gagal'],0);
        }

        $PI= $response -> body();
        $file = 'processed_img.jpg';
        Storage::put("public/images/$file", $PI);
        return response()->json(['success'=> 'sukses', 'file'=> asset("storage/images/$file")]);
    }
}
