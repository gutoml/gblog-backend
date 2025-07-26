<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use App\Services\ImageStoreService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->user = User::factory()->create();
    }

    public function test_it_can_list_images()
    {
        $this->actingAs($this->user);

        Image::factory()->count(25)->create();

        $response = $this->getJson(route('images.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'url',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active'
                    ]
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ])
            ->assertJsonCount(20, 'data');
    }

    public function test_pagination_returns_correct_page()
    {
        $this->actingAs($this->user);
        Image::factory()->count(30)->create();

        $response = $this->getJson(route('images.index', ['page' => 1]));
        $response->assertJsonCount(20, 'data');

        $response = $this->getJson(route('images.index', ['page' => 2]));
        $response->assertJsonCount(10, 'data');
    }

    public function test_can_change_per_page()
    {
        $this->actingAs($this->user);
        Image::factory()->count(15)->create();

        $response = $this->getJson(route('images.index', ['per_page' => 10]));

        $response->assertJson([
            'per_page' => 10,
            'last_page' => 2
        ])->assertJsonCount(10, 'data');
    }

    public function test_it_can_show_single_image()
    {
        $this->actingAs($this->user);

        $image = Image::factory()->create();

        $response = $this->getJson(route('images.show', $image->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'url', 'created_at', 'updated_at'])
            ->assertJson([
                'id' => $image->id,
                'name' => $image->name,
                'url' => $image->url,
            ]);
    }

    public function test_it_can_delete_an_image()
    {
        $this->actingAs($this->user);

        $image = Image::factory()->create();

        $response = $this->deleteJson(route('images.destroy', $image->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('images', [
            'id' => $image->id,
        ]);
    }

    public function test_it_returns_error_for_invalid_images()
    {
        $this->actingAs($this->user);

        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        $response = $this->postJson(route('images.store'), [
            'images' => [$invalidFile]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0']);

        $largeFile = UploadedFile::fake()->image('large.jpg')->size(6 * 1024); // 6MB
        $response = $this->postJson(route('images.store'), [
            'images' => [$largeFile]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0']);
    }

    public function test_it_handles_database_failure_during_upload()
    {
        $this->actingAs($this->user);

        $validImage = UploadedFile::fake()->image('valid.jpg', 800, 600)->size(1500);

        // Mock do service
        $mockService = Mockery::mock(ImageStoreService::class);
        $mockService->shouldReceive('execute')
            ->with($validImage)
            ->andReturn([
                'name' => 'valid.jpg',
                'url' => 'images/valid.jpg'
            ]);
        $this->app->instance(ImageStoreService::class, $mockService);

        // Mock da model de imagem
        $mockImage = Mockery::mock(Image::class);
        $mockImage->shouldReceive('create')
            ->with([
                'name' => 'valid.jpg',
                'url' => 'images/valid.jpg'
            ])
            ->andReturn(false);
        $this->app->instance(Image::class, $mockImage);

        $response = $this->postJson(route('images.store'), [
            'images' => [$validImage]
        ]);

        $response->assertStatus(500);

        Storage::disk('public')->assertMissing('images/valid.jpg');
    }

    public function test_it_returns_404_for_nonexistent_image()
    {
        $this->actingAs($this->user);

        // Garante que o banco está vazio
        Image::query()->delete();

        // 1. Teste para show()
        $response = $this->getJson(route('images.show', 1)); // Primeiro ID que não existe

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message'
            ]);

        // 2. Teste para destroy()
        $response = $this->deleteJson(route('images.destroy', 1));

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message'
            ]);

        // 3. Teste com ID inválido (string)
        $response = $this->getJson(route('images.show', 'invalid-id'));
        $response->assertStatus(404);
    }

    public function test_it_forbids_upload_to_unauthenticated_users()
    {
        // 1. Teste sem nenhuma autenticação
        $image = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson(route('images.store'), [
            'images' => [$image]
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.' // Mensagem padrão do Laravel
            ]);

        // 2. Teste com token inválido (opcional)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token'
        ])->postJson(route('images.store'), [
            'images' => [$image]
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_it_can_upload_single_image()
    {
        $this->actingAs($this->user);

        Storage::fake('public');

        // Cria uma imagem fake que atende todas as validações
        $image = UploadedFile::fake()->image('profile.jpg', 800, 600)
            ->size(1500) // 1.5KB
            ->mimeType('image/jpeg');

        $response = $this->postJson(route('images.store'), [
            'images' => [$image]
        ]);

        // Verifica a resposta
        $response->assertStatus(201)
            ->assertJsonStructure([[
                'id',
                'name',
                'url'
            ]]);

        // Verifica se a imagem foi armazenada
        $imageData = $response->json()[0];
        Storage::disk('public')->assertExists(
            str_replace('/storage/', '', $imageData['url'])
        );

        // Verifica se foi criado no banco de dados
        $this->assertDatabaseHas('images', [
            'id' => $imageData['id'],
            'name' => 'profile.jpg'
        ]);
    }

    public function test_it_can_upload_multiple_images()
    {
        $this->actingAs($this->user);

        Storage::fake('public');

        // Cria 3 imagens fakes válidas
        $images = [
            UploadedFile::fake()->image('image1.jpg', 800, 600)->size(1500),
            UploadedFile::fake()->image('image2.png', 800, 600)->size(1500),
            UploadedFile::fake()->image('image3.webp', 800, 600)->size(1500)
        ];

        $response = $this->postJson(route('images.store'), [
            'images' => $images
        ]);

        // Verifica a resposta
        $response->assertStatus(201)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'url']
            ]);

        // Verifica cada imagem individualmente
        foreach ($response->json() as $imageData) {
            Storage::disk('public')->assertExists(
                str_replace('/storage/', '', $imageData['url'])
            );

            $this->assertDatabaseHas('images', [
                'id' => $imageData['id'],
                'name' => $imageData['name']
            ]);
        }
    }

    public function test_it_returns_proper_response_after_upload()
    {
        $this->actingAs($this->user);
        Storage::fake('public');

        // Cria imagem de teste válida
        $image = UploadedFile::fake()->image('profile.jpg', 800, 600)
            ->size(2000); // 2KB

        $response = $this->postJson(route('images.store'), [
            'images' => [$image]
        ]);

        // Verifica status e estrutura básica
        $response->assertStatus(201)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'url',
                ]
            ]);

        // Verifica dados específicos na resposta
        $responseData = $response->json()[0];
        $this->assertEquals('profile.jpg', $responseData['name']);
        $this->assertNotNull($responseData['id']);

        // Verifica se a URL é acessível (opcional)
        if (config('filesystems.default') === 'public') {
            $this->assertEquals(200, $this->get($responseData['url'])->status());
        }
    }

    public function test_it_validates_image_upload()
    {
        $this->actingAs($this->user);

        // 1. Teste com arquivo inválido (não imagem)
        $invalidFile = UploadedFile::fake()->create('document.pdf', (6 * 1024));
        $response = $this->postJson(route('images.store'), [
            'images' => [$invalidFile]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0']);

        // 3. Teste com arquivo muito grande (>5MB)
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(6 * 1024); // 6MB
        $response = $this->postJson(route('images.store'), [
            'images' => [$largeFile]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0']);

        // 4. Teste sem arquivos
        $response = $this->postJson(route('images.store'), [
            'images' => []
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images']);
    }
}
