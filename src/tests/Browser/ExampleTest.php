<?php
// tests/Browser/ExampleTest.php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /** @test */
    public function basic_example()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
        });
    }
}
