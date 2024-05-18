<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function read($materialId) {
        $submissions = Submission::where('material_id', $materialId)->get();
        return $this->jsonResponse(['message' => 'Semua pengumpulan di materi ini ditemukan', 'submissions' => $submissions]);
    }

    public function readById($id) {
        $submission = Submission::find($id);
        return $this->jsonResponse(['message' => 'Pengumpulan ditemukan', 'submission' => $submission]);
    }

    public function create(Request $request) {
        // Validate the require inputs
        $request->validate([
            'user_id' => 'required',
            'material_id' => 'required',
        ]);

        // Validate that at least one of file and comment field is filled
        if (!$request->input('file') && !$request->input('comment')) {
            return $this->jsonResponse(['message' => 'Isi pengumpulan tidak boleh kosong'], 400);
        }

        // Save the file to storage if it exists
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/submissions');
            $filename = basename($path);
        }

        // Create the submission
        $newId = Str::uuid();
        Submission::create([
            'id' => $newId,
            'user_id' => $request->input('user_id'),
            'material_id' => $request->input('material_id'),
            'file' => $filename ?? null,
            'comment' => $request->input('comment') ?? null,
            'grade' => null,
        ]);

        $submission = Submission::where('id', $newId)->first();

        return $this->jsonResponse(['message' => 'Pengumpulan berhasil dibuat', 'submission' => $submission]);
    }

    public function update(Request $request, $id) {
        Submission::find($id)->update($request->all());
        return $this->jsonResponse(['message' => 'Pengumpulan berhasil diperbarui']);
    }

    public function delete($id) {
        Submission::find($id)->delete();
        return $this->jsonResponse(['message' => 'Pengumpulan berhasil dihapus']);
    }
}
