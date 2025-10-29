<?php

namespace App\Http\Controllers;

use App\Models\Krathong;
use Illuminate\Http\Request;

class KrathongController extends Controller
{
    private array $types = [
        'banana'  => ['label' => 'ใบตอง',   'img' => '/images/krathongs/banana.png'],
        'banana1' => ['label' => 'ต้นกล้วย', 'img' => '/images/krathongs/nana1.png'],
        'flower'  => ['label' => 'ดอกไม้',  'img' => '/images/krathongs/flower.png'],
        'candle'  => ['label' => 'เทียน',    'img' => '/images/krathongs/candle.png'],
        'eco'     => ['label' => 'รักษ์โลก', 'img' => '/images/krathongs/eco.png'],
        'silver'  => ['label' => 'กระทงเงิน', 'img' => '/images/krathongs/silver.png'],
        'gold'    => ['label' => 'กระทงทอง', 'img' => '/images/krathongs/gold.png'],
        'kurab'    => ['label' => 'กระทงกุหลาบ', 'img' => '/images/krathongs/kurab.png'],
    ];

    public function metrics()
    {
        return response()->json([
            'total' => \App\Models\Krathong::count(),
            'updated' => now()->toIso8601String(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function show()
    {
        $recent = Krathong::latest()
            ->take(30)
            ->get(['id', 'type', 'nickname', 'age', 'wish', 'created_at']);

        $total = Krathong::count(); // จำนวนทั้งหมด

        return view('krathong', [
            'types'  => $this->types,
            'recent' => $recent,
            'total'  => $total,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'     => 'required|in:' . implode(',', array_keys($this->types)),
            'nickname' => 'required|string|max:50',
            'age'      => 'required|integer|min:1|max:120',
            'wish'     => 'required|string|max:200',
        ]);

        $k = Krathong::create($data + ['ip' => $request->ip()]);

        return response()->json([
            'id'       => $k->id,
            'type'     => $k->type,
            'img'      => $this->types[$k->type]['img'],
            'nickname' => $k->nickname,
            'age'      => $k->age,
            'wish'     => $k->wish,
            'created'  => $k->created_at->toIso8601String(),
        ]);
    }
}
