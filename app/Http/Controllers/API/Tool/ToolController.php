<?php

namespace App\Http\Controllers\API\Tool;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Tool\IndexRequest;
use App\Http\Requests\API\Tool\StoreRequest;
use App\Http\Requests\API\Tool\UpdateRequest;
use App\Http\Resources\API\Tool\ToolResource;
use App\Models\Tool;

class ToolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(Tool::class, 'tool');
    }

    public function index(IndexRequest $request)
    {
        $tools = auth()->user()->tools()
            ->when($request->has('tag'), function ($query) use ($request) {
                $query->whereJsonContains('tags', $request->tag);
            })
            ->paginate();

        return ToolResource::collection($tools);
    }

    public function store(StoreRequest $request)
    {
        $tool = auth()->user()->tools()->create($request->validated());

        return new ToolResource($tool);
    }

    public function show(Tool $tool)
    {
        return new ToolResource($tool->load('user'));
    }

    public function update(UpdateRequest $request, Tool $tool)
    {
        $tool->update($request->validated());

        return new ToolResource($tool->load('user'));
    }

    public function destroy(Tool $tool)
    {
        $tool->delete();

        return response()->noContent();
    }

    public function destroyAll()
    {
        auth()->user()->tools()->delete();

        return response()->noContent();
    }
}
