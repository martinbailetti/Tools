<?php

namespace Tests\Feature\Tasks;

use App\Models\Tasks\UserTask;
use App\Models\Security\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTasksControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_method_returns_error_when_id_is_missing()
    {
        // Simula un usuario autenticado
        $user = User::factory()->create()->first();
        $this->actingAs($user);

        // Llama al método sin pasar el parámetro 'id'
        $response = $this->getJson('/api/user-tasks');

        // Verifica que la respuesta sea un error
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => false,
                     'message' => 'ID is required',
                 ]);
    }

    public function test_get_method_returns_task_when_id_is_provided()
    {
        // Simula un usuario autenticado
        $user = User::factory()->create()->first();
        $this->actingAs($user);

        // Crea una tarea de usuario
        $task = UserTask::factory()->create([
            'user_id' => $user->id,
        ]);

        // Llama al método con el parámetro 'id'
        $response = $this->getJson('/api/user-tasks?id=' . $task->id);

        // Verifica que la respuesta sea exitosa y contenga los datos esperados
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'result' => [
                         'id' => $task->id,
                         'user_id' => $user->id,
                     ],
                 ]);
    }
}
