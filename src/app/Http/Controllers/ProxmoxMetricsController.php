<?php

namespace App\Http\Controllers;

use App\Services\ProxmoxClient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProxmoxMetricsController extends Controller
{
    public function cpuJson(Request $req, ProxmoxClient $pve, string $node, string $vmid)
    {
        $timeframe = $req->query('timeframe', 'day');
        $type = $req->query('type', 'lxc'); // หรือ 'qemu'
        $points = $pve->rrdData($node, $vmid, $timeframe, $type);
        return response()->json(['points' => $points]);
    }

    public function cpuPng(Request $req, ProxmoxClient $pve, string $node, string $vmid)
    {
        $timeframe = $req->query('timeframe', 'day');
        $type = $req->query('type', 'lxc');
        $png = $pve->rrdPng($node, $vmid, $timeframe, $type);
        return response($png, Response::HTTP_OK, ['Content-Type' => 'image/png']);
    }
}
