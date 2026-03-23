<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@extends('layouts.app')

@section('title', __('Meu Perfil'))

@section('content')
<div class="container">

    <div class="card" style="max-width: 700px; margin: 0 auto; padding: 30px;">

        <h1 style="display:flex; align-items:center; gap:10px; margin-bottom:25px;">
            👤 {{ __('Meu Perfil') }}
        </h1>

        @if(session('success'))
            <div class="success-msg" style="margin-bottom:20px;">
                ✓ {{ session('success') }}
            </div>
        @endif

        <form action="/profile" method="POST" class="space-y-4">
            @csrf

            <!-- GRID -->
            <div class="grid grid-auto">

                <div>
                    <label>{{ __('Nome') }}</label>
                    <input type="text" name="name" value="{{ $user->name }}" required
                        placeholder="{{ __('Seu nome completo') }}">
                </div>

                <div>
                    <label>{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ $user->email }}" required
                        placeholder="email@exemplo.com">
                </div>

            </div>

            <div>
                <label>LinkedIn</label>
                <input type="url" name="linkedin" value="{{ $user->linkedin }}"
                    placeholder="https://linkedin.com/in/seu-perfil">
            </div>

            <div>
                <label>Bio ({{ __('Resumo Rápido') }})</label>
                <textarea name="bio" rows="4"
                    placeholder="{{ __('Uma breve descrição sobre você...') }}">{{ $user->bio }}</textarea>
            </div>

            <div>
                <label>{{ __('Habilidades') }}</label>
                <input type="text" name="skills_text"
                    value="{{ is_array($user->skills) ? implode(', ', $user->skills) : '' }}"
                    placeholder="PHP, Laravel, Docker...">
            </div>

            <div>
                <label>{{ __('Experiência Profissional') }}</label>
                <textarea name="experience" rows="6"
                    placeholder="{{ __('Descreva suas experiências...') }}">{{ $user->experience }}</textarea>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">
                    💾 {{ __('Salvar Perfil') }}
                </button>
            </div>

        </form>

        <div style="margin-top:30px; border-top:1px solid #eee; padding-top:20px;">
            <a href="/dashboard">
                ← {{ __('Voltar ao Dashboard') }}
            </a>
        </div>

    </div>

</div>
@endsection