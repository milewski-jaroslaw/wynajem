<?php

namespace Tests\Unit\Repository;

use App\Models\Estate;
use App\Models\User;
use App\Repositories\EstateRepository;
use DB;
use Tests\TestCase;

class EstateRepositoryTest extends TestCase
{
    private EstateRepository $estateRepository;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('users_shifts')->truncate();
        DB::table('estates')->truncate();

        $this->estateRepository = new EstateRepository();
    }

    /** @test */
    public function it_can_get_all_estates_by_user_id(): void
    {
        Estate::factory()->count(5)->create();
        $userId = User::factory()->create()->getKey();
        Estate::factory()->create([
            'supervisor_user_id' => $userId,
        ]);

        $result = $this->estateRepository->getAllByUserId($userId)->get();

        $this->assertCount(1, $result);
        $this->assertEquals($userId, $result->first()->supervisor_user_id);
    }
}
