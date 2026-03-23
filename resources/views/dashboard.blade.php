<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@extends('layouts.app')

@section('title', 'ApplyFlow - ' . __('Dashboard'))
@section('content')
<div class="container">
    <section style="margin: 30px 0;">
        <h2 style="font-size: 24px; color: #333; margin-bottom: 20px;">📊 {{ __('Analytics') }}</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                <div style="font-size: 12px; color: #666; margin-bottom: 10px;">{{ __('Total de Vagas') }}</div>
                <div style="font-size: 32px; font-weight: bold; color: #0ea5e9;">{{ $stats['total_jobs'] }}</div>
            </div>
            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #22c55e;">
                <div style="font-size: 12px; color: #666; margin-bottom: 10px;">{{ __('Score Médio') }}</div>
                <div style="font-size: 32px; font-weight: bold; color: #22c55e;">{{ number_format($stats['avg_match_score'], 1) }}</div>
            </div>
            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <div style="font-size: 12px; color: #666; margin-bottom: 10px;">{{ __('Taxa de Aplicação') }}</div>
                <div style="font-size: 32px; font-weight: bold; color: #f59e0b;">{{ count($jobs) }}</div>
            </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0;">
                    <h4 style="font-size: 16px; color: #333; margin-top: 0; margin-bottom: 15px;">{{ __('Vagas por Fonte') }}</h4>
                    <canvas id="sourceChart" width="300" height="200"></canvas>
                </div>
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0;">
                    <h4 style="font-size: 16px; color: #333; margin-top: 0; margin-bottom: 15px;">{{ __('Vagas por Tipo') }}</h4>
                    <canvas id="typeChart" width="300" height="200"></canvas>
                </div>
            </div>
        </section>

        <section style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; margin: 30px 0;">
            <h2 style="font-size: 24px; color: #333; margin-top: 0; margin-bottom: 20px;">🔍 {{ __('Buscar Vagas por Tags') }}</h2>
            <form action="/search-jobs" method="POST" id="searchForm">
                @csrf
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <input type="text" name="keywords" placeholder="{{ __('Ex: PHP, Laravel, Python') }}" value="PHP, Laravel, Python" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Buscar') }}</button>
                </div>
            </form>
            <div id="searchResult" style="margin-top: 15px;"></div>
        </section>

        <section style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; margin: 30px 0;">
            <h2 style="font-size: 24px; color: #333; margin-top: 0; margin-bottom: 20px;">📍 {{ __('Sua Localização') }}</h2>

            @if($stats['user_location']['has_coordinates'])
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #166534; font-weight: 500;">
                        ✅ {{ __('Localização configurada:') }}
                        @if($stats['user_location']['city'] && $stats['user_location']['country'])
                            {{ $stats['user_location']['city'] }}, {{ $stats['user_location']['country'] }}
                        @else
                            {{ __('Coordenadas definidas') }}
                        @endif
                    </p>
                </div>
            @else
                <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #92400e;">
                        ⚠️ {{ __('Configure sua localização para ver vagas próximas a você.') }}
                    </p>
                </div>
            @endif

            <form action="/profile/update-location" method="POST" id="locationForm">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">{{ __('Cidade') }}</label>
                        <input type="text" name="city" value="{{ auth()->user()->city }}" placeholder="{{ __('Ex: São Paulo') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">{{ __('País') }}</label>
                        <input type="text" name="country" value="{{ auth()->user()->country }}" placeholder="{{ __('Ex: Brazil') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">{{ __('Idioma Preferido') }}</label>
                        <select name="preferred_language" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="pt" {{ auth()->user()->preferred_language == 'pt' ? 'selected' : '' }}>🇧🇷 Português</option>
                            <option value="en" {{ auth()->user()->preferred_language == 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                            <option value="es" {{ auth()->user()->preferred_language == 'es' ? 'selected' : '' }}>🇪🇸 Español</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn" style="background: #059669; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 500;">
                        📍 {{ __('Atualizar Localização') }}
                    </button>
                    <button type="button" onclick="getCurrentLocation()" class="btn" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 500;">
                        🎯 {{ __('Usar Minha Localização Atual') }}
                    </button>
                </div>
            </form>
        </section>

        <section style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; margin: 30px 0;">
            <h2 style="font-size: 24px; color: #333; margin-top: 0; margin-bottom: 20px;">🎯 {{ __('Filtros') }}</h2>
            <form action="/dashboard" method="GET" id="filterForm">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                    <input type="text" name="location" placeholder="{{ __('Localização') }}" value="{{ request('location') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    <select name="job_type" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">{{ __('Tipo de Emprego') }}</option>
                        <option value="full-time" {{ request('job_type') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part-time" {{ request('job_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="contract" {{ request('job_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="freelance" {{ request('job_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                    <input type="number" name="min_salary" placeholder="{{ __('Salário Mínimo') }}" value="{{ request('min_salary') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    <select name="max_distance" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" {{ !$stats['user_location']['has_coordinates'] ? 'disabled' : '' }}>
                        <option value="">{{ __('Distância Máx (km)') }}</option>
                        <option value="50" {{ request('max_distance') == '50' ? 'selected' : '' }}>50 km</option>
                        <option value="100" {{ request('max_distance') == '100' ? 'selected' : '' }}>100 km</option>
                        <option value="250" {{ request('max_distance') == '250' ? 'selected' : '' }}>250 km</option>
                        <option value="500" {{ request('max_distance') == '500' ? 'selected' : '' }}>500 km</option>
                        <option value="1000" {{ request('max_distance') == '1000' ? 'selected' : '' }}>1000 km</option>
                    </select>
                    <select name="source" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">{{ __('Fonte') }}</option>
                        <option value="Remotive" {{ request('source') == 'Remotive' ? 'selected' : '' }}>Remotive</option>
                        <option value="The Muse" {{ request('source') == 'The Muse' ? 'selected' : '' }}>The Muse</option>
                        <option value="Adzuna" {{ request('source') == 'Adzuna' ? 'selected' : '' }}>Adzuna</option>
                        <option value="JSearch" {{ request('source') == 'JSearch' ? 'selected' : '' }}>JSearch</option>
                        <option value="Indeed" {{ request('source') == 'Indeed' ? 'selected' : '' }}>Indeed</option>
                        <option value="Glassdoor" {{ request('source') == 'Glassdoor' ? 'selected' : '' }}>Glassdoor</option>
                        <option value="LinkedIn" {{ request('source') == 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn btn-primary">{{ __('Filtrar') }}</button>
                    <a href="/dashboard" class="btn btn-secondary">{{ __('Limpar') }}</a>
                </div>
            </form>
        </section>

        <section style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; margin: 30px 0;">
            <h2 style="font-size: 24px; color: #333; margin-top: 0; margin-bottom: 20px;">✨ {{ __('Perfil Base para a IA') }}</h2>
            
            <div style="background: #faf5ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #8b5cf6;">
                <p style="margin: 0; color: #5b21b6; font-size: 14px;">
                    ✓ <strong>{{ __('A IA usará este texto para cruzar com as vagas abaixo') }}</strong><br>
                    ℹ️ {{ __('Cole aqui o seu resumo profissional, experiências e habilidades atuais para que a IA possa adaptá-lo a cada oportunidade.') }}
                </p>
            </div>
            
            <form action="/profile/update-resume" method="POST" id="baseResumeForm">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <label for="user_resume" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">{{ __('Seu Currículo Atual (Texto Bruto)') }}</label>
                        <textarea id="user_resume" name="user_resume" rows="6" placeholder="{{ __('Ex: Sou desenvolvedor web com 3 anos de experiência em PHP, Laravel e Vue.js. Tenho foco em...') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: inherit; resize: vertical;"></textarea>
                    </div>
                    
                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="submit" class="btn" style="background: #8b5cf6; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 500; transition: 0.3s;">
                            💾 {{ __('Salvar Perfil Base') }}
                        </button>
                    </div>
                </div>
                <p style="font-size: 12px; color: #666; margin-top: 15px;">
                    💡 <strong>{{ __('Dica:') }}</strong> {{ __('Mantenha este texto sempre atualizado com suas últimas tecnologias. Sempre que você clicar em "✨ Analisar com IA" em uma vaga, nosso bot vai ler esse texto!') }}
                </p>
            </form>
        </section>

        <section style="margin: 30px 0;">
            <h2 class="section-title" style="
                display: flex;
                align-items: center;
                gap: 10px;
                background: #f1f5f9;
                padding: 10px 16px;
                border-radius: 8px;
                width: fit-content;
            ">
                <span style="
                    background: #0ea5e9;
                    color: white;
                    padding: 6px 14px;
                    border-radius: 999px;
                    font-size: 16px;
                    font-weight: bold;
                ">
                    {{ count($jobs) }}
                </span>

                <span style="
                    color: #1e293b;
                    font-weight: 500;
                ">
                    {{ __('Vagas Encontradas') }}
                </span>
            </h2>

            @forelse($jobs as $job)
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <div>
                        <h3 style="font-size: 18px; color: #333; margin: 0 0 5px 0;">{{ $job->title }}</h3>
                        <p style="font-size: 14px; color: #666; margin: 0;">{{ $job->company }}</p>
                    </div>
                    <span style="background: #f0f0f0; padding: 6px 12px; border-radius: 4px; font-size: 12px; color: #666;">{{ $job->source }}</span>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; font-size: 14px; color: #666;">
                    <span>📍 {{ $job->location }}</span>
                    @if($job->distance_km)
                        <span style="color: #3b82f6; font-weight: bold;">📏 {{ number_format($job->distance_km, 0) }} km</span>
                    @endif
                    @if($job->salary)
                        <span style="color: #22c55e; font-weight: bold;">💰 {{ $job->salary }}</span>
                    @endif
                    <span>⏰ {{ ucfirst($job->job_type) }}</span>
                    <span style="color: #f59e0b; font-weight: bold;">🎯 {{ __('Match:') }} {{ $job->match_score }}</span>
                    <span style="background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 12px; font-size: 12px;">{{ strtoupper($job->language) }}</span>
                </div>

                <p style="color: #555; line-height: 1.6; margin: 0 0 15px 0;">{{ Str::limit(strip_tags($job->description), 200) }}</p>

                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                    <a href="{{ $job->link }}" target="_blank" class="btn btn-success" style="background: #22c55e; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; font-size: 14px;">{{ __('Ver Vaga') }}</a>
                    <a href="/resume/{{ $job->id }}" target="_blank" class="btn btn-primary" style="background: #0ea5e9; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; font-size: 14px;">{{ __('Gerar CV') }}</a>
                    <a href="/resume/{{ $job->id }}/download" class="btn btn-warning" style="background: #f59e0b; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; font-size: 14px; color: black;">{{ __('Baixar CV') }}</a>
                    
                    <button type="button" onclick="analisarVaga({{ $job->id }})" id="btn-ai-{{ $job->id }}" style="background: #8b5cf6; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 14px;">
                        ✨ {{ __('Analisar com IA') }}
                    </button>
                </div>

                <div id="ai-result-{{ $job->id }}" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-top: 15px;">
                    <h4 style="color: #4f46e5; margin-top: 0; font-size: 16px;">🤖 {{ __('Dicas da IA para o Currículo') }}</h4>
                    <p style="margin: 10px 0; font-size: 14px;"><strong>{{ __('Score de Match:') }}</strong> <span id="ai-score-{{ $job->id }}" style="font-size: 18px; font-weight: bold;"></span>%</p>
                    
                    <strong style="font-size: 14px; color: #333;">📌 {{ __('Palavras-chave para adicionar:') }}</strong>
                    <ul id="ai-keywords-{{ $job->id }}" style="margin-top: 5px; margin-bottom: 15px; color: #475569; padding-left: 20px; font-size: 14px;"></ul>
                    
                    <strong style="font-size: 14px; color: #333;">💡 {{ __('Dicas de melhoria:') }}</strong>
                    <ul id="ai-tips-{{ $job->id }}" style="margin-top: 5px; margin-bottom: 15px; color: #475569; padding-left: 20px; font-size: 14px;"></ul>

                    <strong style="font-size: 14px; color: #333;">📝 {{ __('Resumo Modificado para o CV:') }}</strong>
                    <p id="ai-resumo-{{ $job->id }}" style="margin-top: 5px; color: #1e293b; background: #fff; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 4px; font-size: 14px;"></p>
                </div>
            </div>
            @empty
            <div style="background: #f5f5f5; padding: 40px 20px; border-radius: 8px; text-align: center;">
                <p style="color: #666; font-size: 16px; margin: 0;">{{ __('Nenhuma vaga encontrada. Tente ajustar os filtros.') }}</p>
            </div>
            @endforelse
        </section>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Função para Analisar a vaga com IA
    function analisarVaga(jobId) {
        const btn = document.getElementById(`btn-ai-${jobId}`);
        const resultDiv = document.getElementById(`ai-result-${jobId}`);
        const originalText = btn.innerHTML;
        
        btn.innerHTML = "⏳ {{ __('Pensando...') }}";
        btn.disabled = true;

        fetch(`/api/analyze-job/${jobId}`)
            .then(response => response.json())
            .then(data => {
                if(data.error) throw new Error(data.error);
                
                const scoreSpan = document.getElementById(`ai-score-${jobId}`);
                scoreSpan.innerText = data.match_score;
                scoreSpan.style.color = data.match_score > 70 ? '#22c55e' : (data.match_score > 40 ? '#f59e0b' : '#ef4444');
                
                const kwList = document.getElementById(`ai-keywords-${jobId}`);
                kwList.innerHTML = '';
                if(data.keywords && data.keywords.length > 0) {
                    data.keywords.forEach(kw => {
                        kwList.innerHTML += `<li>${kw}</li>`;
                    });
                }
                
                const tipsList = document.getElementById(`ai-tips-${jobId}`);
                tipsList.innerHTML = '';
                if(data.tips && data.tips.length > 0) {
                    data.tips.forEach(tip => {
                        tipsList.innerHTML += `<li>${tip}</li>`;
                    });
                }

                // Preenche o resumo modificado
                if(data.novo_resumo) {
                    document.getElementById(`ai-resumo-${jobId}`).innerText = data.novo_resumo;
                }

                resultDiv.style.display = 'block';
                btn.innerHTML = "✨ {{ __('Análise Concluída') }}";
            })
            .catch(error => {
                alert("{{ __('Erro ao analisar vaga:') }} " + error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    }

    // Search Form Handler
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const button = this.querySelector('button');
        const originalText = button.textContent;
        button.textContent = "{{ __('Buscando...') }}";
        button.disabled = true;

        fetch('/search-jobs', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('searchResult').innerHTML = `<div style="background: #f0fdf4; color: #166534; padding: 12px; border-radius: 4px;"><p style="margin: 0;">✓ {{ __('Busca realizada com sucesso! Recarregando...') }}</p></div>`;
            setTimeout(() => location.reload(), 2000);
        })
        .catch(error => {
            document.getElementById('searchResult').innerHTML = `<div style="background: #fef2f2; color: #991b1b; padding: 12px; border-radius: 4px;"><p style="margin: 0;">✗ {{ __('Erro:') }} ${error.message}</p></div>`;
            button.textContent = originalText;
            button.disabled = false;
        });
    });

    // Analytics Charts
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const sourceData = @json($stats['jobs_by_source']);
            const sourceLabels = sourceData.map(item => item.source || 'Unknown');
            const sourceValues = sourceData.map(item => item.count);

            new Chart(document.getElementById('sourceChart'), {
                type: 'doughnut',
                data: {
                    labels: sourceLabels,
                    datasets: [{
                        data: sourceValues,
                        backgroundColor: [
                            '#0ea5e9', '#22c55e', '#eab308', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'
                        ],
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 12 }, padding: 15 } }
                    }
                }
            });

            const typeData = @json($stats['jobs_by_type']);
            const typeLabels = typeData.map(item => item.job_type || 'Unknown');
            const typeValues = typeData.map(item => item.count);

            new Chart(document.getElementById('typeChart'), {
                type: 'bar',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        label: "{{ __('Quantidade') }}",
                        data: typeValues,
                        backgroundColor: '#0ea5e9',
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        } catch (error) {
            console.warn('Charts not available:', error);
        }
    });

    // Location Form Handler
    document.getElementById('locationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.textContent;
        button.textContent = "{{ __('Atualizando...') }}";
        button.disabled = true;

        fetch('/profile/update-location', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(text || 'Erro inesperado');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                throw new Error(data.message || "{{ __('Erro ao atualizar localização') }}");
            }
        })
        .catch(error => {
            alert("{{ __('Erro:') }} " + error.message);
            button.textContent = originalText;
            button.disabled = false;
        });
    });

    // Get Current Location
    function getCurrentLocation() {
        if (!navigator.geolocation) {
            alert("{{ __('Geolocalização não é suportada pelo seu navegador') }}");
            return;
        }

        const button = document.querySelector('button[onclick="getCurrentLocation()"]');
        const originalText = button.textContent;
        button.textContent = "{{ __('Obtendo localização...') }}";
        button.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Reverse geocoding usando Nominatim
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=10&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        const city = data.address?.city || data.address?.town || data.address?.village || '';
                        const country = data.address?.country || '';

                        // Preencher os campos do formulário
                        document.querySelector('input[name="city"]').value = city;
                        document.querySelector('input[name="country"]').value = country;

                        // Detectar idioma baseado no país
                        let language = 'en'; // default
                        if (country === 'Brazil' || country === 'Brasil') {
                            language = 'pt';
                        } else if (['Spain', 'Mexico', 'Argentina', 'Colombia', 'Chile', 'Peru'].includes(country)) {
                            language = 'es';
                        }

                        document.querySelector('select[name="preferred_language"]').value = language;

                        button.textContent = originalText;
                        button.disabled = false;

                        alert("{{ __('Localização obtida com sucesso! Clique em \"Atualizar Localização\" para salvar.') }}");
                    })
                    .catch(error => {
                        console.error('Reverse geocoding error:', error);
                        // Fallback: apenas definir coordenadas
                        document.querySelector('input[name="city"]').value = "{{ __('Localização Atual') }}";
                        document.querySelector('input[name="country"]').value = '';
                        button.textContent = originalText;
                        button.disabled = false;
                        alert("{{ __('Localização obtida, mas não foi possível identificar cidade/país. Você pode preenchê-los manualmente.') }}");
                    });
            },
            function(error) {
                let errorMessage = "{{ __('Erro ao obter localização:') }} ";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += "{{ __('Usuário negou a solicitação de geolocalização.') }}";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += "{{ __('Informações de localização indisponíveis.') }}";
                        break;
                    case error.TIMEOUT:
                        errorMessage += "{{ __('A solicitação de localização expirou.') }}";
                        break;
                    default:
                        errorMessage += "{{ __('Erro desconhecido.') }}";
                        break;
                }
                alert(errorMessage);
                button.textContent = originalText;
                button.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutos
            }
        );
    }
</script>
@endsection