<?php 

namespace VCComponent\Laravel\Script\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Script\Entities\Script;
use VCComponent\Laravel\Script\Test\TestCase;

class ScriptControllerTest extends TestCase {
    use RefreshDatabase;

    /** @test */
    public function can_get_script_list_by_admin_router()
    {
        $scripts = factory(Script::class, 5)->create();

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $listIds = array_column($scripts, 'id');
        array_multisort($listIds, SORT_DESC, $scripts);

        $response = $this->call('GET', 'api/admin/scripts/list');

        $response->assertStatus(200);

        foreach ($scripts as $item) {
            $this->assertDatabaseHas('scripts', $item);
        }
    }

    /** @test */
    public function can_get_scripts_by_admin_router()
    {
        $scripts = factory(Script::class, 20)->create();

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $listIds = array_column($scripts, 'id');
        array_multisort($listIds, SORT_DESC, $scripts);

        $response = $this->call('GET', 'api/admin/scripts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        foreach ($scripts as $item) {
            $this->assertDatabaseHas('scripts', $item);
        }
    }
    
    public function can_get_scrifpt_list_by_admin_router()
    {
        
        $this->assertTrue(true);
    }

    /** @test */
    public function can_get_script_item_by_admin() {
        $script = factory(Script::class)->create();

        $response = $this->call('GET', 'api/admin/scripts/'.$script->id);
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'title' => $script['title'],
                'position' => $script['position'],
                'content' => $script['content'],
            ]
        ]);
    }

    /** @test */
    public function can_creat_script_by_admin() {
        $data = factory(Script::class)->make()->toArray();

        $response = $this->json('POST', 'api/admin/scripts', $data);

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'title' => $data['title'],
                'position' => $data['position'],
                'content' => $data['content'],
            ]
        ]);

        $this->assertDatabaseHas('scripts', $data);
    }

    /** @test */
    public function can_update_script_by_admin() {
        $script = factory(Script::class)->create();

        unset($script['created_at']);
        unset($script['updated_at']);

        $id = $script->id;
        $script->title = "updated content";
        $script->position = "updated position";
        $script->content = "updated contetn";

        $data = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/'.$id, $data);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $data['title'],
                'position' => $data['position'],
                'content' => $data['content'],
            ]
        ]);
    }

    /** @test */
    public function can_delete_script_by_admin() {
        $script = factory(Script::class)->create();

        $script = $script->toArray();

        unset($script['created_at']);
        unset($script['updated_at']);

        $this->assertDatabaseHas('scripts', $script);

        $response = $this->call('DELETE', 'api/admin/scripts/'.$script['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDeleted('scripts', $script);
    }

    /** @test */
    public function can_update_script_status_by_admin() {
        $script = factory(Script::class)->create();
        $script->status = 2;
        unset($script['created_at']);
        unset($script['updated_at']);
        $script = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/status/'.$script['id'], ['status' => $script['status'], 'title' => 'new title']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('scripts', $script);
    }

    /** @test */
    public function can_update_script_position_by_admin() {
        $script = factory(Script::class)->create();
        $script->position = "New position";
        unset($script['created_at']);
        unset($script['updated_at']);
        $script = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/position/'.$script['id'], ['position' => $script['position'], 'title' => 'new title', 'status' => 'new status']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('scripts', $script);
    }

    /** @test */
    public function can_bulk_update_script_status_by_admin() {
        $scripts = factory(Script::class, 5)->create();
        $scripts = $scripts->map(function ($item) {
            unset($item['created_at']);
            unset($item['updated_at']);
            $item['status'] = 2;
            return $item;
        })->toArray();

        $listIds = array_column($scripts, 'id');

        $response = $this->json('PUT', 'api/admin/scripts/status/bulk', ["id" => $listIds, "status" => 2]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($scripts as $script) {
            $this->assertDatabaseHas('scripts', $script);
        }
    }

}