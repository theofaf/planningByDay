<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;

class BatimentService
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || null === $data['libelle']
            || !is_int($data['numVoie'])
            || null === $data['rue']
            || null === $data['ville']
            || null === $data['codePostal']
            || null === $data['numeroTel']
            || null === $data['etablissementId']
        ) {
            return false;
        }
       return true;
    }
}