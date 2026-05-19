<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\CreativityType;
use App\Models\MasterClass;
use Tests\TestCase;

class CreativityTypeTest extends TestCase
{
    public function test_has_many_master_classes(): void
    {
        $creativityType = CreativityType::factory()->create();
        $masterClass = MasterClass::factory()->for($creativityType)->create();

        $this->assertTrue($creativityType->masterClasses->contains($masterClass));
        $this->assertCount(1, $creativityType->masterClasses);
    }

    public function test_fillable_fields_are_mass_assignable(): void
    {
        $data = [
            'name' => 'Test Type',
            'description' => 'Test description',
            'image' => 'test.jpg',
        ];

        $type = CreativityType::create($data);

        $this->assertEquals('Test Type', $type->name);
        $this->assertEquals('test.jpg', $type->image);
    }
}
