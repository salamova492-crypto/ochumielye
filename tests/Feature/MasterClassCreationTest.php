<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CreativityType;
use App\Models\MasterClass;
use App\Models\User;
use Tests\TestCase;

class MasterClassCreationTest extends TestCase
{
    private User $leader;
    private CreativityType $creativityType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leader = $this->createLeader();
        $this->creativityType = CreativityType::factory()->create();
    }

    public function test_leader_can_access_create_form(): void
    {
        $response = $this->actingAs($this->leader)
            ->get(route('cabinet.master-class.create'));

        $response->assertStatus(200);
        $response->assertSee('Форма добавления мастер-класса');
    }

    public function test_visitor_cannot_access_create_form(): void
    {
        $visitor = $this->createVisitor();

        $response = $this->actingAs($visitor)
            ->get(route('cabinet.master-class.create'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_create_form(): void
    {
        $response = $this->get(route('cabinet.master-class.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_leader_can_create_master_class(): void
    {
        $masterClassData = [
            'creativity_type_id' => $this->creativityType->id,
            'title' => 'Test Master Class',
            'description' => 'Test description',
            'date' => now()->addDays(10)->format('Y-m-d'),
            'time_slot' => '09:00-11:00',
            'maxPeople' => 10,
            'price' => 1500.00,
        ];

        $response = $this->actingAs($this->leader)
            ->post(route('cabinet.master-class.store'), $masterClassData);

        $response->assertRedirect(route('cabinet.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('master_classes', [
            'title' => 'Test Master Class',
            'leader_id' => $this->leader->id,
        ]);
    }

    public function test_leader_cannot_create_duplicate_time_slot(): void
    {
        $date = now()->addDays(5)->format('Y-m-d');

        MasterClass::factory()->create([
            'leader_id' => $this->leader->id,
            'date' => $date,
            'time_slot' => '09:00-11:00',
        ]);

        $response = $this->actingAs($this->leader)
            ->post(route('cabinet.master-class.store'), [
                'creativity_type_id' => $this->creativityType->id,
                'title' => 'Second Class',
                'description' => 'Description',
                'date' => $date,
                'time_slot' => '09:00-11:00',
                'maxPeople' => 10,
                'price' => 1000,
            ]);

        $response->assertSessionHasErrors('time_slot');
    }

    public function test_different_leaders_can_use_same_time_slot(): void
    {
        $date = now()->addDays(3)->format('Y-m-d');
        $otherLeader = $this->createLeader();

        MasterClass::factory()->create([
            'leader_id' => $otherLeader->id,
            'date' => $date,
            'time_slot' => '09:00-11:00',
        ]);

        $response = $this->actingAs($this->leader)
            ->post(route('cabinet.master-class.store'), [
                'creativity_type_id' => $this->creativityType->id,
                'title' => 'My Class',
                'description' => 'Description',
                'date' => $date,
                'time_slot' => '09:00-11:00',
                'maxPeople' => 10,
                'price' => 1000,
            ]);

        $response->assertRedirect(route('cabinet.index'));
        $this->assertDatabaseHas('master_classes', [
            'title' => 'My Class',
            'leader_id' => $this->leader->id,
            'date' => $date,
            'time_slot' => '09:00-11:00',
        ]);
    }

    public function test_create_validates_required_fields(): void
    {
        $response = $this->actingAs($this->leader)
            ->post(route('cabinet.master-class.store'), []);

        $response->assertSessionHasErrors([
            'creativity_type_id', 'title', 'description', 'date', 'time_slot', 'maxPeople', 'price',
        ]);
    }

    public function test_get_available_slots_returns_only_own_slots(): void
    {
        $date = now()->addDays(7)->format('Y-m-d');
        $otherLeader = $this->createLeader();

        MasterClass::factory()->create([
            'leader_id' => $otherLeader->id,
            'date' => $date,
            'time_slot' => '09:00-11:00',
        ]);

        MasterClass::factory()->create([
            'leader_id' => $this->leader->id,
            'date' => $date,
            'time_slot' => '11:00-13:00',
        ]);

        $response = $this->actingAs($this->leader)
            ->get(route('cabinet.master-class.available-slots', ['date' => $date]));

        $response->assertJson([
            'busySlots' => ['11:00-13:00'],
            'allBusy' => false,
        ]);

        $response->assertJsonMissing(['busySlots' => ['09:00-11:00']]);
    }

    public function test_leader_can_edit_own_master_class(): void
    {
        $masterClass = MasterClass::factory()->create([
            'leader_id' => $this->leader->id,
        ]);

        $response = $this->actingAs($this->leader)
            ->get(route('cabinet.master-class.edit', $masterClass->id));

        $response->assertStatus(200);
        $response->assertSee('Редактирование мастер-класса');
    }

    public function test_leader_cannot_edit_others_master_class(): void
    {
        $otherLeader = $this->createLeader();
        $masterClass = MasterClass::factory()->create([
            'leader_id' => $otherLeader->id,
        ]);

        $response = $this->actingAs($this->leader)
            ->get(route('cabinet.master-class.edit', $masterClass->id));

        $response->assertStatus(403);
    }

    public function test_leader_can_update_own_master_class(): void
    {
        $masterClass = MasterClass::factory()->create([
            'leader_id' => $this->leader->id,
        ]);

        $response = $this->actingAs($this->leader)
            ->put(route('cabinet.master-class.update', $masterClass->id), [
                'description' => 'Updated description',
                'price' => 2000,
            ]);

        $response->assertRedirect(route('cabinet.index'));

        $this->assertDatabaseHas('master_classes', [
            'id' => $masterClass->id,
            'description' => 'Updated description',
            'price' => 2000.00,
        ]);
    }
}
