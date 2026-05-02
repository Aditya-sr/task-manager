<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id())
            ->with('files');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByRaw("CASE WHEN status = 'Completed' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'status']),
            'flash' => [
                'message' => session('message')
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Tasks/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,Completed',
            'due_date' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
        ]);

        $validated['user_id'] = auth()->id();
        $task = Task::create($validated);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('task-files', 'public');
                TaskFile::create([
                    'task_id' => $task->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('tasks.index')
            ->with('message', 'Task created successfully!');
    }

    public function edit(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->load('files');

        return Inertia::render('Tasks/Form', [
            'task' => $task
        ]);
    }
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,Completed',
            'due_date' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'nullable|integer|exists:task_files,id',
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        if ($request->has('delete_files')) {
            foreach (array_filter($request->delete_files) as $fileId) {
                $file = TaskFile::where('id', $fileId)
                              ->where('task_id', $task->id)
                              ->first();
                if ($file) {
                    Storage::disk('public')->delete($file->file_path);
                    $file->delete();
                }
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('task-files', 'public');
                    TaskFile::create([
                        'task_id' => $task->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                    ]);
                }
            }
        }

        return redirect()->route('tasks.index')
            ->with('message', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        foreach ($task->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('message', 'Task deleted successfully!');
    }
}
