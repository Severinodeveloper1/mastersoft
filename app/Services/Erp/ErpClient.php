<?php

namespace App\Services\Erp;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Read-only client for the Vesertech ERP API. The token lives only in memory for one run.
 */
class ErpClient
{
    private ?string $token = null;

    public function login(): void
    {
        $config = config('services.erp');

        if (blank($config['tenant'] ?? null) || blank($config['username'] ?? null) || blank($config['password'] ?? null)) {
            throw new ErpException('ERP credentials are not configured (ERP_TENANT, ERP_USERNAME, ERP_PASSWORD).');
        }

        $response = $this->send(fn () => $this->request()->post('/auth/login', [
            'usuario' => $config['username'],
            'clave' => $config['password'],
            'aplicacion' => $config['application'],
            'dispositivo' => $config['device'],
        ]), 'login');

        $token = $response->json('token');
        if ($response->json('status') !== 'success' || ! is_string($token) || $token === '') {
            throw new ErpException('ERP login did not return a token.');
        }

        $this->token = $token;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(string $path, array $query = []): array
    {
        $response = $this->get($path, $query);

        return $this->ensureList($response->json('data'), $path);
    }

    /**
     * Reads every page of a Laravel-paginated endpoint; any failed page aborts the whole read.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(string $path, array $query = []): array
    {
        $items = [];
        $page = 1;

        do {
            $response = $this->get($path, [...$query, 'page' => $page, 'per_page' => config('services.erp.per_page')]);
            $items = [...$items, ...$this->ensureList($response->json('data.data'), "{$path} page {$page}")];
            $lastPage = (int) $response->json('data.last_page', 1);
            $page++;
        } while ($page <= $lastPage);

        return $items;
    }

    private function get(string $path, array $query): Response
    {
        if ($this->token === null) {
            throw new ErpException('ERP client is not logged in.');
        }

        $response = $this->send(fn () => $this->request()->withToken($this->token)->get($path, $query), $path);

        if ($response->json('status') !== 'success') {
            throw new ErpException("ERP request {$path} did not succeed.");
        }

        return $response;
    }

    private function ensureList(mixed $data, string $what): array
    {
        if (! is_array($data) || ! array_is_list($data)) {
            throw new ErpException("ERP response for {$what} has no data list.");
        }

        return $data;
    }

    /**
     * @param  callable(): Response  $call
     */
    private function send(callable $call, string $what): Response
    {
        try {
            $response = $call();
        } catch (ConnectionException $exception) {
            throw new ErpException("ERP request {$what} could not connect: {$exception->getMessage()}", previous: $exception);
        }

        if ($response->failed()) {
            throw new ErpException("ERP request {$what} failed with HTTP {$response->status()}.");
        }

        return $response;
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.erp.base_url'), '/'))
            ->withHeaders(['X-Tenant-Id' => config('services.erp.tenant')])
            ->acceptJson()
            ->timeout(config('services.erp.timeout'))
            ->retry(2, 1000, fn ($exception) => $exception instanceof ConnectionException
                || ($exception instanceof RequestException && $exception->response->serverError()), throw: false);
    }
}
