<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Skilltree;
use Facades\Tests\Setup\SkilltreeFactory;

class ManageSkilltreesTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    function a_teacher_can_create_a_skilltree()
    {
        //$this->withoutExceptionHandling();
        $this->signInTeacher();
        $this->get('/skilltrees/create')->assertStatus(200);
        $attributes = factory(Skilltree::class)->raw();

        $this->followingRedirects()
            ->post('/skilltrees', $attributes);
        // ->assertSee(str_limit($attributes['title'], 20)) Does not work since string limitation is done clientside with JS
        // ->assertSee(str_limit($attributes['description'], 50));
    }

    /** @test */
    function skills_can_be_included_as_part_of_a_new_skilltree_creation()
    {
        $this->signInTeacher();
        $attributes = factory(Skilltree::class)->raw();
        $attributes['skills'] = [
            ['name' => 'Skill 1'],
            ['name' => 'Skill 2']
        ];
        $this->post('/skilltrees', $attributes);

        $this->assertCount(2, Skilltree::first()->skills);
    }

    /** @test */
    function a_user_can_view_their_skilltree()
    {
        $skilltree = SkilltreeFactory::create();

        $this->actingAs($skilltree->owner)
            ->get($skilltree->path())
            ->assertSee($skilltree->title)
            ->assertSee($skilltree->description);
    }

    /** @test **/
    function a_teacher_can_delete_a_skilltree()
    {
        $user = $this->signInTeacher();

        $skilltree = factory('App\Skilltree')->create(['owner_id' => $user->id]);

        $this->actingAs($skilltree->owner)
            ->delete($skilltree->path())
            ->assertRedirect('/skilltrees');

        $this->assertDatabaseMissing('skilltrees', $skilltree->only('id'));
    }

    /** @test **/
    function a_teacher_can_update_a_skilltree()
    {
        $user = $this->signInTeacher();

        $skilltree = factory('App\Skilltree')->create(['owner_id' => $user->id]);

        $this->actingAs($skilltree->owner)
            ->patch($skilltree->path(), [
                'title' => 'changed',
                'description' => 'changed'
            ])
            ->assertRedirect($skilltree->path());

        $this->assertDatabaseHas('skilltrees', ['title' => 'changed', 'description' => 'changed']);
    }

    /**  @test */
    function an_authenticated_user_cannot_view_the_skilltrees_of_others()
    {
        $this->signIn();
        $skilltree = SkilltreeFactory::create();
        $this->get($skilltree->path())->assertStatus(403);
    }

    /** @test **/
    function an_authenticated_user_cannot_update_the_skilltrees_of_others()
    {
        $this->signIn();
        $skilltree = SkilltreeFactory::create();
        $this->patch($skilltree->path())->assertStatus(403);
    }

    /** @test */
    function only_authenticated_users_can_create_skilltrees()
    {
        $attributes = factory('App\Skilltree')->raw();
        $this->post('/skilltrees', $attributes)->assertRedirect('/login');
    }

    /** @test */
    function only_authenticated_users_can_view_skilltrees()
    {
        $this->get('/skilltrees')->assertRedirect('/login');
    }

    /** @test */
    function unauthorized_users_cannot_manage_skilltrees()
    {
        $skilltree = SkilltreeFactory::create();
        $this->get($skilltree->path())->assertRedirect('/login');
        $this->get('/skilltrees')->assertRedirect('/login');
        $this->get('/skilltrees/create')->assertRedirect('/login');
        $this->get($skilltree->path() . '/edit')->assertRedirect('/login');
        $this->post('/skilltrees', $skilltree->toArray())->assertRedirect('/login');
    }

    /** @test */
    function a_skilltree_requires_a_title()
    {
        $this->signInTeacher();
        $attributes = factory('App\Skilltree')->raw(['title' => '']);
        $this->post('/skilltrees', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    function a_skilltree_requires_a_description()
    {
        $this->signInTeacher();
        $attributes = factory('App\Skilltree')->raw(['description' => '']);
        $this->post('/skilltrees', $attributes)->assertSessionHasErrors('description');
    }

    /** @test **/
    function unauthorized_users_cannot_delete_skilltrees()
    {
        $skilltree = SkilltreeFactory::create();
        $this->delete($skilltree->path())
            ->assertRedirect('/login');

        $user = $this->signIn();
        $this->delete($skilltree->path())
            ->assertStatus(403);

        $skilltree->invite($user);
        $this->actingAs($user)->delete($skilltree->path())->assertStatus(403);
    }

    /** @test **/
    function a_user_can_see_all_skilltrees_they_have_been_invited_to_on_their_dashboard()
    {
        //$this->withoutExceptionHandling();
        $skilltree = tap(SkilltreeFactory::create())->invite($this->signInTeacher()); // tap ensures we get a return value

        $this->get('/skilltrees')->assertSee($skilltree->title);
    }

    /** @test */
    function a_user_can_retrieve_a_skilltrees_positions()
    {
        $this->withoutExceptionHandling();

        $this->signIn();
        $skilltree = SkilltreeFactory::create(); //->raw();

        $attributes['positions'] = "position: 0";

        $skilltree->storePositions($attributes);

        $this->actingAs($skilltree->owner)->get($skilltree->path() . '/pos')->assertSee("position: 0");
    }

    /** @test */
    function a_student_user_cannot_create_skilltrees()
    {
        $this->signInStudent();
        $this->get('/skilltrees/create')->assertStatus(403);

        $attributes = factory(Skilltree::class)->raw();
        $this->followingRedirects()
            ->post('/skilltrees', $attributes)
            ->assertStatus(403);

        $this->assertDatabaseMissing('skilltrees', $attributes);
    }

    /** @test **/
    function student_users_cannot_delete_skilltrees()
    {
        $this->signInStudent();
        $skilltree = SkilltreeFactory::create();
        $this->delete($skilltree->path())
            ->assertStatus(403);
    }
}
