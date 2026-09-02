<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Seeds the application's three roles:
 *
 * - مدیر (admin): full access to everything, all organizations.
 * - حسابدار (accountant): read-only access, scoped to their assigned
 *   organizations.
 * - کارمند (staff): can register/edit expenses, scoped to their assigned
 *   organizations.
 *
 * Role names are kept in Persian since the rest of the app's domain
 * vocabulary (labels, enums) is Persian too — see App\Models\User for the
 * matching hasRole() helpers.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        collect(['مدیر', 'حسابدار', 'کارمند'])
            ->each(fn (string $role) => Role::findOrCreate($role, 'web'));

        // Make sure the first seeded user (see UserSeeder) can actually log
        // into the panel; without a role, canAccessPanel() denies access.
        User::query()->first()?->assignRole('مدیر');
    }
}
