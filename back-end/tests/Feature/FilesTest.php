<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilesTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateSignatureSignature()
    {
        Storage::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Company $company */
        $company = Company::factory()->create();
        $company->users()->attach($user->id, ['role' => 'ADMIN']);

        $containerResponse = $this->post("/api/container", [
            'files' => [
                [
                    'name'    => 'test.txt',
                    'content' => 'SGVsbG8gd29ybGQK',
                    'mime'    => 'text/plain',
                ],
                [
                    'name'    => 'small.pdf',
                    'content' => 'JVBERi0xLjQKJcOiw6PDj8OTCjIgMCBvYmoKPDwvTGVuZ3RoIDczID4+c3RyZWFtCkJUCjM2IDgwNiBUZAowIC0xOCBUZAovRjEgMTIgVGYKKEhlbGxvIFdvcmxkKVRqCjAgMCBUZApFVApRCmVuZHN0cmVhbQplbmRvYmoKNCAwIG9iago8PC9QYXJlbnQgMyAwIFIvQ29udGVudHMgMiAwIFIvVHlwZS9QYWdlL1Jlc291cmNlczw8L1Byb2NTZXQgWy9QREYgL1RleHQgL0ltYWdlQgovSW1hZ2VDIC9JbWFnZUldL0ZvbnQ8PC9GMSAxIDAgUj4+Pj4vTWVkaWFCb3hbMCAwIDU5NSA4NDJdPj4KZW5kb2JqCjEgMCBvYmoKPDwvQmFzZUZvbnQvSGVsdmV0aWNhL1R5cGUvRm9udC9FbmNvZGluZy9XaW5BbnNpRW5jb2RpbmcvU3VidHlwZS9UeXBlMT4+CmVuZG9iagozIDAgb2JqCjw8L0lUWFQoNS4zLjApL1R5cGUvUGFnZXMvQ291bnQgMS9LaWRzWzQgMCBSXT4+CmVuZG9iago1IDAgb2JqCjw8L1R5cGUvQ2F0YWxvZy9QYWdlcyAzIDAgUj4+CmVuZG9iago2IDAgb2JqCjw8L1Byb2R1Y2VyKGlUZXh0wq4gNS4zLjAgwqkyMDAwLTIwMTIgMVQzWFQKQlZCQSkvTW9kRGF0ZShEOjIwMTIwNjEzMTAyNzI1KzAyJzAwJykvQ3JlYXRpb25EYXRlKEQ6MjAxMjA2MTMxMDI3MjUrMDInMDAnKT4+CmVuZG9iagp4cmVmCjAgNwowMDAwMDAwMDAwIDY1NTM1IGYKMDAwMDAwMDMxMSAwMDAwMCBuCjAwMDAwMDAwMTUgMDAwMDAgbgowMDAwMDAwMzk5IDAwMDAwIG4KMDAwMDAwMDE1NCAwMDAwMCBuCjAwMDAwMDA0NjIgMDAwMDAgbgowMDAwMDAwNTA3IDAwMDAwIG4KdHJhaWxlcgo8PC9Sb290IDUgMCBSL0lECls8MGY2YmI2NTFjMDQ4MDIxM2ZiYmQxMzQ0OWI0MGZlOGY+PGU3N2ZiM2MzYzY0YzMwZWEyYTkwOGNkMTgxYzVmNTAwPl0vSW5mbyA2IDAKUi9TaXplIDc+PgpzdGFydHhyZWYKNjQzCiUlRU9GCg==',
                    'mime'    => 'application/pdf',
                ],
            ]
        ]);

        $containerResponse->assertStatus(200);

        $containerResponse->assertJson([
            'id'    => 1,
            'files' => [
                ['id' => 1, 'name' => 'test.txt'],
                ['id' => 2, 'name' => 'small.pdf'],
            ],
        ]);

        $containerResponse = $this->post("/api/finish-signature", [
            'fileid'    => $containerResponse->json()['id'],
            'signature' => '',
            'sign-type' => 'id-card'
        ]);
    }
}
