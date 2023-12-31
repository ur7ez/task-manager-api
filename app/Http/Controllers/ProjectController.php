<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    public function index(Request $request): ProjectCollection
    {
        $projects = QueryBuilder::for(Project::class)
            ->allowedIncludes('tasks')
            ->paginate();
        return new ProjectCollection($projects);
    }

    public function show(Request $request, Project $project)
    {
        return (new ProjectResource($project))
            ->load('tasks')
            ->load('members');
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        $validated = $request->validated();
        $project = Auth::user()->projects()->create($validated);

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $validated = $request->validated();
        $project->update($validated);

        return new ProjectResource($project);
    }

    public function destroy(Request $request, Project $project): Response
    {
        $project->delete();
        return response()->noContent();
    }
}
