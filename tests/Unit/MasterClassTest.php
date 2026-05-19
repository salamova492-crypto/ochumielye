<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\CreativityType;
use App\Models\Enrollment;
use App\Models\MasterClass;
use App\Models\User;
use Tests\TestCase;

class MasterClassTest extends TestCase
{
    public function test_belongs_to_creativity_type(): void
    {
        $creativityType = CreativityType::factory()->create();
        $masterClass = MasterClass::factory()->for($creativityType)->create();

        $this->assertInstanceOf(CreativityType::class, $masterClass->creativityType);
        $this->assertTrue($masterClass->creativityType->is($creativityType));
    }

    public function test_belongs_to_leader(): void
    {
        $leader = User::factory()->leader()->create();
        $masterClass = MasterClass::factory()->for($leader, 'leader')->create();

        $this->assertInstanceOf(User::class, $masterClass->leader);
        $this->assertTrue($masterClass->leader->is($leader));
    }

    public function test_has_many_enrollments(): void
    {
        $masterClass = MasterClass::factory()->create();
        $enrollment = Enrollment::factory()->for($masterClass, 'masterClass')->create();

        $this->assertTrue($masterClass->enrollments->contains($enrollment));
        $this->assertCount(1, $masterClass->enrollments);
    }

    public function test_enrolled_count_returns_correct_number(): void
    {
        $masterClass = MasterClass::factory()->create();

        Enrollment::factory()->count(3)->for($masterClass, 'masterClass')->create();

        $this->assertEquals(3, $masterClass->enrolledCount());
    }

    public function test_free_spots_returns_correct_number(): void
    {
        $masterClass = MasterClass::factory()->create(['maxPeople' => 10]);

        Enrollment::factory()->count(4)->for($masterClass, 'masterClass')->create();

        $this->assertEquals(6, $masterClass->freeSpots());
    }

    public function test_is_full_returns_true_when_no_spots_left(): void
    {
        $masterClass = MasterClass::factory()->create(['maxPeople' => 2]);

        Enrollment::factory()->count(2)->for($masterClass, 'masterClass')->create();

        $this->assertTrue($masterClass->isFull());
    }

    public function test_is_full_returns_false_when_spots_available(): void
    {
        $masterClass = MasterClass::factory()->create(['maxPeople' => 10]);

        Enrollment::factory()->count(3)->for($masterClass, 'masterClass')->create();

        $this->assertFalse($masterClass->isFull());
    }

    public function test_is_enrolled_returns_true_for_enrolled_user(): void
    {
        $user = User::factory()->create();
        $masterClass = MasterClass::factory()->create();

        Enrollment::factory()->for($user)->for($masterClass, 'masterClass')->create();

        $this->assertTrue($masterClass->isEnrolled($user));
    }

    public function test_is_enrolled_returns_false_for_not_enrolled_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $masterClass = MasterClass::factory()->create();

        Enrollment::factory()->for($otherUser)->for($masterClass, 'masterClass')->create();

        $this->assertFalse($masterClass->isEnrolled($user));
    }

    public function test_fillable_fields_are_mass_assignable(): void
    {
        $data = [
            'creativity_type_id' => CreativityType::factory()->create()->id,
            'leader_id' => User::factory()->leader()->create()->id,
            'title' => 'Test Master Class',
            'description' => 'Test description',
            'date' => '2026-06-15',
            'time_slot' => '11:00-13:00',
            'maxPeople' => 15,
            'price' => 1500.00,
        ];

        $masterClass = MasterClass::create($data);

        $this->assertEquals('Test Master Class', $masterClass->title);
        $this->assertEquals(15, $masterClass->maxPeople);
        $this->assertEquals(1500.00, (float) $masterClass->price);
    }
}
