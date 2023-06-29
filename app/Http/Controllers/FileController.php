<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:doc,pdf,docx,zip,jpeg,jpg,png|max:2048'
        ]);
        $files = [];
        foreach ($request->file('files') as $file) {
            $stored_file = Storage::putFile("files/{$request->user()->id}", $file);
            $file_name = File::toName($request->user()->id, $file);

            if ($stored_file) {
                $fileModel = File::query()->create([
                    'name' => $file_name,
                    'src' => $stored_file,
                    'file_id' => Str::random(10)
                ]);
                FileAccess::query()->create([
                    'type' => 'author',
                    'fk_file' => $fileModel->id,
                    'fk_author' => $request->user()->id
                ]);
                $files[] = [
                    'success' => true,
                    'message' => 'Success',
                    'name' => $fileModel->name,
                    'url' => route('get-file', ['file_id' => $fileModel->file_id]),
                    'file_id' => $fileModel->file_id
                ];
            } else {
                $files[] = [
                    'success' => false,
                    'message' => 'File not loaded',
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }
        return response()->json($files);
    }

    public function renameFile(File $file, Request $request)
    {

    }
}
