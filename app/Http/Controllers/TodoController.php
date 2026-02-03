<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    // 1. LİSTELEME (GET /api/todos)
    public function index()
    {
        $todos = Todo::all();

        return response()->json([
            'status' => true,
            'message' => 'Todo listesi',
            'data' => $todos
        ], 200);
    }

    // 2. EKLEME (POST /api/todos)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        $todo = Todo::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Todo oluşturuldu',
            'data' => $todo
        ], 201);
    }

    // 3. TEK KAYIT GÖSTERME (GET /api/todos/{id})
    public function show($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json([
                'status' => false,
                'message' => 'Kayıt bulunamadı'
            ], 404); // Maildeki 404 isteği
        }

        return response()->json([
            'status' => true,
            'message' => 'Kayıt bulundu',
            'data' => $todo
        ], 200);
    }

    // 4. GÜNCELLEME (PUT /api/todos/{id})
    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['status' => false, 'message' => 'Kayıt bulunamadı'], 404);
        }

        // Validation (Güncellemede title zorunlu olmayabilir ama biz yine de kontrol edelim)
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean' // Tamamlandı mı? (true/false)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        $todo->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Todo güncellendi',
            'data' => $todo
        ], 200);
    }

    // 5. SİLME (DELETE /api/todos/{id})
    public function destroy($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['status' => false, 'message' => 'Kayıt bulunamadı'], 404);
        }

        $todo->delete();

        return response()->json([
            'status' => true,
            'message' => 'Todo silindi'
        ], 200);
    }
}