<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Tool\StoreRequest;
use App\Http\Requests\API\Tool\UpdateRequest;
use App\Http\Resources\API\Tool\ToolResource;
use App\Models\Tool;

class ToolController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tool::class, 'tool');
    }

    public function index()
    {
        $tools = auth()->user()->tools()->paginate();

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
}
