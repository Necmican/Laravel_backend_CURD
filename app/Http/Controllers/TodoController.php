<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\ActivityLog;

class TodoController extends Controller
{
    
    public function index()
    {
        
        return Todo::where('user_id', Auth::id())->get();
    }

   
    
       public function store(Request $request)
{
    
    $todo = $request->user()->todos()->create([
        'title' => $request->title,    
        'completed' => false           
    ]);

    ActivityLog::create([
        'user_id' => $request->user()->id,      
        'action' => 'TODO_CREATED',    
        'description' => "'{$todo->title}' eklendi.", 
        
    ]);

    return response()->json($todo, 201);
}
    

    
    public function update(Request $request, Todo $todo)
    {
        
        if ($todo->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bu işlem için yetkiniz yok.'], 403);
        }

    $oldTitle = $todo->title; 

    
    $todo->update($request->all());

    
    ActivityLog::create([
        'user_id' => $request->user()->id,
        'action' => 'TODO_UPDATED',
        'description' => "Güncellendi. Eski: {$oldTitle} -> Yeni: {$todo->title}",
    ]);

    return response()->json($todo);
    }

    
    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bu işlem için yetkiniz yok.'], 403);
        }

    $title = $todo->title;

    $todo->delete();

  
    ActivityLog::create([
        'user_id' => request()->user()->id,
        'action' => 'TODO_DELETED',
    
        'description' => "'{$title}' silindi.",
    ]);

    
    return response()->json(['message' => 'Silindi']);
    }
}