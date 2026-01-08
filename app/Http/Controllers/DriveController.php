<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Inertia\Inertia;
use App\Models\Drive;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriveController extends Controller
{
    

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Drive', [
            'folders' => Folder::where('user_id', Auth::id())->get(),
            'files' => Item::where('user_id', Auth::id())->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        if($request->name){
            $folder = $request->validate([
                'name' => 'required|min:4',
            ]);

            Folder::create([...$folder, 'user_id' => Auth::id()]);

            return back();
        }else{
            

            
            $file = $request->validate([
                'title' => 'required|min:4',
                'description' => 'nullable|max:255',
                'folder_id' => 'nullable|exists:folders,id',
                'file_name' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            ]);

            if($request->hasFile('file_name')){
                $file['file_name'] = $request->file('file_name')->store('drive', 'public');
            }

            Item::create([
                ...$file,
                'user_id' => Auth::id()
            ]);

            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Drive $drive)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Drive $drive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Drive $drive)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drive $drive)
    {
        //
    }
}
