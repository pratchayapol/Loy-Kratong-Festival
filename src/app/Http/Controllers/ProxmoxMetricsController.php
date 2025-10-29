<?php

namespace App\Http\Controllers;

use App\Services\ProxmoxClient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProxmoxMetricsController extends Controller
{
    public function cpuJson(Request $req, ProxmoxClient $pve, string $node, string $vmid)
    {
        if ($node === 'undefined' || $vmid === 'undefined' || $node === '' || $vmid === '') {
            return response()->json(['error' => 'Missing node/vmid'], 422);
        }
        $timeframe = $req->query('timeframe', 'day');
        $type = $req->query('type', 'lxc');

        try {
            $points = $pve->rrdData($node, $vmid, $timeframe, $type);
            return response()->json(['points' => $points]);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json([
                'error' => 'Upstream error',
                'status' => optional($e->response)->status(),
                'body' => optional($e->response)->body(),
            ], 502);
        }
    }

    public function cpuPng(Request $req, ProxmoxClient $pve, string $node, string $vmid)
    {
        $timeframe = $req->query('timeframe', 'day');
        $type = $req->query('type', 'lxc');
        $png = $pve->rrdPng($node, $vmid, $timeframe, $type);
        return response($png, Response::HTTP_OK, ['Content-Type' => 'image/png']);
    }
}
