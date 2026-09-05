<?php

namespace App\Service;

class ComptesMainEventClient
{
    private const DEFAULT_API_URL = 'https://api.vhep.fr';
    private const TIMEOUT_SECONDS = 5;

    private string $apiUrl;

    public function __construct(string $apiUrl = self::DEFAULT_API_URL)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return array{players: list<array{pseudo: string, prenom: string, nom: string, club: string}>, error: ?string}
     */
    public function fetchParticipants(): array
    {
        $url = rtrim($this->apiUrl !== '' ? $this->apiUrl : self::DEFAULT_API_URL, '/') . '/api/v1/public/main-event';

        try {
            $payload = $this->requestJson($url);
        } catch (\Throwable $e) {
            return $this->failure();
        }

        if (!isset($payload['participants']) || !is_array($payload['participants'])) {
            return $this->failure();
        }

        $players = [];
        foreach ($payload['participants'] as $participant) {
            if (!is_array($participant)) {
                continue;
            }

            $players[] = [
                'pseudo' => (string) ($participant['pseudo'] ?? ''),
                'prenom' => (string) ($participant['first_name'] ?? ''),
                'nom' => (string) ($participant['last_name'] ?? ''),
                'club' => (string) ($participant['club'] ?? ''),
            ];
        }

        return [
            'players' => $players,
            'error' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function requestJson(string $url): array
    {
        [$json, $status] = $this->httpGet($url);
        if ($json === null) {
            throw new \RuntimeException('Unable to reach Comptes API');
        }

        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException("Comptes API returned HTTP $status");
        }

        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            throw new \RuntimeException('Invalid JSON from Comptes API');
        }

        return $payload;
    }

    /**
     * @return array{0: ?string, 1: int}
     */
    private function httpGet(string $url): array
    {
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            if ($curl !== false) {
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => self::TIMEOUT_SECONDS,
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json',
                        'User-Agent: MainEvent/1.0',
                    ],
                ]);
                $body = curl_exec($curl);
                $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if (is_string($body)) {
                    return [$body, $status];
                }
            }
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => self::TIMEOUT_SECONDS,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\nUser-Agent: MainEvent/1.0\r\n",
            ],
        ]);

        $json = @file_get_contents($url, false, $context);
        if ($json === false) {
            return [null, 0];
        }

        return [$json, $this->statusCodeFromHeaders($http_response_header ?? [])];
    }

    /**
     * @param list<string> $headers
     */
    private function statusCodeFromHeaders(array $headers): int
    {
        foreach ($headers as $header) {
            if (preg_match('#^HTTP/\S+\s+(\d+)#', $header, $match)) {
                return (int) $match[1];
            }
        }

        return 0;
    }

    /**
     * @return array{players: list<array{pseudo: string, prenom: string, nom: string, club: string}>, error: string}
     */
    private function failure(): array
    {
        return [
            'players' => [],
            'error' => "La liste des inscrits est temporairement indisponible. Merci de réessayer plus tard.",
        ];
    }
}
