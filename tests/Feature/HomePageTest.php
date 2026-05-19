<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CreativityType;
use App\Models\MasterClass;
use App\Models\User;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('ОчУмелые ручки');
    }

    public function test_home_page_shows_creativity_types(): void
    {
        CreativityType::factory()->count(3)->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function test_category_page_shows_master_classes(): void
    {
        $creativityType = CreativityType::factory()->create();
        MasterClass::factory()->count(2)->for($creativityType)->create();

        $response = $this->get(route('category.show', $creativityType->id));

        $response->assertStatus(200);
        $response->assertSee($creativityType->name);
    }
}
