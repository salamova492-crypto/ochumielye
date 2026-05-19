<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\MasterClass;
use App\Models\User;
use Tests\TestCase;

class EnrollmentFlowTest extends TestCase
{
    private User $visitor;
    private MasterClass $masterClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = $this->createVisitor();
        $this->masterClass = MasterClass::factory()->create(['maxPeople' => 10]);
    }

    public function test_guest_redirected_to_login_on_confirm(): void
    {
        $response = $this->get(route('enrollment.confirm', $this->masterClass->id));

        $response->assertRedirect(route('login'));
    }

    public function test_visitor_can_see_confirm_page(): void
    {
        $response = $this->actingAs($this->visitor)
            ->get(route('enrollment.confirm', $this->masterClass->id));

        $response->assertStatus(200);
        $response->assertSee('Подтверждение записи');
        $response->assertSee($this->masterClass->title);
    }

    public function test_visitor_can_enroll(): void
    {
        $response = $this->actingAs($this->visitor)
            ->post(route('enrollment.store', $this->masterClass->id));

        $response->assertRedirect(route('category.show', $this->masterClass->creativity_type_id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'master_class_id' => $this->masterClass->id,
            'user_id' => $this->visitor->id,
        ]);
    }

    public function test_visitor_cannot_enroll_twice(): void
    {
        Enrollment::create([
            'master_class_id' => $this->masterClass->id,
            'user_id' => $this->visitor->id,
        ]);

        $response = $this->actingAs($this->visitor)
            ->post(route('enrollment.store', $this->masterClass->id));

        $response->assertSessionHas('error');
    }

    public function test_visitor_cannot_enroll_in_full_class(): void
    {
        $fullClass = MasterClass::factory()->create(['maxPeople' => 1]);

        Enrollment::create([
            'master_class_id' => $fullClass->id,
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->visitor)
            ->post(route('enrollment.store', $fullClass->id));

        $response->assertSessionHas('error');
    }

    public function test_guest_redirected_to_login_on_store(): void
    {
        $response = $this->post(route('enrollment.store', $this->masterClass->id));

        $response->assertRedirect(route('login'));
    }

    public function test_leader_cannot_enroll_as_visitor(): void
    {
        $leader = $this->createLeader();

        $response = $this->actingAs($leader)
            ->post(route('enrollment.store', $this->masterClass->id));

        $this->assertFalse(
            Enrollment::where('master_class_id', $this->masterClass->id)
                ->where('user_id', $leader->id)
                ->exists()
        );
    }
}
