<?php

namespace App\Http\Controllers;

use App\Http\Requests\Login\FinishTwoStepLogin;
use App\Http\Requests\Login\SmartIdLoginRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddedIdentityController extends Controller
{
    public function startMobileidLogin(Request $request, Client $client)
    {
        try {
            $url      = config('eid-easy.api-url') . "/api/identity/" . config('eid-easy.client-id') . "/mobile-id/start";
            $response = $client->post($url, [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                    'json'    => [
                        'secret' => config('eid-easy.secret'),
                        'phone'  => $request->input('phone'),
                        'idcode' => $request->input('idcode'),
                        'lang'   => 'en',
                    ]
                ]
            );
        } catch (ClientException $exception) {
            Log::error("Mobile-ID login start failed", [$exception]);
            $response     = $exception->getResponse();
            $responseData = json_decode((string)$response->getBody());
            return response()->json([
                'message' => $responseData->message,
            ], $response->getStatusCode());
        }

        $responseData = json_decode((string)$response->getBody());

        return response()->json([
            'challenge' => $responseData->challenge,
            'token'     => $responseData->token,
        ]);
    }

    public function finishMobileidLogin(FinishTwoStepLogin $request, Client $client)
    {
        try {
            $url      = config('eid-easy.api-url') . "/api/identity/" . config('eid-easy.client-id') . "/mobile-id/complete";
            $response = $client->post($url, [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                    'json'    => [
                        'secret' => config('eid-easy.secret'),
                        'token'  => $request->input('token'),
                        'lang'   => 'en',
                    ]
                ]
            );
        } catch (ClientException $exception) {
            Log::error("Mobile-ID login complete failed", [$exception]);
            $response     = $exception->getResponse();
            $responseData = json_decode((string)$response->getBody());
            return response()->json([
                'message' => $responseData->message,
            ], $response->getStatusCode());
        }

        $responseData = json_decode((string)$response->getBody());
        if ($responseData->status !== "OK") {
            return response()->json([
                'message' => $responseData->message,
            ], 400);
        }

        return response()->json($responseData);
    }

    public function startSmartIdLogin(SmartIdLoginRequest $request, Client $client)
    {
        try {
            $url      = config('eid-easy.api-url') . "/api/identity/" . config('eid-easy.client-id') . "/smart-id/start";
            $response = $client->post($url, [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                    'json'    => [
                        'secret'  => config('eid-easy.secret'),
                        'country' => $request->input('country'),
                        'idcode'  => $request->input('idcode'),
                        'lang'    => 'en',
                    ]
                ]
            );
        } catch (ClientException $exception) {
            Log::error("Smart-ID login start failed", [$exception]);
            $response     = $exception->getResponse();
            $responseData = json_decode((string)$response->getBody());
            return response()->json([
                'message' => $responseData->message,
            ], $response->getStatusCode());
        }

        $responseData = json_decode((string)$response->getBody());

        return response()->json([
            'challenge' => $responseData->challenge,
            'token'     => $responseData->token,
        ]);
    }

    public function finishSmartIdLogin(FinishTwoStepLogin $request, Client $client)
    {
        try {
            $url      = config('eid-easy.api-url') . "/api/identity/" . config('eid-easy.client-id') . "/smart-id/complete";
            $response = $client->post($url, [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                    'json'    => [
                        'secret' => config('eid-easy.secret'),
                        'token'  => $request->input('token'),
                        'lang'   => 'en',
                    ]
                ]
            );
        } catch (ClientException $exception) {
            Log::error("Smart-ID login complete failed", [$exception]);
            $response     = $exception->getResponse();
            $responseData = json_decode((string)$response->getBody());
            return response()->json([
                'message' => $responseData->message,
            ], $response->getStatusCode());
        }

        $responseData = json_decode((string)$response->getBody());
        unset($responseData->email);

        return response()->json($responseData);
    }

    public function idCardLogin(Request $request)
    {
        $data = $request->validate([
            'token'   => 'required',
            'country' => 'required',
            'lang'    => 'nullable'
        ]);

        $url      = config('eid-easy.api-url') . "/api/identity/" . config('eid-easy.client-id') . "/id-card/complete";
        $response = Http::post($url, [
            'secret'  => config('eid-easy.secret'),
            'token'   => $data['token'],
            'country' => $data['country'],
            'lang'    => $data['lang'] ?? 'en',
        ]);

        return response()->json($response->json(), $response->status());
    }
}
