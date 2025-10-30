<?php

namespace App\Http\Controllers;

use App\Models\Krathong;
use Illuminate\Http\Request;

class KrathongController extends Controller
{
    private array $types = [
        'banana'  => ['label' => 'กระทงใบตอง',   'img' => '/images/krathongs/banana.png'],
        'banana1' => ['label' => 'กระทงต้นกล้วย', 'img' => '/images/krathongs/nana1.png'],
        'flower'  => ['label' => 'กระทงดอกไม้',  'img' => '/images/krathongs/flower.png'],
        'candle'  => ['label' => 'กระทงเทียน',    'img' => '/images/krathongs/candle.png'],
        'eco'     => ['label' => 'กระทงรักษ์โลก', 'img' => '/images/krathongs/eco.png'],
        'silver'  => ['label' => 'กระทงเงิน', 'img' => '/images/krathongs/silver.png'],
        'gold'    => ['label' => 'กระทงทอง', 'img' => '/images/krathongs/gold.png'],
        'kurab'    => ['label' => 'กระทงกุหลาบ', 'img' => '/images/krathongs/kurab.png'],

        'dog'    => ['label' => 'กระทงสุนัข', 'img' => '/images/krathongs/dog.png'],
        'cat'    => ['label' => 'กระทงเหมียว', 'img' => '/images/krathongs/cat.png'],
        'baar'    => ['label' => 'กระทงหมี', 'img' => '/images/krathongs/baar.png'],
        'bird'    => ['label' => 'กระทงนก', 'img' => '/images/krathongs/bird.png'],
        'ped'    => ['label' => 'กระทงเป็ด', 'img' => '/images/krathongs/ped.png'],
        'buf'    => ['label' => 'กระทงกระบือ', 'img' => '/images/krathongs/buf.png'],
        'cown'    => ['label' => 'กระทงวัว', 'img' => '/images/krathongs/cown.png'],
        'elephant'    => ['label' => 'กระทงช้าง', 'img' => '/images/krathongs/elephant.png'],
        'hippo'    => ['label' => 'กระทงฮิปโป', 'img' => '/images/krathongs/hippo.png'],
        'pik'    => ['label' => 'กระทงหมู', 'img' => '/images/krathongs/pik.png'],
        'katai'    => ['label' => 'กระทงกระต่าย', 'img' => '/images/krathongs/katai.png'],
        'tao'    => ['label' => 'กระทงเต่า', 'img' => '/images/krathongs/tao.png'],
        'tao'    => ['label' => 'กระทงเต่า', 'img' => '/images/krathongs/tao.png'],
        'juad'    => ['label' => 'กระทงบั้งไฟ', 'img' => '/images/krathongs/juad.png'],
        'GateA'    => ['label' => 'กระทงเกรด A', 'img' => '/images/krathongs/GateA.png'],
        'GateF'    => ['label' => 'กระทงพ้น F', 'img' => '/images/krathongs/GateF.png'],
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
            'shareUrl' => 'https://loykrathong.pcnone.com',
            'shareTitle' => '🎑 คืนลอยกระทงไม่ต้องออกบ้าน
มาลอยออนไลน์กันหน่อย 🕯️🌊
ขอพรแล้วแชร์เลย 👉',
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
