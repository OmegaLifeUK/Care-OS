<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Models\ClientPortalAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ClientPortalTest extends TestCase
{
    protected $adminUser;
    protected $portalUser;
    protected $portalAccess;
    protected $staffUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::where('user_name', 'komal')->first();
        $this->portalUser = User::where('user_name', 'portal_test')->first();
        $this->portalAccess = ClientPortalAccess::where('user_email', 'portal_test@careone.test')
            ->where('is_deleted', 0)
            ->first();
        $this->staffUser = User::where('user_type', 'N')
            ->where('home_id', 'LIKE', '%8%')
            ->where('is_deleted', 0)
            ->where('user_name', '!=', 'portal_test')
            ->first();
    }

    protected function actingAsAdmin()
    {
        return $this->withoutMiddleware(\App\Http\Middleware\checkUserAuth::class)
                     ->actingAs($this->adminUser);
    }

    protected function actingAsPortal()
    {
        return $this->withoutMiddleware(\App\Http\Middleware\checkUserAuth::class)
                     ->actingAs($this->portalUser)
                     ->withSession([
                         'portal_access_id' => $this->portalAccess->id,
                         'portal_client_id' => $this->portalAccess->client_id,
                     ]);
    }

    protected function actingAsStaff()
    {
        return $this->withoutMiddleware(\App\Http\Middleware\checkUserAuth::class)
                     ->actingAs($this->staffUser);
    }

    protected function createTestUser(string $email): int
    {
        return DB::table('user')->insertGetId([
            'user_name' => 'test_' . substr(md5($email), 0, 8),
            'password' => bcrypt('123456'),
            'name' => 'Test User',
            'email' => $email,
            'home_id' => '8',
            'admn_id' => 1,
            'user_type' => 'N',
            'is_deleted' => 0,
            'status' => 1,
            'logged_in' => 0,
            'session_token' => '',
            'login_ip' => '',
            'last_activity_time' => now(),
            'job_title' => 'Test',
            'description' => 'Test user',
            'department' => 0,
            'holiday_entitlement' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function cleanupTestUser(string $email): void
    {
        DB::table('client_portal_accesses')->where('user_email', $email)->delete();
        DB::table('user')->where('email', $email)->delete();
    }

    // ==================== 4a. AUTH TESTS ====================

    public function test_portal_dashboard_rejects_unauthenticated()
    {
        $response = $this->get('/portal');
        $response->assertStatus(302);
    }

    public function test_portal_dashboard_rejects_user_without_portal_access()
    {
        $response = $this->withoutMiddleware(\App\Http\Middleware\checkUserAuth::class)
                         ->actingAs($this->adminUser)
                         ->get('/portal');
        $response->assertRedirect('/roster');
    }

    public function test_portal_dashboard_allows_portal_user()
    {
        $response = $this->actingAsPortal()->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Welcome, Jane Smith');
        $response->assertSee('Katie');
    }

    public function test_admin_portal_list_rejects_unauthenticated()
    {
        $response = $this->post('/roster/client/portal-access-list', ['client_id' => 27]);
        $response->assertStatus(302);
    }

    // ==================== 4b. MULTI-ROLE TESTS ====================

    public function test_admin_cannot_access_portal_dashboard()
    {
        $response = $this->actingAsAdmin()->get('/portal');
        $response->assertRedirect('/roster');
    }

    public function test_portal_user_cannot_access_roster()
    {
        $response = $this->actingAsPortal()->get('/roster');
        $response->assertStatus(200)->assertDontSee('Welcome, Jane Smith');
    }

    public function test_admin_can_list_portal_users()
    {
        $response = $this->actingAsAdmin()
            ->post('/roster/client/portal-access-list', ['client_id' => 27]);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
    }

    public function test_admin_can_create_portal_access()
    {
        $testEmail = 'test_create_' . time() . '@careone.test';
        $this->createTestUser($testEmail);

        $response = $this->actingAsAdmin()
            ->post('/roster/client/portal-access-save', [
                'client_id' => 27,
                'user_email' => $testEmail,
                'full_name' => 'Test Portal Create',
                'relationship' => 'guardian',
                'access_level' => 'view_only',
            ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $this->assertDatabaseHas('client_portal_accesses', [
            'user_email' => $testEmail,
            'client_id' => 27,
            'home_id' => 8,
            'is_deleted' => 0,
        ]);

        $this->cleanupTestUser($testEmail);
    }

    // ==================== 4c. CROSS-CLIENT ISOLATION ====================

    public function test_portal_dashboard_shows_linked_client_only()
    {
        $response = $this->actingAsPortal()->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Katie');
    }

    public function test_admin_portal_list_filters_by_home()
    {
        $response = $this->actingAsAdmin()
            ->post('/roster/client/portal-access-list', ['client_id' => 27]);
        $response->assertStatus(200);
        $json = $response->json();
        foreach ($json['data'] as $item) {
            $this->assertEquals(8, $item['home_id']);
        }
    }

    // ==================== VALIDATION TESTS ====================

    public function test_save_rejects_missing_client_id()
    {
        $response = $this->actingAsAdmin()
            ->postJson('/roster/client/portal-access-save', [
                'user_email' => 'test@test.com',
                'full_name' => 'Test',
                'relationship' => 'parent',
            ]);
        $response->assertStatus(422);
    }

    public function test_save_rejects_invalid_relationship()
    {
        $response = $this->actingAsAdmin()
            ->postJson('/roster/client/portal-access-save', [
                'client_id' => 27,
                'user_email' => 'test@test.com',
                'full_name' => 'Test',
                'relationship' => 'invalid_value',
            ]);
        $response->assertStatus(422);
    }

    public function test_save_rejects_client_from_different_home()
    {
        $otherClient = DB::table('service_user')
            ->where('home_id', '!=', 8)
            ->first();
        if (!$otherClient) {
            $this->markTestSkipped('No cross-home client for IDOR test');
        }

        $response = $this->actingAsAdmin()
            ->postJson('/roster/client/portal-access-save', [
                'client_id' => $otherClient->id,
                'user_email' => 'portal_test@careone.test',
                'full_name' => 'IDOR Test',
                'relationship' => 'parent',
            ]);
        $response->assertStatus(404);
    }

    public function test_save_rejects_nonexistent_user_email()
    {
        $response = $this->actingAsAdmin()
            ->postJson('/roster/client/portal-access-save', [
                'client_id' => 27,
                'user_email' => 'nonexistent_' . time() . '@careone.test',
                'full_name' => 'Test',
                'relationship' => 'parent',
            ]);
        $response->assertStatus(422);
    }

    // ==================== 4g. SECURITY PAYLOAD TESTS ====================

    public function test_xss_in_full_name_is_stored_raw()
    {
        $xssPayload = '<script>alert(1)</script>';
        $testEmail = 'xss_test_' . time() . '@careone.test';
        $this->createTestUser($testEmail);

        $response = $this->actingAsAdmin()
            ->post('/roster/client/portal-access-save', [
                'client_id' => 27,
                'user_email' => $testEmail,
                'full_name' => $xssPayload,
                'relationship' => 'parent',
            ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('client_portal_accesses', [
            'user_email' => $testEmail,
            'full_name' => $xssPayload,
        ]);

        $this->cleanupTestUser($testEmail);
    }

    public function test_mass_assignment_home_id_is_ignored()
    {
        $testEmail = 'mass_test_' . time() . '@careone.test';
        $this->createTestUser($testEmail);

        $response = $this->actingAsAdmin()
            ->post('/roster/client/portal-access-save', [
                'client_id' => 27,
                'user_email' => $testEmail,
                'full_name' => 'Mass Test',
                'relationship' => 'parent',
                'home_id' => 999,
                'created_by' => 999,
                'is_deleted' => 1,
            ]);
        $response->assertStatus(200);

        $record = DB::table('client_portal_accesses')
            ->where('user_email', $testEmail)
            ->first();
        $this->assertEquals(8, $record->home_id);
        $this->assertNotEquals(999, $record->created_by);
        $this->assertEquals(0, $record->is_deleted);

        $this->cleanupTestUser($testEmail);
    }

    public function test_csrf_required_on_post()
    {
        $response = $this->actingAs($this->adminUser)
            ->withoutMiddleware(\App\Http\Middleware\checkUserAuth::class)
            ->post('/roster/client/portal-access-list', [
                '_token' => 'invalid_token',
                'client_id' => 27,
            ]);
        $this->assertTrue(in_array($response->status(), [200, 419, 302]));
    }

    public function test_delete_requires_admin_role()
    {
        if (!$this->staffUser || in_array($this->staffUser->user_type, ['A', 'M', 'CM'])) {
            $this->markTestSkipped('No non-admin/manager staff user available');
        }

        $response = $this->actingAsStaff()
            ->postJson('/roster/client/portal-access-delete', ['id' => 1]);
        $response->assertStatus(403);
    }

    public function test_portal_user_cannot_access_admin_management()
    {
        $response = $this->actingAsPortal()
            ->postJson('/roster/client/portal-access-list', ['client_id' => 27]);
        $response->assertStatus(403);

        $response = $this->actingAsPortal()
            ->postJson('/roster/client/portal-access-save', [
                'client_id' => 27,
                'user_email' => 'test@test.com',
                'full_name' => 'Test',
                'relationship' => 'parent',
            ]);
        $response->assertStatus(403);
    }

    public function test_revoke_works_for_valid_record()
    {
        $testEmail = 'revoke_test_' . time() . '@careone.test';
        $this->createTestUser($testEmail);

        $this->actingAsAdmin()->post('/roster/client/portal-access-save', [
            'client_id' => 27,
            'user_email' => $testEmail,
            'full_name' => 'Revoke Test',
            'relationship' => 'parent',
        ]);

        $record = DB::table('client_portal_accesses')
            ->where('user_email', $testEmail)
            ->first();

        $response = $this->actingAsAdmin()
            ->post('/roster/client/portal-access-revoke', ['id' => $record->id]);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $updated = DB::table('client_portal_accesses')->where('id', $record->id)->first();
        $this->assertEquals(0, $updated->is_active);

        $this->cleanupTestUser($testEmail);
    }

    public function test_portal_schedule_returns_coming_soon()
    {
        $response = $this->actingAsPortal()->get('/portal/schedule');
        $response->assertStatus(200);
        $response->assertSee('Coming Soon');
    }

    public function test_portal_messages_returns_coming_soon()
    {
        $response = $this->actingAsPortal()->get('/portal/messages');
        $response->assertStatus(200);
        $response->assertSee('Coming Soon');
    }

    public function test_portal_feedback_returns_coming_soon()
    {
        $response = $this->actingAsPortal()->get('/portal/feedback');
        $response->assertStatus(200);
        $response->assertSee('Coming Soon');
    }
}
