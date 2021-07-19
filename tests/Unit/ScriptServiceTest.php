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
        $scripts = factory(EntitiesScript::class, 2)->state('head')->create([
            'status' => '1',
            'content' => 'content',
        ]);
        
        $content = "";
        foreach ($scripts as $script) {
            $content .= $script->content.PHP_EOL;
        }
        $get_script = Script::get_Script('head');
        $this->assertEquals($get_script, $content);
    }
}