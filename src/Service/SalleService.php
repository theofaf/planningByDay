<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;

class SalleService
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || !isset($data['libelle'])
            || (!isset($data['nbPlace']) || !is_int($data['nbPlace']))
            || !isset($data['equipementInfo'])
            || !isset($data['batimentId'])
        ) {
            return false;
        }
       return true;
    }
}