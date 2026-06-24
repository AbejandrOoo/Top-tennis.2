<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCanchaRequest;
use App\Http\Requests\UpdateCanchaRequest;
use App\Models\Cancha;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CanchaController extends Controller
{
    public function index(): View
    {
        $canchas = Cancha::latest()->paginate(10);

        return view('canchas.index', compact('canchas'));
    }

    public function create(): View
    {
        return view('canchas.create');
    }

    public function store(StoreCanchaRequest $request): RedirectResponse
    {
        Cancha::create($request->validated());

        return redirect()->route('canchas.index')
            ->with('success', 'Cancha creada correctamente.');
    }

    public function edit(Cancha $cancha): View
    {
        return view('canchas.edit', compact('cancha'));
    }

    public function update(UpdateCanchaRequest $request, Cancha $cancha): RedirectResponse
    {
        $cancha->update($request->validated());

        return redirect()->route('canchas.index')
            ->with('success', 'Cancha actualizada correctamente.');
    }

    public function destroy(Cancha $cancha): RedirectResponse
    {
        $cancha->delete();

        return redirect()->route('canchas.index')
            ->with('success', 'Cancha eliminada correctamente.');
    }
}
