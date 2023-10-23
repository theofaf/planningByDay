<?php

namespace App\Service;

class SessionService
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || !isset($data['dateFin'])
            || !isset($data['dateDebut'])
            || !isset($data['salleId'])
            || !isset($data['utilisateurId'])
            || !isset($data['moduleFormationId'])
            || !isset($data['classeId'])
        ) {
            return false;
        }
        return true;
    }
}