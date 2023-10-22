<?php

namespace App\Service;

class UtilisateurService
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || !isset($data['nom'])
            || !isset($data['prenom'])
            || !isset($data['email'])
            || !isset($data['roles'])
            || !isset($data['etablissementId'])
        ) {
            return false;
        }
        return true;
    }
}