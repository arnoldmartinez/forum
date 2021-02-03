<?php

namespace Tests\Unit;

use App\Spam;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SpamTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_validate_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));
    }
}