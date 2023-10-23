<?php

namespace App\Service;

class UtilisateurService
{
    public function isDataValide(?array $data, ?bool $pasDeRoleEtablissement = true): bool
    {
        if (
            null === $data
            || !isset($data['nom'])
            || !isset($data['prenom'])
            || !isset($data['email'])
            || (!$pasDeRoleEtablissement && !isset($data['roles']))
            || (!$pasDeRoleEtablissement && !isset($data['etablissementId']))
        ) {
            return false;
        }
        return true;
    }
}