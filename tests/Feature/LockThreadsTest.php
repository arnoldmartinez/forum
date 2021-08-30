<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LockThreads extends TestCase {

    use DatabaseMigrations;

    /** @test */
    public function an_administrator_can_lock_any_thread()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $thread->lock();

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
