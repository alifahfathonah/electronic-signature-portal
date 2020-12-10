<?php

namespace App\Http\Controllers;

use App\Models\SignatureContainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    public function getIdcardToken()
    {
        // TODO error handling.
        $response = Http::post(env('EID_API_URL') . "/api/signatures/integration/id-card/get-token", [
            'client_id' => env('EID_CLIENT_ID'),
            'secret'    => env('EID_SECRET'),
            'method'    => 'id-signature',
        ]);

        return response()->json(['token' => $response->json()['token']]);
    }

    public function getSignatureDigest(Request $request)
    {
        // TODO check permissions.
        $signatureContainer = SignatureContainer::find($request->fileid);

        // TODO get actual container files to get digest.

//        $tempFile = tempnam(sys_get_temp_dir(), 'signature');
//        file_put_contents($tempFile, $content);
//        $mimeType = mime_content_type($tempFile);

        $files   = [];
        $files[] = [
            'fileName'    => 'test.pdf',
            'fileContent' => base64_encode(hash('sha256', Storage::get('/company1/1/test.pdf'), true)),
            'mimeType'    => 'application/pdf',
        ];
        $files[] = [
            'fileName'    => 'test.txt',
            'fileContent' => base64_encode(hash('sha256', Storage::get('/company1/1/test.txt'), true)),
            'mimeType'    => 'text/plain'
        ];

        $prepareResponse = Http::post(env('EID_API_URL') . "/api/signatures/prepare-files-for-signing", [
            'client_id'      => env('EID_CLIENT_ID'),
            'secret'         => env('EID_SECRET'),
            'container_type' => 'xades',
            'baseline'       => 'B', //TODO remove to default to LT
            'files'          => $files
        ]);

        $docId = $prepareResponse->json()['doc_id'];

        $startSigningResponse = Http::withHeaders(['accept' => 'application/json'])->post(env('EID_API_URL') . "/api/signatures/start-signing", [
            'client_id'   => env('EID_CLIENT_ID'),
            'doc_id'      => $docId,
            'sign_type'   => 'id-card',
            'certificate' => $request->input('certificate'),
        ]);

        if ($startSigningResponse->failed()) {
            Log::error("Start signing failed", $startSigningResponse->json());
            return $startSigningResponse->body();
        }

        return response()->json([
            'hexDigest' => $startSigningResponse->json()['hexDigest'],
            'doc_id'    => $docId,
        ]);
    }

    public function finishSignature(Request $request)
    {
        $signedFile = "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PGRzOlNpZ25hdHVyZSB4bWxuczpkcz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyIgSWQ9ImlkLWQxMGZmNTdjM2U3YTYyMDMxNzBmNjZkODk1MTA3YmVjIj48ZHM6U2lnbmVkSW5mbz48ZHM6Q2Fub25pY2FsaXphdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyIvPjxkczpTaWduYXR1cmVNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNlY2RzYS1zaGEyNTYiLz48ZHM6UmVmZXJlbmNlIElkPSJyLWlkLWQxMGZmNTdjM2U3YTYyMDMxNzBmNjZkODk1MTA3YmVjLTEiIFVSST0idGVzdC5wZGYiPjxkczpEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGVuYyNzaGEyNTYiLz48ZHM6RGlnZXN0VmFsdWU+WUE0VmZUL3NaenVITzQxVjNuSlFIbmpaZFZxQzB2clZUZ3c5T3Z4Wm5aaz08L2RzOkRpZ2VzdFZhbHVlPjwvZHM6UmVmZXJlbmNlPjxkczpSZWZlcmVuY2UgSWQ9InItaWQtZDEwZmY1N2MzZTdhNjIwMzE3MGY2NmQ4OTUxMDdiZWMtMiIgVVJJPSJ0ZXN0LnR4dCI+PGRzOkRpZ2VzdE1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMDQveG1sZW5jI3NoYTI1NiIvPjxkczpEaWdlc3RWYWx1ZT5HSlNobklXNkZUckw5ME9zVGtQOEFFeUpGZ1N5YjR4cDRlZytvcS9IeEk4PTwvZHM6RGlnZXN0VmFsdWU+PC9kczpSZWZlcmVuY2U+PGRzOlJlZmVyZW5jZSBUeXBlPSJodHRwOi8vdXJpLmV0c2kub3JnLzAxOTAzI1NpZ25lZFByb3BlcnRpZXMiIFVSST0iI3hhZGVzLWlkLWQxMGZmNTdjM2U3YTYyMDMxNzBmNjZkODk1MTA3YmVjIj48ZHM6VHJhbnNmb3Jtcz48ZHM6VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4jIi8+PC9kczpUcmFuc2Zvcm1zPjxkczpEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGVuYyNzaGEyNTYiLz48ZHM6RGlnZXN0VmFsdWU+QXpUYWN4dTJSUHd2RURSWW1rT3Z5dlU1SWRKVlhoVTZZdmVyd2RtcHRlRT08L2RzOkRpZ2VzdFZhbHVlPjwvZHM6UmVmZXJlbmNlPjwvZHM6U2lnbmVkSW5mbz48ZHM6U2lnbmF0dXJlVmFsdWUgSWQ9InZhbHVlLWlkLWQxMGZmNTdjM2U3YTYyMDMxNzBmNjZkODk1MTA3YmVjIj4xQU14MSs5M2dnMStjeG5kdmtTbUJJYjJsRE9IVUx1ZytiZ2tFcGxrOXNVd3JHa0ZhQkhSVEt2T003WVRxUGxMZUJMNHBBekxRZFAvNnZ2OXhvYVFYVEt0UEhoaEVxZVNOSnlLV2RrV05MeVVHL2Flc1ZibTdSMFowNmphNjlXMDwvZHM6U2lnbmF0dXJlVmFsdWU+PGRzOktleUluZm8+PGRzOlg1MDlEYXRhPjxkczpYNTA5Q2VydGlmaWNhdGU+TUlJRndEQ0NBNmlnQXdJQkFnSVFPTW0vMEpSN3ppMWFBcmNFZ2pFM1BUQU5CZ2txaGtpRzl3MEJBUXNGQURCak1Rc3dDUVlEVlFRR0V3SkZSVEVpTUNBR0ExVUVDZ3daUVZNZ1UyVnlkR2xtYVhSelpXVnlhVzFwYzJ0bGMydDFjekVYTUJVR0ExVUVZUXdPVGxSU1JVVXRNVEEzTkRjd01UTXhGekFWQmdOVkJBTU1Ea1ZUVkVWSlJDMVRTeUF5TURFMU1CNFhEVEUzTVRFd09EQTNORGt5TkZvWERUSXlNVEF4TWpJd05UazFPVm93Z1pJeEN6QUpCZ05WQkFZVEFrVkZNUTh3RFFZRFZRUUtEQVpGVTFSRlNVUXhHakFZQmdOVkJBc01FV1JwWjJsMFlXd2djMmxuYm1GMGRYSmxNU0F3SGdZRFZRUUREQmRRUVV4QkxFMUJVa2RWVXl3ek9ERXhNakE0TmpBeU56RU5NQXNHQTFVRUJBd0VVRUZNUVRFUE1BMEdBMVVFS2d3R1RVRlNSMVZUTVJRd0VnWURWUVFGRXdzek9ERXhNakE0TmpBeU56QjJNQkFHQnlxR1NNNDlBZ0VHQlN1QkJBQWlBMklBQkNUdW9KcUVobUJzK1ZnSG1ZNElCTUhnenpEV1J3ZVBuNEw3aWNyOC85T0phVnBXNzZBc21sRXNxMmN5YTQ5WHNpWUN5OEdUdG9laysvWWQvM1c4eXFsQWR3RXZMZU9KSEJGd0lPY200MDgvUWZnUWxCRjdXUXBnMGJUcHltc0lLS09DQWV3d2dnSG9NQWtHQTFVZEV3UUNNQUF3RGdZRFZSMFBBUUgvQkFRREFnWkFNRlFHQTFVZElBUk5NRXN3UGdZSkt3WUJCQUhPSHdFQk1ERXdMd1lJS3dZQkJRVUhBZ0VXSTJoMGRIQnpPaTh2ZDNkM0xuTnJMbVZsTDNKbGNHOXphWFJ2YjNKcGRXMHZRMUJUTUFrR0J3UUFpK3hBQVFJd0hRWURWUjBPQkJZRUZGQSs0TkVHRXArMDc1akExZ2FiVVB0MHlIQVBNSUdLQmdnckJnRUZCUWNCQXdSK01Id3dDQVlHQkFDT1JnRUJNQWdHQmdRQWprWUJCREJSQmdZRUFJNUdBUVV3UnpCRkZqOW9kSFJ3Y3pvdkwzTnJMbVZsTDJWdUwzSmxjRzl6YVhSdmNua3ZZMjl1WkdsMGFXOXVjeTFtYjNJdGRYTmxMVzltTFdObGNuUnBabWxqWVhSbGN5OFRBa1ZPTUJNR0JnUUFqa1lCQmpBSkJnY0VBSTVHQVFZQk1COEdBMVVkSXdRWU1CYUFGTE9yaUx5WjFXS2toU29JemJRZGNqdURja2RSTUdvR0NDc0dBUVVGQndFQkJGNHdYREFuQmdnckJnRUZCUWN3QVlZYmFIUjBjRG92TDJGcFlTNXpheTVsWlM5bGMzUmxhV1F5TURFMU1ERUdDQ3NHQVFVRkJ6QUNoaVZvZEhSd09pOHZZeTV6YXk1bFpTOUZVMVJGU1VRdFUwdGZNakF4TlM1a1pYSXVZM0owTUR3R0ExVWRId1ExTURNd01hQXZvQzJHSzJoMGRIQTZMeTkzZDNjdWMyc3VaV1V2WTNKc2N5OWxjM1JsYVdRdlpYTjBaV2xrTWpBeE5TNWpjbXd3RFFZSktvWklodmNOQVFFTEJRQURnZ0lCQUFzUFI2UzlmeWVaOVQ4bUNwaWRzeDd0SWhqUndaK3JrSm5CODQ5QjJKK3R2NTl5eFNBOTh4VisvY2dObkpYVGZNbWZIdXp4ZHdsaWZUYldxY1N2SHNHcU1zNkJqUFg1YzRNSmxKUyt6NDdIZmZScEtJMXd0aGN0eUFsSEtJekcraEEyQ1RsSFpCd1EwMHY0YmRqaEJGckVhbTVnQWdram53M0U1aXF3TE53eFdhbnF2bS9wSHlZS0RUc0NDdWFnNFRnZU1EVXZrTVMzWmVZQmJKVUFjRmc3VVhrMW5JbkRSMXRaOEUxZEF2VlNjWVlraWVUaU9YTk5HNjF6bmhQOFRGMUlRaWVxMCtvUDZjNk1Bc0ZHWWdYSlhJZWYwdngxYll1VjlncjQxNmFvUTRJSG9GWnZZWGRNMkZ3TFJrQTdnZytkNGxjVEc3WE05aEJVZjNhOHJ3RjI2V1RiWTdwbkV2TGQ1b2k4bTNmempkdmRnd2hDWVJzdFhLU1BTQ1ViQ0I4RW5RV29ZRFdjcnljaW9SQ3YwNzFIVWpleTJhMnFNbWtpM2U1SW43Vy9lekNCbkJWLzM4SHg4TjR6Skl0N1VsT1VzOVJzUWQyOE9MK3hyQjd1ZlorcVF4RkZLVSs5b3pUOFcxRURCRDBjWEErR1M2QjA2TGI0TlZOLzBrY3F3MTJyQU1nZ2xXTitaREtVMnRYZFFsa0NUYmtNZHQ3OTR6ZjBDTnFXMERiWUpvUFhmT2d4WDEzQTdiTkhtQjlXZ2JDY0RFZ2luWmk0ajU2STB6cjdna1ovUXp3NS9ydjVteGNZQlpjdVB0R2VZUGJDZ0U3TEpLZmZZblVTNjY3WExLeE1aZzRlSnlHNWpBT2xwbTRndkx3MWJPZUVRazQ3N3VhcnBUdFRma2ZnPC9kczpYNTA5Q2VydGlmaWNhdGU+PC9kczpYNTA5RGF0YT48L2RzOktleUluZm8+PGRzOk9iamVjdD48eGFkZXM6UXVhbGlmeWluZ1Byb3BlcnRpZXMgeG1sbnM6eGFkZXM9Imh0dHA6Ly91cmkuZXRzaS5vcmcvMDE5MDMvdjEuMy4yIyIgVGFyZ2V0PSIjaWQtZDEwZmY1N2MzZTdhNjIwMzE3MGY2NmQ4OTUxMDdiZWMiPjx4YWRlczpTaWduZWRQcm9wZXJ0aWVzIElkPSJ4YWRlcy1pZC1kMTBmZjU3YzNlN2E2MjAzMTcwZjY2ZDg5NTEwN2JlYyI+PHhhZGVzOlNpZ25lZFNpZ25hdHVyZVByb3BlcnRpZXM+PHhhZGVzOlNpZ25pbmdUaW1lPjIwMjAtMTItMDdUMTk6NTc6MDVaPC94YWRlczpTaWduaW5nVGltZT48eGFkZXM6U2lnbmluZ0NlcnRpZmljYXRlVjI+PHhhZGVzOkNlcnQ+PHhhZGVzOkNlcnREaWdlc3Q+PGRzOkRpZ2VzdE1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMDQveG1sZW5jI3NoYTUxMiIvPjxkczpEaWdlc3RWYWx1ZT5lcUduUnM4WUF0bFZmdDFqQ1BjTWlvMjJmVkZuVks4ZHp3eVhMcHQ0QXpUMTJOdmR5RHdhTG96Vzk3SForc0Evd3ZQclRMd2xvRnRhb09XWXFTNjNBQT09PC9kczpEaWdlc3RWYWx1ZT48L3hhZGVzOkNlcnREaWdlc3Q+PHhhZGVzOklzc3VlclNlcmlhbFYyPk1Ic3daNlJsTUdNeEN6QUpCZ05WQkFZVEFrVkZNU0l3SUFZRFZRUUtEQmxCVXlCVFpYSjBhV1pwZEhObFpYSnBiV2x6YTJWemEzVnpNUmN3RlFZRFZRUmhEQTVPVkZKRlJTMHhNRGMwTnpBeE16RVhNQlVHQTFVRUF3d09SVk5VUlVsRUxWTkxJREl3TVRVQ0VEakp2OUNVZTg0dFdnSzNCSUl4TnowPTwveGFkZXM6SXNzdWVyU2VyaWFsVjI+PC94YWRlczpDZXJ0PjwveGFkZXM6U2lnbmluZ0NlcnRpZmljYXRlVjI+PC94YWRlczpTaWduZWRTaWduYXR1cmVQcm9wZXJ0aWVzPjx4YWRlczpTaWduZWREYXRhT2JqZWN0UHJvcGVydGllcz48eGFkZXM6RGF0YU9iamVjdEZvcm1hdCBPYmplY3RSZWZlcmVuY2U9IiNyLWlkLWQxMGZmNTdjM2U3YTYyMDMxNzBmNjZkODk1MTA3YmVjLTEiPjx4YWRlczpNaW1lVHlwZT5hcHBsaWNhdGlvbi9wZGY8L3hhZGVzOk1pbWVUeXBlPjwveGFkZXM6RGF0YU9iamVjdEZvcm1hdD48eGFkZXM6RGF0YU9iamVjdEZvcm1hdCBPYmplY3RSZWZlcmVuY2U9IiNyLWlkLWQxMGZmNTdjM2U3YTYyMDMxNzBmNjZkODk1MTA3YmVjLTIiPjx4YWRlczpNaW1lVHlwZT50ZXh0L3BsYWluPC94YWRlczpNaW1lVHlwZT48L3hhZGVzOkRhdGFPYmplY3RGb3JtYXQ+PC94YWRlczpTaWduZWREYXRhT2JqZWN0UHJvcGVydGllcz48L3hhZGVzOlNpZ25lZFByb3BlcnRpZXM+PC94YWRlczpRdWFsaWZ5aW5nUHJvcGVydGllcz48L2RzOk9iamVjdD48L2RzOlNpZ25hdHVyZT4=";

        // TODO check permissions.
        $signatureContainer = SignatureContainer::find($request->fileid);

        $rootSignature               = new \DOMDocument('1.0', 'utf-8');
        $rootSignature->formatOutput = true;
        $rootSignature->loadXML('<asic:XAdESSignatures xmlns:asic="http://uri.etsi.org/02918/v1.2.1#" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"></asic:XAdESSignatures>');

        $signature = new \DOMDocument();
        $signature->loadXML(base64_decode($signedFile));
        $node = $signature->firstChild;

        $node = $rootSignature->importNode($node, true);

        $rootSignature->documentElement->appendChild($node);

//
//        // TODO get actual container files to get digest.
//
////        $tempFile = tempnam(sys_get_temp_dir(), 'signature');
////        file_put_contents($tempFile, $content);
////        $mimeType = mime_content_type($tempFile);
//
//        $files   = [];
//        $files[] = [
//            'fileName'    => 'test.pdf',
//            'fileContent' => hash('sha256', Storage::get('/company1/1/test.pdf')),
//            'mimeType'    => 'application/pdf'
//        ];
//        $files[] = [
//            'fileName'    => 'test.txt',
//            'fileContent' => hash('sha256', Storage::get('/company1/1/test.txt')),
//            'mimeType'    => 'text/plain'
//        ];
//
//        $completeResponse = Http::post(env('EID_API_URL') . "/api/signatures/id-card/complete", [
//            'client_id'       => env('EID_CLIENT_ID'),
//            'secret'          => env('EID_SECRET'),
//            'doc_id'          => $request->doc_id,
//            'signature_value' => $request->signature_value,
//        ]);
//
//        if ($completeResponse->failed()) {
//            Log::error("Signature complete failed", $completeResponse->json());
//            return $completeResponse->body();
//        }
//
//        $downloadResponse = Http::post(env('EID_API_URL') . "/api/signatures/download-signed-file", [
//            'client_id' => env('EID_CLIENT_ID'),
//            'secret'    => env('EID_SECRET'),
//            'doc_id'    => $request->doc_id,
//        ]);
//
//        if ($downloadResponse->failed()) {
//            return $downloadResponse->body();
//        }

        // TODO take the signature XML and compile into the .asice container

//        return $downloadResponse->json()['signed_file_contents'];
        return "";
    }
}
