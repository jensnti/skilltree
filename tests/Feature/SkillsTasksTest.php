<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\SkilltreeFactory;

class SkillsTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_skill_can_have_tasks()
    {
        $user = $this->signInTeacher();
        $skilltree = SkilltreeFactory::withSkills(1)->create($user);

        $skilltree = factory('App\Skilltree')->create(['owner_id' => $user->id]);
        $skilltree->addSkill(['name' => 'Test skill']);

        $this->actingAs($skilltree->owner)
            ->post($skilltree->skills[0]->path() . '/tasks', [
                'body' => 'Test task'
            ]);

        $this->get($skilltree->path())
            ->assertSee('Test task');
    }

    /** @test */
    function a_task_requires_a_body()
    {
        $user = $this->signInTeacher();
        $skilltree = SkilltreeFactory::withSkills(1)->create($user);

        $skilltree = factory('App\Skilltree')->create(['owner_id' => $user->id]);
        $skill = $skilltree->addSkill(['name' => 'Test skill']);

        $attributes = factory('App\Task')->raw(['body' => '']);
        $this->actingAs($skilltree->owner)
            ->post($skilltree->path() . '/skills/' . $skilltree->skills->first()->id . '/tasks', $attributes)
            ->assertSessionHasErrors('body');
    }

    /** @test */
    function only_the_owner_of_a_skilltree_may_add_tasks_to_a_skill()
    {
        $this->signIn();
        $skilltree = SkilltreeFactory::withSkills(1)->create();
        $this->post($skilltree->path() . '/skills/' . $skilltree->skills->first()->id . '/tasks', ['body' => 'Fails'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Fails']);
    }
}
