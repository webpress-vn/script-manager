<?php

namespace VCComponent\Laravel\Script\Test\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use VCComponent\Laravel\Script\Entities\Script as EntitiesScript;
use VCComponent\Laravel\Script\Facades\Script;
use VCComponent\Laravel\Script\Test\TestCase;

class ScriptServiceTest extends TestCase {
    use RefreshDatabase;
    
    /** @test */
    public function can_get_script() {
        $script = factory(EntitiesScript::class)->state('head')->create();

        $get_script = Script::get_Script('head');
        
        $this->assertEquals($script['content'], $get_script);
    }
}