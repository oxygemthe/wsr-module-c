<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserFileAccessesResource;
use App\Models\File;
use App\Models\FileAccess;
use App\Models\User;
use Illuminate\Http\Request;

class FileAccessController extends Controller
{
    public function provideAccess(File $file, Request $request)
    {
        $request->validate([
           'email' => 'required|email|exists:users,email'
        ]);
        if ($request->user()->id != $file->accesses()->where('type', 'author')->first()->author->id) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403);
        }
        $co_author = User::query()->where('email', $request->get('email'))->firstOrFail();
        FileAccess::query()->firstOrCreate([
            'type' => 'co_author',
            'fk_file' => $file->id,
            'fk_author' => $co_author->id
        ]);
        return UserFileAccessesResource::collection($file->accesses);
    }

    public function takeAwayAccess(File $file, Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        if ($request->user()->id != $file->accesses()->where('type', 'author')->first()->author->id) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403);
        }
        $co_author = User::query()->where('email', $request->get('email'))->firstOrFail();
        if ($request->user()->id == $co_author->id) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403);
        }
        $file->accesses()->where('fk_author', $co_author->id)->firstOrFail()->delete();
        return UserFileAccessesResource::collection($file->accesses);
    }
}
