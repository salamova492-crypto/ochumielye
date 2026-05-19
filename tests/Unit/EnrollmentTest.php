<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Enrollment;
use App\Models\MasterClass;
use App\Models\User;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    public function test_belongs_to_master_class(): void
    {
        $masterClass = MasterClass::factory()->create();
        $enrollment = Enrollment::factory()->for($masterClass, 'masterClass')->create();

        $this->assertInstanceOf(MasterClass::class, $enrollment->masterClass);
        $this->assertTrue($enrollment->masterClass->is($masterClass));
    }

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $enrollment->user);
        $this->assertTrue($enrollment->user->is($user));
    }

    public function test_fillable_fields_are_mass_assignable(): void
    {
        $masterClass = MasterClass::factory()->create();
        $user = User::factory()->create();

        $enrollment = Enrollment::create([
            'master_class_id' => $masterClass->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals($masterClass->id, $enrollment->master_class_id);
        $this->assertEquals($user->id, $enrollment->user_id);
    }
}
