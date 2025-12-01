<?php

namespace App\Http\Controllers\Api;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function index()
    {
        return response()->json(Note::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'nullable'
        ]);

        $note = Note::create($data);

        return response()->json($note, 201);
    }

    public function show(Note $note)
    {
        return response()->json($note);
    }

    public function update(Request $request, Note $note)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'nullable'
        ]);

        $note->update($data);

        return response()->json($note);
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
