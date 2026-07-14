<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case DELIVERED = 'DELIVERED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::CONFIRMED => 'Confirmado',
            self::DELIVERED => 'Entregado',
            self::CANCELLED => 'Cancelado',
        };
    }
}
