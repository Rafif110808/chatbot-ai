<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAiSettingRequest;
use App\Models\AiSetting;
use Illuminate\Http\JsonResponse;

class AiSettingController extends Controller
{
    public function show(): JsonResponse
    {
        $setting = AiSetting::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'system_prompt' => null,
                'language' => 'indonesia',
                'answer_length' => 'medium',
            ]
        );

        return response()->json($setting);
    }

    public function update(UpdateAiSettingRequest $request): JsonResponse
    {
        $setting = AiSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->validated()
        );

        return response()->json([
            'message' => 'Pengaturan AI berhasil disimpan.',
            'data' => $setting,
        ]);
    }
}
