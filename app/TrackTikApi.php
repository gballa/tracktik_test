<?php

namespace App;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Throwable;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;

class TrackTikApi
{

    private string $base_url;
    private string $token;
    private string $refresh_token;
    private PendingRequest $client;

    public function __construct()
    {
        $this->base_url = env('TRACKTIK_URL', 'https://smoke.staffr.net/rest/v1');
        $this->token = env('TRACKTIK_TOKEN');
        $this->refresh_token = env('TRACKTIK_REFRESH_TOKEN');
        $this->client = $this->createClient();
    }

    private function createClient(): PendingRequest
    {
        return Http::withToken($this->token)->retry(2, 0, function (Exception $exception, PendingRequest $request): bool
        {
            if (! $exception instanceof RequestException || $exception->response->status() !== 401) {
                return false;
            }
            $this->refreshToken();
            $request->withToken($this->token);
            return true;
        });
    }

    private function refreshToken()
    {
        $path = $this->base_url.'/auth/refresh';
        try {
            $response = Http::withToken($this->token)->post($path, [
                'refreshToken' => $this->refresh_token
            ]);
            $this->token = $response->json()['auth']['token'];
            $this->refresh_token = $response->json()['auth']['refreshToken'];
        } catch (ConnectionException $e) {
            throw new \Error('An error while refreshing token.');
        }

    }

    public function getEmployees()
    {
        $path = $this->base_url . '/employees';
        try {
            $response = $this->client->get($path);
            return $response->json();
        } catch (ConnectionException $e) {
            throw new \Error('An error while getting list of employee.');
        }
    }

    public function getEmployee($id)
    {
        try {
            $path = $this->base_url . '/employees/' . $id;
            $response = $this->client->get($path);
            return $response->json();
        } catch (RequestException $e) {
            return null;
        }
    }

    public function createEmployee($data)
    {
        $path = $this->base_url . '/employees';
        try {
            $response = $this->client->post($path, $data);
            return $response->json();
        } catch (RequestException $e) {
            throw new \Error('An error while creating employee.');
        }
    }

    public function updateEmployee($id, $data)
    {
        $path = $this->base_url . '/employees/' . $id;
        try {
            $employee = $this->getEmployee($id);
            if (empty($employee)) {
                throw new \Error('Employee not found');
            }
            $response = $this->client->put($path, $data);
            return $response->json();
        } catch (RequestException $e) {
            throw new \Error('An error while updating employee.');
        }
    }

    public function getRegions()
    {
        $path = $this->base_url . '/regions';
        try {
            $response = $this->client->get($path);
            return $response->json();
        } catch (RequestException $e) {
            throw new \Error('An error while getting regions.');
        }
    }
}
