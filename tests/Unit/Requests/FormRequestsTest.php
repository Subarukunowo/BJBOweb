<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Laporan\StoreLaporanRequest;
use App\Http\Requests\Postingan\StorePostinganRequest;
use App\Http\Requests\Postingan\UpdatePostinganRequest;
use App\Http\Requests\Ulasan\StoreUlasanRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FormRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_request_rules(): void
    {
        $request = new LoginRequest;
        $this->assertTrue($request->authorize());

        $validator = Validator::make([], $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_rules(): void
    {
        $request = new RegisterRequest;
        $this->assertTrue($request->authorize());

        $validator = Validator::make([], $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_store_postingan_request_rules(): void
    {
        $request = new StorePostinganRequest;
        $this->assertTrue($request->authorize());

        $validator = Validator::make([], $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
        $this->assertArrayHasKey('category', $validator->errors()->toArray());
    }

    public function test_update_postingan_request_rules(): void
    {
        $request = new UpdatePostinganRequest;
        $this->assertTrue($request->authorize());

        $validator = Validator::make([], $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
    }

    public function test_store_laporan_request_rules(): void
    {
        $request = new StoreLaporanRequest;
        $this->assertTrue($request->authorize());

        $validator = Validator::make([], $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('jenis_laporan', $validator->errors()->toArray());
        $this->assertArrayHasKey('teks', $validator->errors()->toArray());
    }

    public function test_store_ulasan_request_rules(): void
    {
        $request = new StoreUlasanRequest;
        $this->assertTrue($request->authorize());

        $validator = Validator::make([], $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rating', $validator->errors()->toArray());
        $this->assertArrayHasKey('komentar', $validator->errors()->toArray());
    }

    public function test_update_profile_request_rules(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $request = new UpdateProfileRequest;
        $this->assertTrue($request->authorize());

        $validator = Validator::make([], $request->rules(), $request->messages());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
}
