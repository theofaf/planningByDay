<?php

namespace App\Service;

use Exception;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogService
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function insererLog(String $message, Exception $exception): void
    {
        if ($exception->getCode() >= Level::Critical) {
            $this->logger->critical($message, [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]);
        } else {
            $this->logger->error($message, [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]);
        }
    }
}