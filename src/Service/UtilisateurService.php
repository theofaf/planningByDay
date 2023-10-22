<?php

namespace App\Service;

class UtilisateurService
{
    public function isDataValide(?array $data, ?bool $pasDeRole = true): bool
    {
        if (
            null === $data
            || !isset($data['nom'])
            || !isset($data['prenom'])
            || !isset($data['email'])
            || (!$pasDeRole && !isset($data['roles']))
            || !isset($data['etablissementId'])
        ) {
            return false;
        }
        return true;
    }
}