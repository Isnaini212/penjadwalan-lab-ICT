<?php

namespace App\Http\Controllers;
use App\Models\Lab;
use Illuminate\Http\Request;

class LabController extends Controller
{
    
    public function manajemenLab(Request $request) {
        $labs = Lab::all();
        return view('spv.lab', compact('labs'));
    }

    
    public function buatLab(Request $request)
    {
        $messages = [
            'nama_lab.unique'   => 'Nama laboratorium tersebut sudah terdaftar di sistem!',
            'nama_lab.required' => 'Nama lab wajib diisi.',
            'kapasitas.required'=> 'Kapasitas wajib diisi.',
            'kapasitas.numeric' => 'Kapasitas harus berupa angka.',
            'kapasitas.min'     => 'Kapasitas minimal adalah 1 kursi.',
            'fasilitas.required'=> 'Fasilitas wajib diisi.',
        ];

         $request->validate([
            'nama_lab'     => 'required|unique:labs,nama_lab',
            'kapasitas'     => 'required|numeric|min:1',
            'fasilitas'     => 'required',
        ]);


        Lab::create([
            'nama_lab'     =>  ucwords(strtolower($request->nama_lab)),
            'kapasitas'     => $request->kapasitas,
            'fasilitas'     => $request->fasilitas,
        ]);

        return back()->with('success', 'Data Laboratorium berhasil ditambahkan!');
    }


    public function update(Request $request, $id_Lab)
    {
         $labs = Lab::findOrFail($id_Lab);

         $messages = [
            'nama_lab.unique'   => 'Nama laboratorium tersebut sudah digunakan oleh lab lain!',
            'nama_lab.required' => 'Nama lab wajib diisi.',
            'kapasitas.required'=> 'Kapasitas wajib diisi.',
            'kapasitas.numeric' => 'Kapasitas harus berupa angka.',
            'kapasitas.min'     => 'Kapasitas minimal adalah 1 kursi.',
            'fasilitas.required'=> 'Fasilitas wajib diisi.',
        ];

         $request->validate([
            'nama_lab'  => 'required|unique:labs,nama_lab,' . $id_Lab . ',id_lab',
            'kapasitas' => 'required|numeric|min:1',
            'fasilitas' => 'required',
         ], $messages);

          $labs->update([
            'nama_lab'     =>  ucwords(strtolower($request->nama_lab)) ?? $labs->nama_lab,
            'kapasitas'     => $request->kapasitas ?? $labs->kapasitas,
            'fasilitas'   =>  $request->fasilitas ?? $labs->fasilitas,
        ]);

        return back()->with('success', 'Data Laboratorium berhasil diperbarui!');

    }

    
    public function destroy($id_lab)
    {
        $labs = Lab::findOrFail($id_lab);
        $labs->delete();

        return back()->with('success', 'Lab berhasil dihapus!');
    }
}
