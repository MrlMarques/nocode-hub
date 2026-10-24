<?php
header('Content-Type: application/json');
require_once '../config.php';

$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, TRUE);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Nenhum dado recebido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO projects 
        (aluno_nome, aluno_email, aluno_nivel, projeto_nome, projeto_descricao, projeto_objetivo, ferramentas_usadas, integracoes, em_producao, horas_economizadas_semana, area_ajuda, link_referencia) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['aluno_nome'],
        $data['aluno_email'],
        $data['aluno_nivel'],
        $data['projeto_nome'],
        $data['projeto_descricao'],
        $data['projeto_objetivo'],
        $data['ferramentas_usadas'],
        $data['integracoes'],
        $data['em_producao'],
        $data['horas_economizadas_semana'],
        $data['area_ajuda'],
        $data['link_referencia'] ?? null
    ]);

    $projectId = $pdo->lastInsertId();

    $prompt = "Você é um mentor sênior de automações no-code. Receba os dados de um projeto feito por um aluno e devolva um JSON puro, sem formatação markdown ou texto extra, com estas chaves exatas: nivel_projeto (iniciante, intermediário ou avançado), area_principal (marketing, vendas, operacional, financeiro, suporte ou geral), stack_sugerida (texto simples com lista de ferramentas para evoluir), tags_ai (texto simples separando tags por vírgula), impacto_estimado (baixo, médio ou alto), dificuldade_manutencao (baixa, média ou alta), resumo_curto (resumo em 2 frases focando na transformação), proxima_aula_recomendada (texto sugerindo tema de aula).
    Dados do projeto:
    - Nome do aluno: {$data['aluno_nome']}
    - Nível do aluno: {$data['aluno_nivel']}
    - Nome do Projeto: {$data['projeto_nome']}
    - Descrição: {$data['projeto_descricao']}
    - Objetivo: {$data['projeto_objetivo']}
    - Ferramentas: {$data['ferramentas_usadas']}
    - Integrações: {$data['integracoes']}
    - Área principal indicada: {$data['area_ajuda']}
    ";

    $apiData = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ],
        "generationConfig" => [
            "responseMimeType" => "application/json"
        ]
    ];

    $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . GEMINI_API_KEY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    $ia_json_data = null;

    if (!$err && $response) {
        $resultData = json_decode($response, true);
        if (isset($resultData['candidates'][0]['content']['parts'][0]['text'])) {
            $aiText = $resultData['candidates'][0]['content']['parts'][0]['text'];
            
            $aiText = str_replace('```json', '', $aiText);$aiText = str_replace('```', '', $aiText);
            
            $aiParsed = json_decode(trim($aiText), true);
            
            if ($aiParsed) {
                $updateStmt = $pdo->prepare("UPDATE projects SET 
                    nivel_projeto = ?, area_principal = ?, stack_sugerida = ?, tags_ai = ?, impacto_estimado = ?, dificuldade_manutencao = ?, resumo_curto = ?, proxima_aula_recomendada = ?
                    WHERE id = ?");
                
                $updateStmt->execute([
                    $aiParsed['nivel_projeto'] ?? '',
                    $aiParsed['area_principal'] ?? '',
                    is_array($aiParsed['stack_sugerida']) ? implode(', ', $aiParsed['stack_sugerida']) : ($aiParsed['stack_sugerida'] ?? ''),
                    is_array($aiParsed['tags_ai']) ? implode(', ', $aiParsed['tags_ai']) : ($aiParsed['tags_ai'] ?? ''),
                    $aiParsed['impacto_estimado'] ?? '',
                    $aiParsed['dificuldade_manutencao'] ?? '',
                    $aiParsed['resumo_curto'] ?? '',
                    $aiParsed['proxima_aula_recomendada'] ?? '',
                    $projectId
                ]);
                $ia_json_data = $aiParsed;
            }
        }
    }

    echo json_encode(['success' => true, 'id' => $projectId, 'ia_data' => $ia_json_data]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
