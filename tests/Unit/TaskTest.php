<?php

namespace Tests\Unit;

use App\Http\Controllers\TaskController;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_tasks()
    {
        $user = User::factory()->create();

        $tasks = Task::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $controller = new TaskController();
        $result = $controller->index();

        $this->assertCount(2, $result);
        $this->assertEqualsCanonicalizing(
            $tasks->pluck('id')->all(),
            $result->pluck('id')->all()
        );
    }

    public function test_store_creates_new_task()
    {
        $user = User::factory()->create();

        $request = Request::create('/tasks', 'POST', [
            'name' => 'Task de prueba',
            'user_id' => $user->id,
        ]);

        $controller = new TaskController();
        $task = $controller->store($request);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Task de prueba',
            'user_id' => $user->id,
        ]);
    }

    public function test_show_returns_task_by_id()
    {
        $task = Task::factory()->create();

        $controller = new TaskController();
        $result = $controller->show($task);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertTrue($result->is($task));
    }

    public function test_update_changes_task_data()
    {
        $task = Task::factory()->create();
        $newUser = User::factory()->create();

        $request = Request::create('/tasks/' . $task->id, 'PUT', [
            'name' => 'Nombre actualizado',
            'user_id' => $newUser->id,
        ]);

        $controller = new TaskController();
        $updatedTask = $controller->update($request, $task);

        $this->assertInstanceOf(Task::class, $updatedTask);
        $this->assertEquals('Nombre actualizado', $updatedTask->name);
        $this->assertEquals($newUser->id, $updatedTask->user_id);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Nombre actualizado',
            'user_id' => $newUser->id,
        ]);
    }

    public function test_destroy_removes_task()
    {
        $task = Task::factory()->create();

        $controller = new TaskController();
        $response = $controller->destroy($task);

        $this->assertSame(204, $response->getStatusCode());
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_mark_task_completed()
    {
        $task = Task::factory()->create(['completed' => false]);

        $controller = new TaskController();
        $result = $controller->complete($task);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertTrue($result->completed);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);
    }
}

