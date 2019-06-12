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
    function a_user_can_create_a_skilltree()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        $this->get('/skilltrees/create')->assertStatus(200);
        $this->followingRedirects()
            ->post('/skilltrees', $attributes = factory(Skilltree::class)->raw())
            ->assertSee($attributes['title'])
            ->assertSee(str_limit($attributes['description'], 150));
    }

    /** @test */
    function a_user_can_view_their_skilltree()
    {
        $skilltree = SkilltreeFactory::create();

        $this->actingAs($skilltree->owner)
            ->get($skilltree->path())
            ->assertSee($skilltree->title)
            ->assertSee(str_limit($skilltree->description, 150));
    }

    /**  @test */
    function an_authenticated_user_cannot_view_the_skilltrees_of_others()
    {
        $this->signIn();
        $skilltree = SkilltreeFactory::create();
        $this->get($skilltree->path())->assertStatus(403);
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
        $skilltree = factory('App\Skilltree')->create();
        $this->get($skilltree->path())->assertRedirect('/login');
        $this->get('/skilltrees')->assertRedirect('/login');
        $this->get('/skilltrees/create')->assertRedirect('/login');
        $this->get($skilltree->path() . '/edit')->assertRedirect('/login');
        $this->post('/skilltrees', $skilltree->toArray())->assertRedirect('/login');
    }

    /** @test */
    function a_skilltree_requires_a_title()
    {
        $this->signIn();
        $attributes = factory('App\Skilltree')->raw(['title' => '']);
        $this->post('/skilltrees', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    function a_skilltree_requires_a_description()
    {
        $this->signIn();
        $attributes = factory('App\Skilltree')->raw(['description' => '']);
        $this->post('/skilltrees', $attributes)->assertSessionHasErrors('description');
    }
}