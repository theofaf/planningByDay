<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;

class BatimentService
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || !isset($data['libelle'])
            || (!isset($data['numVoie']) || !is_int($data['numVoie']))
            || !isset($data['rue'])
            || !isset($data['ville'])
            || !isset($data['codePostal'])
            || !isset($data['numeroTel'])
            || !isset($data['etablissementId'])
        ) {
            return false;
        }
       return true;
    }
}