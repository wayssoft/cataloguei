<?php 
function verificarHorarioLoja($horarios) {
    date_default_timezone_set('America/Sao_Paulo');
    $agora = new DateTime();
    $status = 'fechado';
    $tempoRestante = null;

    foreach ($horarios as $horario) {
        $abertura = new DateTime($horario['abertura']);
        $fechamento = new DateTime($horario['fechamento']);

        // Verifica se o horário de fechamento é menor que o horário de abertura (indica que o horário passa pela meia-noite)
        if ($fechamento < $abertura) {
            $fechamento->modify('+1 day');
        }

        // Verifica se a loja está aberta
        if ($horario['horario_ativo'] && $agora >= $abertura && $agora <= $fechamento) {
            $status = 'aberto';
            break;
        }

        // Calcula o tempo restante para a próxima abertura se a loja estiver fechada
        if ($agora < $abertura && ($tempoRestante === null || $abertura < $tempoRestante)) {
            $tempoRestante = $abertura;
        }
    }

    if ($status === 'fechado' && $tempoRestante !== null) {
        $intervalo = $agora->diff($tempoRestante);
        $tempoRestanteStr = $intervalo->format('%h horas e %i minutos');
    } else {
        $tempoRestanteStr = 'N/A';
    }

    return [
        'status' => $status,
        'tempo_restante_para_abrir' => $tempoRestanteStr
    ];
}

function obterDiaDaSemana() {
    date_default_timezone_set('America/Sao_Paulo');
    $diaDaSemanaNumero = date('w');
    $diasDaSemana = [
        'domingo',
        'segunda',
        'terca',
        'quarta',
        'quinta',
        'sexta',
        'sabado'
    ];

    return $diasDaSemana[$diaDaSemanaNumero];
}
?>