<?php

namespace App\Http\Controllers;

use App\Models\Krathong;
use Illuminate\Http\Request;

class KrathongController extends Controller
{
    // ชนิดกระทงที่เปิดให้เลือก
    private array $types = [
        'banana' => ['label' => 'ใบตอง', 'img' => '/images/krathongs/banana.png'],
        'flower' => ['label' => 'ดอกไม้', 'img' => '/images/krathongs/flower.png'],
        'candle' => ['label' => 'เทียน',   'img' => '/images/krathongs/candle.png'],
        'eco'    => ['label' => 'รักษ์โลก', 'img' => '/images/krathongs/eco.png'],
    ];

    public function show()
    {
        // โหลดรายการล่าสุด 30 ชิ้นสำหรับปล่อยลอย
        $recent = Krathong::latest()->take(30)->get(['id','type','nickname','age','wish','created_at']);
        return view('krathong', [
            'types' => $this->types,
            'recent' => $recent,
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

        $krathong = Krathong::create($data + ['ip' => $request->ip()]);

        // ส่งข้อมูลกลับให้ JS สร้างกระทงลอยทันที
        return response()->json([
            'id'       => $krathong->id,
            'type'     => $krathong->type,
            'img'      => $this->types[$krathong->type]['img'],
            'nickname' => $krathong->nickname,
            'age'      => $krathong->age,
            'wish'     => $krathong->wish,
            'created'  => $krathong->created_at->toIso8601String(),
        ]);
    }
}
