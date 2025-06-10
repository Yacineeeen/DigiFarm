<?php
namespace App\Enum;

enum StatutMaintenance: string
{
    case EN_ATTENTE = 'en attente';
    case EN_COURS = 'en cours';
    case TERMINEE = 'terminée';
}
