<?php

namespace Tests\Feature\Controllers;

use App\Genre;
use App\Contracts\GenreRepositoryInterface;

class GenreControllerTest extends ControllerTestCase
{
    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->genre = resolve(GenreRepositoryInterface::class);
    }

    protected function getExactJson(Genre $genre)
    {
        return [
            'id' => $genre->id,
            'name' => $genre->name,
            'approved_at' => null,
            'approver_id' => null,
            'created_at' => $genre->created_at,
            'updated_at' => $genre->updated_at,
        ];
    }

    /**
     * Ensure that a request for the index returns OK HTTP status and the
     * expected JSON string.
     */
    public function testAllReturnsOkStatusAndExpectedJsonStructure()
    {
        $genre = $this->factory($this->genre)->create();

        $this->json('GET', route('genres.index'))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [$this->getExactJson($genre)]
            ]);
    }

    /**
     * Ensure that a request for an existing record returns OK HTTP status and
     * the expected JSON string.
     */
    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $genre = $this->factory($this->genre)->create();

        $this->json('GET', route('genres.show', ['id' => $genre->id]))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => $this->getExactJson($genre)
            ]);
    }

    /**
     * Ensure that Create requests when the user is not authorized result in
     * Forbidden HTTP status.
     */
    public function testStoreWhenNotAuthorizedReturnsUnauthorizedStatus()
    {
        $this->json('POST', route('genres.store'))
            ->assertStatus(403);
    }

    /**
     * Ensure that Delete requests when the user is not authorized result in
     * Forbidden HTTP status.
     */
    public function testDeleteWhenNotAuthorizedReturnsUnauthorizedStatus()
    {
        $this->factory($this->genre)->create();

        $this->json('DELETE', route('genres.destroy', ['id' => 1]))
            ->assertStatus(403);
    }
}
