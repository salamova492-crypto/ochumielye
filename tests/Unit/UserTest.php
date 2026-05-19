<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\MasterClass;
use App\Models\Enrollment;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_leader_role_returns_true_for_is_leader(): void
    {
        $user = User::factory()->leader()->create();

        $this->assertTrue($user->isLeader());
        $this->assertFalse($user->isVisitor());
    }

    public function test_visitor_role_returns_true_for_is_visitor(): void
    {
        $user = User::factory()->visitor()->create();

        $this->assertTrue($user->isVisitor());
        $this->assertFalse($user->isLeader());
    }

    public function test_leader_has_many_master_classes(): void
    {
        $leader = User::factory()->leader()->create();
        $masterClass = MasterClass::factory()->for($leader, 'leader')->create();

        $this->assertTrue($leader->masterClasses->contains($masterClass));
        $this->assertCount(1, $leader->masterClasses);
    }

    public function test_user_has_many_enrollments(): void
    {
        $visitor = User::factory()->visitor()->create();
        $masterClass = MasterClass::factory()->create();
        $enrollment = Enrollment::factory()->for($visitor)->for($masterClass, 'masterClass')->create();

        $this->assertTrue($visitor->enrollments->contains($enrollment));
        $this->assertCount(1, $visitor->enrollments);
    }

    public function test_fillable_fields_are_mass_assignable(): void
    {
        $data = [
            'fullName' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+71234567890',
            'role' => 'visitor',
            'password' => 'hashed_password',
        ];

        $user = User::create($data);

        $this->assertEquals('Test User', $user->fullName);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('visitor', $user->role);
    }

    public function test_password_is_hidden_from_serialization(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret')]);

        $this->assertNotEquals('secret', $user->password);
        $this->assertStringContainsString('$2y$', $user->password);
    }
}
