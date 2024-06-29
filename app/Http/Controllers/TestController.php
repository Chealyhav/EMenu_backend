<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sampleData = [
            ['id' => 1, 'name' => 'Test 1', 'description' => 'This is the first test'],
            ['id' => 2, 'name' => 'Test 2', 'description' => 'This is the second test'],
            ['id' => 3, 'name' => 'Test 3', 'description' => 'This is the third test'],
        ];

        return response()->json($sampleData, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $newTest = [
            'id' => rand(4, 1000), // Random ID for demonstration
            'name' => $request->name,
            'description' => $request->description,
        ];

        return response()->json($newTest, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sampleData = [
            ['id' => 1, 'name' => 'Test 1', 'description' => 'This is the first test'],
            ['id' => 2, 'name' => 'Test 2', 'description' => 'This is the second test'],
            ['id' => 3, 'name' => 'Test 3', 'description' => 'This is the third test'],
        ];

        $test = collect($sampleData)->where('id', $id)->first();

        if (!$test) {
            return response()->json(['message' => 'Test not found'], 404);
        }

        return response()->json($test, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $updatedTest = [
            'id' => $id,
            'name' => $request->name,
            'description' => $request->description,
        ];

        return response()->json($updatedTest, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // In a real application, you would delete the resource from the database
        // For demonstration, we will return a message with the deleted resource ID
        return response()->json(['message' => 'Test '.$id.' deleted'], 200);
    }
}
