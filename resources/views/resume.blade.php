<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@extends('layouts.app')

@section('title', __('Meu Currículo'))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">

    <!-- HEADER -->
    <div class="card mb-6 flex justify-between items-center">
        <h1 class="flex items-center gap-3">
            <span>📄</span>
            {{ __('Meu Currículo') }}
        </h1>

        <div class="flex gap-3">
            <a href="/dashboard" class="btn btn-secondary">
                ← {{ __('Voltar') }}
            </a>

            <button onclick="window.print()" class="btn btn-primary">
                🖨 {{ __('Imprimir') }}
            </button>
        </div>
    </div>

    <!-- CURRÍCULO -->
    <div class="card">
        <div class="a4-paper">

            <!-- COLUNA PRINCIPAL -->
            <div class="main-col">

                <header style="margin-bottom: 30px;">
                    <h1>{{ $user->name }}</h1>
                    <div class="contact-info">
                        {{ $user->email }}
                        @if($user->linkedin) • LinkedIn @endif
                        • Brasil
                    </div>
                </header>

                @if($user->bio)
                <div class="section">
                    <h2>{{ __('Sobre Mim') }}</h2>
                    <div class="content-text">{{ $user->bio }}</div>
                </div>
                @endif

                @if($user->experience)
                <div class="section">
                    <h2>{{ __('Experiência Profissional') }}</h2>
                    <div class="content-text">{{ $user->experience }}</div>
                </div>
                @endif

                @if($user->resume_text)
                <div class="section">
                    <h2>{{ __('Resumo Estratégico') }}</h2>
                    <div class="content-text" style="
                        background: #f3f4f6;
                        padding: 15px;
                        border-radius: 8px;
                        border-left: 4px solid #007bff;
                    ">
                        {{ $user->resume_text }}
                    </div>
                </div>
                @endif

            </div>

            <!-- SIDEBAR -->
            <div class="sidebar-col">

                @php
                    $skills = is_array($user->skills)
                        ? $user->skills
                        : explode(',', $user->skills ?? '');

                    $skills = array_filter(array_map('trim', $skills));
                @endphp

                @if(count($skills))
                <div class="section">
                    <h2>{{ __('Habilidades') }}</h2>
                    <div class="skills-grid">
                        @foreach($skills as $skill)
                            <span class="skill-tag">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($user->education ?? false)
                <div class="section">
                    <h2>{{ __('Educação') }}</h2>
                    <div class="content-text">{{ $user->education }}</div>
                </div>
                @endif

                @if($user->languages ?? false)
                <div class="section">
                    <h2>{{ __('Idiomas') }}</h2>
                    <div class="content-text">{{ $user->languages }}</div>
                </div>
                @endif

            </div>

            <div style="clear: both;"></div>

        </div>
    </div>

</div>
@endsection