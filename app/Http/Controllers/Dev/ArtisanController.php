<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use MetaFramework\Traits\Ajax;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ArtisanController extends Controller
{
    use Ajax;

    private array $output = [];
    private int $statusCode;

    public function optimizeClear(): array
    {
        $this->executeCommand('optimize:clear');
        if ($this->statusCode == 0) {
            $this->responseSuccess("Le cache application a été réinitialisé");
        }

        return $this->fetchResponse();
    }

    public function migrate(bool $rollback = false): array
    {
        $this->executeCommand('migrate' . ($rollback ? ':rollback' : ''));
        if ($this->statusCode == 0) {
            $this->responseSuccess("Opération terminée.");
        }
        return $this->fetchResponse();
    }

    private function executeCommand(string $command): void
    {
        $this->statusCode = Artisan::call($command, [], $outputBuffer = new BufferedOutput());
        $response = nl2br(trim($outputBuffer->fetch(), "\r\n"));
        if ($this->statusCode == 0) {
            $this->responseNotice($response);
        } else {
            $this->responseError($response);
        }
    }

    public function composerUpdate(): array
    {
        $this->executeShellCommand(['composer', 'selfu']);
        $this->executeShellCommand(['composer', 'u']);
        return $this->fetchResponse();
    }

    private function executeShellCommand(array $command): void
    {
        $process = new Process($command, base_path());
        $process->setTimeout(600); // Set timeout to 10 minutes

        try {
            $process->start();

            $output = '';

            $process->wait(function ($type, $buffer) use (&$output) {
                if ($type === Process::OUT) {
                    $output .= nl2br($buffer);
                } else {
                    $output .= nl2br("<span style='color: red;'>$buffer</span>");
                }
            });

            $this->responseNotice($output);
            $this->responseSuccess("Composer update completed successfully.");
        } catch (ProcessFailedException $exception) {
            $this->responseError(nl2br($exception->getMessage()));
        }
    }


}
