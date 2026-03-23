<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class JobController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Inicializa o tradutor configurado para Português ('pt')
            $tr = new GoogleTranslate('pt');

            // Traduz o título e a descrição (ignorando erros se o texto for vazio)
            $tituloTraduzido = $request->title ? $tr->translate($request->title) : 'Sem título';
            $descricaoTraduzida = $request->description ? $tr->translate($request->description) : '';
            
            // Opcional: Se quiser traduzir o cargo/tipo também
            // $tipoTraduzido = $request->job_type ? $tr->translate($request->job_type) : null;

            // Salva a vaga no banco já em Português
            $job = Job::create([
                'title' => $tituloTraduzido,
                'company' => $request->company,
                'location' => $request->location,
                'description' => $descricaoTraduzida,
                'job_type' => $request->job_type, // Pode colocar $tipoTraduzido se quiser
                'salary' => $request->salary,
                'source' => $request->source,
                'link' => $request->link,
            ]);

            return response()->json(['success' => true, 'job' => $job]);

        } catch (\Exception $e) {
            \Log::error('Erro ao traduzir/salvar vaga: ' . $e->getMessage());
            return response()->json(['error' => 'Falha ao salvar vaga'], 500);
        }
    }

    public function analyzeWithAI($id)
    {
        try {
        $job = Job::findOrFail($id);
        
        // Aqui você pega o currículo real do usuário (ajuste conforme seu banco)
        $userProfile = auth()->user()->resume ?? "Sou desenvolvedor web buscando oportunidades.";
        
        $scriptPath = base_path('bot/ai_resume_bot.py');

        // No Laragon/Windows, às vezes o executável se chama apenas 'python' ou 'python3'
        $process = new Process(['python', $scriptPath, $job->description, $userProfile]);
        $process->setTimeout(120); // Damos 2 minutos, a OpenAI às vezes demora
        $process->run();

        // SE FALHAR, AGORA DA PRA VER O MOTIVO NA TELA!
        if (!$process->isSuccessful()) {
            $erroReal = $process->getErrorOutput();
            return response()->json([
                'error' => "O Python travou! Motivo: " . $erroReal
            ], 500);
        }

        $output = $process->getOutput();
        $result = json_decode($output, true);

        if (!$result || isset($result['error'])) {
            return response()->json([
                'error' => $result['error'] ?? "A IA não retornou um JSON válido. Retorno bruto: $output"
            ], 500);
        }

        return response()->json($result);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erro interno do Laravel: ' . $e->getMessage()
        ], 500);
    }
    }
}