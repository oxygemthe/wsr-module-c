<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserFilesResource;
use App\Http\Resources\UserSharedResource;
use App\Models\File;
use App\Models\FileAccess;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
            $file_name = $file->getClientOriginalName();

            $check_name_exists = File::checkNameExists($request->user()->id, $file_name);
            for ($count = 1; $check_name_exists; $count++) {
                $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                    . " ($count)." . $file->getClientOriginalExtension();
                $check_name_exists = File::checkNameExists($request->user()->id, $file_name);
            }

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
                    'url' => route('get-file', ['file' => $fileModel->file_id]),
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
        $request->validate([
            'name' => 'required'
        ]);
        if ($request->user()->id != $file->accesses()->where('type', 'author')->first()->author->id) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403);
        }
        $file_name = $request->get('name') . "." . pathinfo($file->src, PATHINFO_EXTENSION);

        $check_name_exists = File::checkNameExists($request->user()->id, $file_name);
        for ($count = 1; $check_name_exists; $count++) {
            $file_name = $request->get('name') . " ($count)." . pathinfo($file->src, PATHINFO_EXTENSION);
            $check_name_exists = File::checkNameExists($request->user()->id, $file_name);
        }

        $file->update([
            'name' => $file_name
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Renamed'
        ]);
    }

    public function deleteFile(File $file, Request $request)
    {
        if ($request->user()->id != $file->accesses()->where('type', 'author')->first()->author->id) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403);
        }
        $file->delete();
        return response()->json([
            'success' => true,
            'message' => 'File already deleted' // не по инглишу)) File has deleted
        ]);
    }

    public function getFile(File $file, Request $request)
    {
        if ($file->accesses()->where('fk_author', $request->user()->id)->first() == null) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403);
        }
        return Storage::download($file->src, $file->name);
    }

    public function getUserFiles(Request $request)
    {
        $files = File::query()
            ->whereHas('accesses', function($query) use ($request) {
                $query->where('type', 'author');
                $query->where('fk_author', $request->user()->id);
            })->get();
        return UserFilesResource::collection($files);
    }

    public function getSharedFiles(Request $request)
    {
        $files = File::query()
            ->whereHas('accesses', function($query) use ($request) {
                $query->where('type', 'co_author');
                $query->where('fk_author', $request->user()->id);
            })->get();
        return UserSharedResource::collection($files);
    }
}
