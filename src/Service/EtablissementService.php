<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;

class EtablissementService
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || !isset($data['libelle'])
            || !isset($data['numVoie'])
            || !isset($data['rue'])
            || !isset($data['ville'])
            || !isset($data['codePostal'])
            || !isset($data['numeroTel'])
        ) {
            return false;
        }
        return true;
    }
}
