<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Fileentry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileEntryController extends Controller
{
    public function index()
    {
        $entries = Fileentry::all();
        return view('fileentries.index', compact('entries'));
    }

    public function add(Request $request)
    {
        $file = $request->file('filefield');
        $extention = $file->getClientOriginalExtension();

        Storage::disk('local')->put($file->getFilename() . '.' . $extention,
            File::get($file));
        $entry = new Fileentry();
        $entry->mime = $file->getClientMimeType();
        $entry->original_filename = $file->getClientOriginalName();
        $entry->filename = $file->getFilename().'.'.$extention;

        $entry->save();

        return redirect('fileentry');
    }

    public function get($filename)
    {
        $entry = Fileentry::where('filename', '=', $filename)->firstOrFail();

        $file = Storage::disk('local')->get($entry->filename);
//dd(Response($file, 200)->header('Content-Type', $entry->mine));
        return Response($file, 200)->header('Content-Type', $entry->mine);
    }
}
