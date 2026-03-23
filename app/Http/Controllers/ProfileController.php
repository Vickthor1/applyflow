<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\LocationService;

class ProfileController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }
    public function show()
    {
        // Usar usuário autenticado
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        $user->fill($request->only(['name', 'email', 'linkedin', 'bio', 'experience']));
        
        // Processar skills
        $skillsText = $request->input('skills_text', '');
        $user->skills = array_map('trim', explode(',', $skillsText));
        
        $user->save();

        return redirect('/profile')->with('success', 'Perfil atualizado!');
    }

    public function updateLocation(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        $request->validate([
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'preferred_language' => 'nullable|in:pt,en,es'
        ]);

        // Atualizar localização
        $user->city = $request->city;
        $user->country = $request->country;
        $user->preferred_language = $request->preferred_language ?: 'pt';

        // Se foi fornecida cidade e país, tentar geocodificar
        if ($request->city && $request->country) {
            $locationString = $request->city . ', ' . $request->country;
            $coordinates = $this->locationService->geocodeLocation($locationString);

            if ($coordinates) {
                $user->latitude = $coordinates['latitude'];
                $user->longitude = $coordinates['longitude'];
            }
        }

        $user->save();

        if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Localização atualizada com sucesso!'
            ], 200);
        }

        return redirect('/dashboard')->with('success', 'Localização atualizada com sucesso!');
    }
}
