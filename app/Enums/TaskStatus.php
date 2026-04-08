<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: string
{
    case Pendente = 'pendente';
    case EmAndamento = 'em_andamento';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::EmAndamento => 'Em andamento',
            self::Concluida => 'Concluida',
            self::Cancelada => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pendente => '#FBBF24', // Amarelo
            self::EmAndamento => '#3B82F6', // Azul
            self::Concluida => '#10B981', // Verde
            self::Cancelada => '#EF4444', // Vermelho
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::Pendente => '#FEF3C7', // Amarelo claro
            self::EmAndamento => '#DBEAFE', // Azul claro
            self::Concluida => '#D1FAE5', // Verde claro
            self::Cancelada => '#FEE2E2', // Vermelho claro
        };
    }
}
