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
    public function can_get_script_list_with_json_constraints() {
        $scripts = factory(Script::class, 5)->create();

        $constraints_title = $scripts[0]->title;
        $constraints_position = $scripts[0]->position;

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $constraints = '{"title":"'.$constraints_title.'", "position":"'.$constraints_position.'"}';
        $response = $this->call('GET', 'api/admin/scripts/list?constraints='.$constraints);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$scripts[0]]
        ]);
    }

    /** @test */
    public function can_get_script_list_with_search() {
        $scripts = factory(Script::class, 5)->create();

        $search_title = $scripts[0]->title;

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $response = $this->call('GET', 'api/admin/scripts/list?search='.$search_title);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$scripts[0]]
        ]);
    }

    /** @test */
    public function can_get_script_list_with_order_by() {
        $scripts = factory(Script::class, 5)->create();

        $search_title = $scripts[0]->title;

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $listTitle = array_column($scripts, 'title');
        array_multisort($listTitle, SORT_DESC, $scripts);

        $order_by = '{"title":"desc"}';

        $response = $this->call('GET', 'api/admin/scripts/list?order_by='.$order_by);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $scripts
        ]);
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

    /** @test */
    public function can_get_scripts_with_constraints() {
        $scripts = factory(Script::class, 20)->create();

        $constraints_title = $scripts[0]->title;
        $constraints_position = $scripts[0]->position;

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $constraints = '{"title":"'.$constraints_title.'", "position":"'.$constraints_position.'"}';

        $response = $this->call('GET', 'api/admin/scripts?constraints='.$constraints);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson([
            'data' => [$scripts[0]]
        ]);
    }

    /** @test */
    public function can_get_scripts_with_search()
    {
        $scripts = factory(Script::class, 5)->create();

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $response = $this->call('GET', 'api/admin/scripts?search='.$scripts[0]['title']);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson([
            'data' => [$scripts[0]]
        ]);
    }

    /** @test */
    public function can_get_scripts_with_order_by()
    {
        $scripts = factory(Script::class, 10)->create();

        $scripts = $scripts->map(function ($s) {
            unset($s['updated_at']);
            unset($s['created_at']);
            return $s;
        })->toArray();

        $order_by = '{"title":"desc"}';

        $listTitle = array_column($scripts, 'title');
        array_multisort($listTitle, SORT_DESC, $scripts);

        $response = $this->call('GET', 'api/admin/scripts?order_by='.$order_by);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson([
            'data' => $scripts,
        ]);
    }

    /** @test */
    public function can_get_scripts_with_status()
    {
        $scripts = factory(Script::class, 5)->create();

        $status = $scripts[0]->status;
        $scripts = $scripts->map(function ($s) use ($status) {
            unset($s['updated_at']);
            unset($s['created_at']);
            if($s['status'] === $status) {
                return $s;
            }
        });
        $scripts = $scripts->filter(function ($s) {
            return $s !== null;
        })->toArray();

        $listIds = array_column($scripts, 'id');
        array_multisort($listIds, SORT_DESC, $scripts);


        $response = $this->call('GET', 'api/admin/scripts?status='.$status);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson([
            'data' => $scripts
        ]);
    }

    /** @test */
    public function can_get_scripts_with_position()
    {
        $scripts = factory(Script::class, 10)->create();

        $position = $scripts[0]->position;

        $scripts = $scripts->map(function ($s) use ($position) {
            unset($s['updated_at']);
            unset($s['created_at']);
            if($s['position'] === $position) {
                return $s;
            }
        });
        $scripts = $scripts->filter(function ($s) {
            return $s !== null;
        })->toArray();

        $listIds = array_column($scripts, 'id');
        array_multisort($listIds, SORT_DESC, $scripts);

        $response = $this->call('GET', 'api/admin/scripts?position='.$position);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson([
            'data' => $scripts
        ]);
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
    public function should_not_get_undefined_script() {
        $response = $this->call('GET', 'api/admin/scripts/1');
        $response->assertStatus(400);

        $response->assertJson([
            'message' => 'script not found',
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
    public function should_not_create_script_without_title() {
        $data = factory(Script::class)->make()->toArray();
        unset($data['title']);
        $response = $this->json('POST', 'api/admin/scripts', $data);

        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "title" => [
                    "The title field is required."
                ]
            ],
        ]);
    }

    /** @test */
    public function should_not_create_script_without_position() {
        $data = factory(Script::class)->make()->toArray();
        unset($data['position']);
        $response = $this->json('POST', 'api/admin/scripts', $data);

        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "position" => [
                    "The position field is required."
                ]
            ],
        ]);
    }

    /** @test */
    public function should_not_create_script_without_content() {
        $data = factory(Script::class)->make()->toArray();
        unset($data['content']);
        $response = $this->json('POST', 'api/admin/scripts', $data);

        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "content" => [
                    "The content field is required."
                ]
            ],
        ]);
    }

    /** @test */
    public function should_not_create_script_with_existed_title() {
        $data = factory(Script::class)->create()->toArray();

        $existed_title = $data['title'];

        $data = factory(Script::class)->make()->toArray();
        $data['title'] = $existed_title;
        $response = $this->json('POST', 'api/admin/scripts', $data);

        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "title" => [
                    "The title has already been taken."
                ]
            ],
        ]);
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
    public function should_not_update_script_with_emty_title() {
        $script = factory(Script::class)->create();

        unset($script['created_at']);
        unset($script['updated_at']);

        $id = $script->id;
        $script->title = null;
        $script->position = "updated position";
        $script->content = "updated contetn";

        $data = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/'.$id, $data);

        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "title" => [
                    "The title field is required."
                ]
            ],
        ]);
    }

    /** @test */
    public function should_not_update_script_with_emty_position() {
        $script = factory(Script::class)->create();

        unset($script['created_at']);
        unset($script['updated_at']);

        $id = $script->id;
        $script->title = "updated postion";
        $script->position = null;
        $script->content = "updated contetn";

        $data = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/'.$id, $data);

        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "position" => [
                    "The position field is required."
                ]
            ],
        ]);
    }

    /** @test */
    public function should_not_update_script_with_emty_content() {
        $script = factory(Script::class)->create();

        unset($script['created_at']);
        unset($script['updated_at']);

        $id = $script->id;
        $script->title = "updated position";
        $script->position = "updated position";
        $script->content = null;

        $data = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/'.$id, $data);

        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "content" => [
                    "The content field is required."
                ]
            ],
        ]);
    }

    /** @test */
    public function can_update_script_with_existed_title() {
        $scripts = factory(Script::class, 2)->create();
        $script = $scripts[0];
        unset($script['created_at']);
        unset($script['updated_at']);

        $id = $script->id;
        $script->title = $scripts[1]->title;
        $script->position = "updated position";
        $script->content = "updated position";

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
        $this->assertDatabaseCount('scripts', 2);
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
    public function should_not_update_script_status_without_status() {
        $script = factory(Script::class)->create();
        $script->status = 2;
        unset($script['created_at']);
        unset($script['updated_at']);
        $script = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/status/'.$script['id'], ['title' => 'new title']);

        $response->assertStatus(500);
    }

    /** @test */
    public function should_not_update_undefined_script_status() {
        $script = factory(Script::class)->create();
        unset($script['created_at']);
        unset($script['updated_at']);
        $script = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/status/0', ['status' => 1,'title' => 'new title']);

        $response->assertStatus(400);
        $response->assertJson(['message'=> "script not found"]);
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
    public function should_not_update_script_position_without_position() {
        $script = factory(Script::class)->create();
        unset($script['created_at']);
        unset($script['updated_at']);
        $script = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/position/'.$script['id'], ['title' => 'new title', 'status' => 'new status']);

        $response->assertStatus(500);
    }

    /** @test */
    public function should_not_update_undefined_script_position() {
        $script = factory(Script::class)->create();
        unset($script['created_at']);
        unset($script['updated_at']);
        $script = $script->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/status/0', ['position' => 'new postion','title' => 'new title']);

        $response->assertStatus(400);
        $response->assertJson(['message'=> "script not found"]);
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

        $response = $this->json('PUT', 'api/admin/scripts/status/bulk', ["id" => $listIds, "status" => 2, "title"=> "try_to_update_new_title"]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($scripts as $script) {
            $this->assertDatabaseHas('scripts', $script);
        }
    }

    /** @test */
    public function should_not_bulk_update_script_status_without_status() {
        $scripts = factory(Script::class, 5)->create();
        $scripts = $scripts->map(function ($item) {
            unset($item['created_at']);
            unset($item['updated_at']);
            $item['status'] = 2;
            return $item;
        })->toArray();

        $listIds = array_column($scripts, 'id');

        $response = $this->json('PUT', 'api/admin/scripts/status/bulk', ["id" => $listIds]);

        $response->assertStatus(500);
        $response->assertJson(['message' => "Undefined index: status"]);
    }

    /** @test */
    public function should_not_bulk_update_undefined_script_status() {
        $scripts = factory(Script::class, 5)->create();
        $scripts = $scripts->map(function ($item) {
            unset($item['created_at']);
            unset($item['updated_at']);
            $item['status'] = 2;
            return $item;
        })->toArray();

        $response = $this->json('PUT', 'api/admin/scripts/status/bulk', ["id" => [0,6]]);

        $response->assertStatus(400);
        $response->assertJson(['message' => "script not found"]);
    }
}