<?php

namespace App\Http\Controllers;

use App\Http\Requests\Login\FinishTwoStepLoginRequest;
use App\Http\Requests\Login\SmartIdLoginRequest;
use App\Http\Resources\CompanyResource;
use App\Models\User;
use App\Services\CompanyService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function whoAmI()
    {
        $companyService = app(CompanyService::class);
        $companies = Auth::check() ? $companyService->getUserCompanies(Auth::id()) : collect([]);

        return response()->json([
            'user' => Auth::user(),
            'companies' => CompanyResource::collection($companies),
        ]);
    }

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

    public function finishMobileidLogin(FinishTwoStepLoginRequest $request, Client $client)
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

        return $this->authenticate(
            $responseData->idcode,
            $responseData->firstname ?? null,
            $responseData->lastname ?? null,
            $responseData->country ?? null,
        );
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

    public function finishSmartIdLogin(FinishTwoStepLoginRequest $request, Client $client)
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

        return $this->authenticate(
            $responseData->idcode,
            $responseData->firstname ?? null,
            $responseData->lastname ?? null,
            $responseData->country ?? null,
        );
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

        $responseData = json_decode((string)$response->getBody());

        return $this->authenticate(
            $responseData->idcode,
            $responseData->firstname ?? null,
            $responseData->lastname ?? null,
            $responseData->country ?? null,
        );
    }

    private function authenticate(string $idcode, ?string $firstName, ?string $lastName, ?string $country): Response
    {
        $user = User::firstOrNew(['idcode' => $idcode]);
        $user->idcode = $idcode;
        $user->first_name = $firstName ?: $user->first_name;
        $user->last_name = $lastName ?: $user->last_name;
        $user->country = $country ?: $user->country;
        $user->save();

        Auth::login($user);

        $companyService = app(CompanyService::class);
        $companies = $companyService->getUserCompanies($user->id);

        return response(['status' => 'OK', 'user' => $user, 'companies' => CompanyResource::collection($companies)]);
    }
}
