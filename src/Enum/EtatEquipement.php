<?php
namespace App\Enum;

enum EtatEquipement: string
{
    case ACTIF = 'actif';
    case INACTIF = 'inactif';
    case EN_MAINTENANCE = 'en_maintenance';
}
