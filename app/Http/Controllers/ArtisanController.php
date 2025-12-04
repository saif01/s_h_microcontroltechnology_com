<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class ArtisanController extends Controller
{
    /**
     * Run any artisan command via HTTP (shared hosting helper).
     */
    public function run(Request $request): JsonResponse
    {
        $data = $request->validate([
            'command' => ['required', 'string'],
            'parameters' => ['array'],
        ]);

        $command = trim($data['command']);
        $parameters = $data['parameters'] ?? [];

        try {
            Artisan::call($command, $parameters);

            return response()->json([
                'status' => 'ok',
                'command' => $command,
                'parameters' => $parameters,
                'output' => Artisan::output(),
                'exit_code' => Artisan::exitCode(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'command' => $command,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

