<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Facades\Tests\Setup\SkilltreeFactory;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
     function a_user_has_skilltrees()
    {
        $user = factory('App\User')->create();
        $this->assertInstanceOf(Collection::class, $user->skilltrees);
    }

    /** @test **/
    public function a_user_has_accessible_skilltrees()
    {
        $this->withoutExceptionHandling();

        $john = $this->signIn();

        SkilltreeFactory::ownedBy($john)->create();

        $this->assertCount(1, $john->accessibleSkilltrees());

        $sally = factory(User::class)->create();
        $nick = factory(User::class)->create();

        $skilltree = tap(SkilltreeFactory::ownedBy($sally)->create())->invite($nick);

        $this->assertCount(1, $john->accessibleSkilltrees());

        $skilltree->invite($john);

        $this->assertCount(2, $john->accessibleSkilltrees());
    }
}
