<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Task', [
            'tasks' => Task::all(),
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
        $task = $request->validate([
            'title' => 'required|min:4',
            'description' => 'nullable|max:255',
        ]);

        Task::create($task);

        return redirect()->route('task.index');
    }

    public function markAsDone (Request $request, Task $task)
    {
        $task->status = 'done';
        $task->save();

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|min:4',
            'description' => 'nullable|max:255',
        ]);

        $task->update($data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return back();
    }
}
