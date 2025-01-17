<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\OpenAIService; 

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():Response
    {
        return Inertia::render('Chirps/Index', [
            //
            'chirps' => Chirp::with('user:id,name')->latest()->get(),
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
 
        $request->user()->chirps()->create($validated);
 
        return redirect(route('chirps.index'));
    }
    public function generate(OpenAIService $openAIService)
    {
        try {
            $chirps = $openAIService->generateChirps(); // Använder OpenAIService för att generera chirps
            return response()->json(['chirps' => $chirps]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        Gate::authorize('update', $chirp);
 
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
 
        $chirp->update($validated);
 
        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        Gate::authorize('delete', $chirp);
 
        $chirp->delete();
 
        return redirect(route('chirps.index'));
    }


    //API
    public function apiIndex()
    {
        return response()->json(Chirp::with('user:id,name')->latest()->get());
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
    ]);
    

    $chirp = $request->user()->chirps()->create($validated);

        return response()->json($chirp, 201);
    }

    public function apiShow(Chirp $chirp)
    {
        return response()->json($chirp);
    }

    // public function apiUpdate(Request $request, Chirp $chirp)
    // {
    //     Gate::authorize('update', $chirp);

    //     $validated = $request->validate([
    //     'message' => 'required|string|max:255',
    // ]);

    // $chirp->update($validated);

    //     return response()->json($chirp);
    // }

    public function apiUpdate(Request $request, Chirp $chirp)
{
    try {
        // Authorize the update action
        Gate::authorize('update', $chirp);

        // Validate the request
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Update the chirp
        $chirp->update($validated);

        return response()->json($chirp);

    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return response()->json(['message' => 'You are not authorized to update this chirp.'], 403);
    }
}


public function apiDestroy(Chirp $chirp)
{
    try {
        // Authorize the delete action
        Gate::authorize('delete', $chirp);

        // Proceed with deletion
        $chirp->delete();

        return response()->json(['message' => 'Chirp deleted successfully']);

    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return response()->json(['message' => 'You are not authorized to delete this chirp.'], 403);
    }
}

    

}
