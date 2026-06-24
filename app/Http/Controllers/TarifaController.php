<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTarifaRequest;
use App\Http\Requests\UpdateTarifaRequest;
use App\Models\Cancha;
use App\Models\Tarifa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TarifaController extends Controller
{
    public function index(): View
    {
        $tarifas = Tarifa::with('cancha')->latest()->paginate(10);

        return view('tarifas.index', compact('tarifas'));
    }

    public function create(): View
    {
        $canchas = Cancha::where('estado', 'Disponible')->get();

        return view('tarifas.create', compact('canchas'));
    }

    public function store(StoreTarifaRequest $request): RedirectResponse
    {
        Tarifa::create($request->validated());

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa creada correctamente.');
    }

    public function edit(Tarifa $tarifa): View
    {
        $canchas = Cancha::where('estado', 'Disponible')->get();

        return view('tarifas.edit', compact('tarifa', 'canchas'));
    }

    public function update(UpdateTarifaRequest $request, Tarifa $tarifa): RedirectResponse
    {
        $tarifa->update($request->validated());

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa actualizada correctamente.');
    }

    public function destroy(Tarifa $tarifa): RedirectResponse
    {
        $tarifa->delete();

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa eliminada correctamente.');
    }
}
