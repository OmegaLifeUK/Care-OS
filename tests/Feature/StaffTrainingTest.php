<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Models\Training;
use App\Models\StaffTraining;

class StaffTrainingTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('is_deleted', 0)->where('status', 1)->first();
    }

    // --- Authentication tests ---

    /** @test */
    public function unauthenticated_user_cannot_access_training_list()
    {
        $response = $this->get('/staff/trainings');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_training_view()
    {
        $response = $this->get('/staff/training/view/1');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function unauthenticated_user_cannot_add_training()
    {
        $response = $this->post('/staff/training/add', [
            'name' => 'Test', 'training_provider' => 'P', 'desc' => 'D', 'month' => 1, 'year' => '2026'
        ]);
        $response->assertRedirect('/login');
    }

    // --- Authenticated access tests ---

    /** @test */
    public function authenticated_user_can_view_training_list()
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->get('/staff/trainings');

        $response->assertStatus(200);
        $response->assertViewIs('frontEnd.staffManagement.training_listing');
    }

    /** @test */
    public function authenticated_user_can_view_training_detail()
    {
        $homeId = explode(',', $this->user->home_id)[0];
        $training = Training::where('home_id', $homeId)->where('is_deleted', 0)->first();

        if (!$training) {
            $this->markTestSkipped('No training records for this home.');
        }

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->get('/staff/training/view/' . $training->id);

        $response->assertStatus(200);
        $response->assertViewIs('frontEnd.staffManagement.training_view');
    }

    // --- Validation tests ---

    /** @test */
    public function add_training_requires_name()
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/staff/training/add', [
                'training_provider' => 'Provider',
                'desc' => 'Description',
                'month' => 1,
                'year' => '2026',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function add_training_validates_month_range()
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/staff/training/add', [
                'name' => 'Test Training',
                'training_provider' => 'Provider',
                'desc' => 'Description',
                'month' => 13,  // Invalid
                'year' => '2026',
            ]);

        $response->assertSessionHasErrors('month');
    }

    /** @test */
    public function edit_training_requires_all_fields()
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/staff/training/edit_fields', [
                'training_id' => 1,
                // Missing required fields
            ]);

        $response->assertSessionHasErrors(['name', 'training_provider', 'desc', 'month', 'year']);
    }

    // --- Multi-tenancy tests ---

    /** @test */
    public function cannot_view_training_from_different_home()
    {
        $homeId = explode(',', $this->user->home_id)[0];

        // Find a training that belongs to a different home
        $otherTraining = Training::where('home_id', '!=', $homeId)->where('is_deleted', 0)->first();

        if (!$otherTraining) {
            $this->markTestSkipped('No training from other home available to test.');
        }

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->get('/staff/training/view/' . $otherTraining->id);

        // Should redirect away since training not found for this home
        $response->assertRedirect('/staff/trainings');
    }

    /** @test */
    public function view_fields_returns_false_for_other_homes_training()
    {
        $homeId = explode(',', $this->user->home_id)[0];
        $otherTraining = Training::where('home_id', '!=', $homeId)->where('is_deleted', 0)->first();

        if (!$otherTraining) {
            $this->markTestSkipped('No training from other home available to test.');
        }

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->get('/staff/training/view_fields/' . $otherTraining->id);

        $response->assertJson(['response' => false]);
    }

    // --- Staff assignment tests ---

    /** @test */
    public function assign_staff_validates_user_ids()
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/staff/training/staff/add', [
                'training_id' => 1,
                // Missing user_ids
            ]);

        $response->assertSessionHasErrors('user_ids');
    }

    // --- Delete tests ---

    /** @test */
    public function delete_requires_post_method()
    {
        // GET should return 405 Method Not Allowed
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->get('/staff/training/delete/1');

        $response->assertStatus(405);
    }
}
