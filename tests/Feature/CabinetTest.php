<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MasterClass;
use App\Models\User;
use Tests\TestCase;

class CabinetTest extends TestCase
{
    private User $leader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leader = $this->createLeader();
    }

    public function test_leader_can_access_cabinet(): void
    {
        $response = $this->actingAs($this->leader)
            ->get(route('cabinet.index'));

        $response->assertStatus(200);
        $response->assertSee('Мои мастер-классы');
    }

    public function test_visitor_cannot_access_cabinet(): void
    {
        $visitor = $this->createVisitor();

        $response = $this->actingAs($visitor)
            ->get(route('cabinet.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_cabinet(): void
    {
        $response = $this->get(route('cabinet.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_cabinet_shows_only_own_master_classes(): void
    {
        $otherLeader = $this->createLeader();

        $ownClass = MasterClass::factory()->create(['leader_id' => $this->leader->id]);
        MasterClass::factory()->create(['leader_id' => $otherLeader->id]);

        $response = $this->actingAs($this->leader)
            ->get(route('cabinet.index'));

        $response->assertSee($ownClass->title);
        $this->assertStringNotContainsString(
            MasterClass::where('leader_id', $otherLeader->id)->first()->title,
            $response->getContent()
        );
    }

    public function test_cabinet_shows_enrolled_students(): void
    {
        $masterClass = MasterClass::factory()->create(['leader_id' => $this->leader->id]);
        $visitor = $this->createVisitor();

        $this->post(route('enrollment.store', $masterClass->id), [], [
            'X-CSRF-TOKEN' => csrf_token(),
        ]);

        $response = $this->actingAs($this->leader)
            ->get(route('cabinet.index'));

        $response->assertStatus(200);
    }
}
