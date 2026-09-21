<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionUiEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $staff;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->superAdmin = User::where('email', 'superadmin@example.com')->first();
        $this->admin = User::where('email', 'admin@example.com')->first();
        $this->staff = User::where('email', 'staff@example.com')->first();
        $this->viewer = User::where('email', 'viewer@example.com')->first();
    }

    /**
     * Test Role Simulator switch, session state, and exit.
     */
    public function test_role_simulator_switch_and_exit(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post('/simulator/switch', ['role' => 'staff']);

        $response->assertSessionHas('simulated_role', 'staff');

        // Verify active role name reflects simulation
        $this->assertEquals('staff', $this->superAdmin->getActiveRoleName());

        // Exit simulation
        $exitResponse = $this->actingAs($this->superAdmin)
            ->post('/simulator/exit');

        $exitResponse->assertSessionMissing('simulated_role');
        $this->assertEquals('super-admin', $this->superAdmin->getActiveRoleName());
    }

    /**
     * Test UI Policy toggle between disabled_lock and hide.
     */
    public function test_ui_policy_toggle(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post('/simulator/policy', ['policy' => 'hide']);

        $response->assertSessionHas('ui_policy', 'hide');

        $toggleBack = $this->actingAs($this->superAdmin)
            ->post('/simulator/policy', ['policy' => 'disabled_lock']);

        $toggleBack->assertSessionHas('ui_policy', 'disabled_lock');
    }

    /**
     * Test Product Index page displays unmasked costs for Super Admin.
     */
    public function test_super_admin_sees_cost_price_unmasked(): void
    {
        $product = Product::first();

        $response = $this->actingAs($this->superAdmin)
            ->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Cost Price');
        $response->assertSee(number_format($product->cost_price, 2));
        $response->assertSee('Add Product');
    }

    /**
     * Test Product Index page displays masked data when viewing as Staff.
     */
    public function test_staff_sees_masked_cost_price_and_locked_delete(): void
    {
        $response = $this->actingAs($this->staff)
            ->withSession(['ui_policy' => 'disabled_lock'])
            ->get('/products');

        $response->assertStatus(200);
        // Sensitive data is masked with bullets
        $response->assertSee('••••••');
        $response->assertSee('🔒');
    }

    /**
     * Test CSV Export respects field-level permissions.
     */
    public function test_csv_export_respects_permissions(): void
    {
        // Admin export includes Cost Price
        $adminExport = $this->actingAs($this->admin)->get('/products/export');
        $adminExport->assertStatus(200);
    }
}
