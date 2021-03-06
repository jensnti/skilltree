<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Skill;
use App\Skilltree;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_belongs_to_a_skilltree()
    {
        $skill = factory(Skill::class)->create();
        $this->assertInstanceOf(Skilltree::class, $skill->skilltree);
    }

    /** @test */
    function it_has_a_path()
    {
        $skill = factory(Skill::class)->create();
        $this->assertEquals('/skilltrees/' . $skill->skilltree->id . '/skills/' . $skill->id, $skill->path());
    }

    /** @test **/
    function it_can_add_a_task()
    {
        //        $this->signIn();
        //$this->withoutExceptionHandling();
        $skilltree = factory('App\Skilltree')->create();
        $skill = $skilltree->addSkill(['name' => 'Test skill']);
        $task = $skill->addtask(['skill_id' => $skill->id, 'body' => 'Test task']);

        $this->assertCount(1, $skill->tasks);
        $this->assertTrue($skill->tasks->contains($task));
    }

    /** @test **/
    /*    
    Removed for now can't add multiple tasks

    function it_can_add_many_tasks()
    {
        $skilltree = factory('App\Skilltree')->create();
        $skill = $skilltree->addSkill(['skill_title' => 'Test skill']);
        $tasks = [
            ['body' => 'task 1'],
            ['body' => 'task 2']
        ];
        $skill->addtasks($tasks);

        $this->assertCount(2, $skill->tasks);
        //$this->assertTrue($skill->tasks->contains($tasks));
    }
    */
}
