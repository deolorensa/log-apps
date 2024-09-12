<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : View
    {

        $chirps = Chirp::with('user')->latest()->get();
        $rejects = Chirp::with('user')->where('persetujuan_manajer', 2)->orWhere('persetujuan_direktur', 2)->latest()->get();
        $rejectsDirektur = Chirp::with('user')->Where('persetujuan_manajer', 1)->orWhere('persetujuan_direktur', 2)->latest()->get();
        $pending = Chirp::with('user')->where('persetujuan_manajer', 3)->orWhere('persetujuan_manajer', null)->where('persetujuan_direktur', 0)->latest()->get();
        $manajer = Chirp::with('user')->where('persetujuan_manajer', 1)->Where('persetujuan_direktur', 0)->latest()->get();
        $direktur = Chirp::with('user')->where('persetujuan_manajer', 1)->Where('persetujuan_direktur', 1)->latest()->get();
        $direkturApprove = Chirp::with('user')->where('persetujuan_manajer', null)->Where('persetujuan_direktur', 1)->latest()->get();
        $direkturApproveDirektur = Chirp::with('user')->where('persetujuan_manajer', 0)->Where('persetujuan_direktur', 1)->latest()->get();
        $combined = $direktur->merge($direkturApprove)->merge($direkturApproveDirektur)->unique('id');
        // dd($pending);
        // $id = Auth::user()->id;
        // $chirps = Chirp::where($list->user->id == $id)
        // ->orWhere($list->user->direktur = $id )
        // ->orWhere($list->user->manajer = $id)->first();
        return view('chirps.index', [
            'chirps' => $chirps,
            'rejects' => $rejects,
            'pending' => $pending,
            'manajer' => $manajer,
            'direktur' => $combined,
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
        // dd($request->user()->manajer);
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        if (!$request->user()->manajer == null) {
            $validated['persetujuan_manajer'] = 3;
        }
        if (!$request->user()->direktur == null) {
            $validated['persetujuan_direktur'] = 0;
        }
        if ($request->user()->direktur == null) {
            $validated['persetujuan_manajer'] = 0;
            $validated['persetujuan_direktur'] = 1;
        }

        $request->user()->chirps()->create($validated);

        return redirect(route('chirps.index'));
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
    public function edit(Chirp $chirp): View
    {
        Gate::authorize('update', $chirp);

        return view('chirps.edit', [
            'chirp' => $chirp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {

        // dd($request->all());
        if ($request->message){

        Gate::authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
        $chirp->update($validated);
        }
        if ($request->persetujuan_manajer){
            $chirp->persetujuan_manajer = 1;
            $chirp->update();
        }
        elseif ($request->persetujuan_direktur){
            $chirp->persetujuan_direktur = 1;
            $chirp->update();

        }
        elseif ($request->penolakan_manajer){
            $chirp->persetujuan_manajer = 2;
            $chirp->update();

        }
        elseif ($request->penolakan_direktur){
            $chirp->persetujuan_direktur = 2;
            $chirp->update();
        }
        return redirect(route('chirps.index'));
    }

    public function destroy(Chirp $chirp): RedirectResponse
    {
        Gate::authorize('delete', $chirp);

        $chirp->delete();

        return redirect(route('chirps.index'));
    }
}
