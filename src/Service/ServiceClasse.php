<?php

namespace App\Service;

class ServiceClasse
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || !isset($data['libelle'])
            || (!isset($data['nombreEleves']) || !is_int($data['nombreEleves']))
            || !isset($data['cursusId'])
        ) {
            return false;
        }
        return true;
    }
}