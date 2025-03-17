<?php

namespace App\Enum;

enum Statut: int
{
    case Accepter = 1;
    case Refuser = 2;
    case EnCours = 3;

    public function label(): string
    {
        return match($this) {
            self::Accepter => 'Acceptée',
            self::Refuser => 'Refusée',
            self::EnCours => 'En cours',
        };
    }
}