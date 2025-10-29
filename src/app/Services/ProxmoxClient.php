<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class ProxmoxClient
{
    private string $baseUrl;
    private string $auth;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.proxmox.url'), '/');
        $this->auth    = config('services.proxmox.token');
    }

    /**
     * timeframe: hour|day|week|month|year
     * type: lxc|qemu
     */
    public function rrdData(string $node, string $vmid, string $timeframe = 'day', string $type = 'lxc'): array
    {
        $url = "{$this->baseUrl}/api2/json/nodes/{$node}/{$type}/{$vmid}/rrddata";
        $resp = Http::timeout(10)
            ->withHeaders(['Authorization' => $this->auth])
            ->get($url, ['timeframe' => $timeframe, 'cf' => 'AVERAGE']);

        if ($resp->failed()) {
            throw new RequestException($resp);
        }

        $data = $resp->json('data', []);
        // map -> [{t:<ms>, cpuPct:<0-100>}]
        $points = collect($data)
            ->filter(fn ($p) => isset($p['time']) && isset($p['cpu']))
            ->map(fn ($p) => [
                't' => $p['time'] * 1000,
                'cpuPct' => round(($p['cpu'] ?? 0) * 100, 2),
            ])
            ->values()
            ->all();

        return $points;
    }

    // PNG passthrough
    public function rrdPng(string $node, string $vmid, string $timeframe = 'day', string $type = 'lxc'): string
    {
        $url = "{$this->baseUrl}/api2/png/nodes/{$node}/{$type}/{$vmid}/rrd";
        $resp = Http::timeout(10)
            ->withHeaders(['Authorization' => $this->auth])
            ->get($url, ['timeframe' => $timeframe, 'ds' => 'cpu']);

        if ($resp->failed()) {
            throw new RequestException($resp);
        }
        return $resp->body();
    }
}
