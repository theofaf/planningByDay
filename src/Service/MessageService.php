<?php

namespace App\Service;

class MessageService
{
    public function isDataValide(?array $data): bool
    {
        if (
            null === $data
            || !isset($data['receveurId'])
            || !isset($data['emetteurId'])
            || !isset($data['contenu'])
        ) {
            return false;
        }
        return true;
    }
}