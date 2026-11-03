<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Core
            'view-dashboard', 'manage-settings', 'manage-users', 'manage-roles', 'view-audit-logs',
            'manage-tenants', 'manage-branches', 'manage-modules', 'view-system-health',
            // AI Sales
            'view-lead-scores', 'recalculate-scores', 'view-followups', 'approve-followups',
            'send-followups', 'use-ai-copilot', 'view-ai-insights', 'manage-ai-providers',
            // CRM
            'view-leads', 'create-leads', 'edit-leads', 'delete-leads', 'assign-leads',
            'view-contacts', 'create-contacts', 'edit-contacts', 'delete-contacts',
            'view-deals', 'create-deals', 'edit-deals', 'delete-deals',
            'view-inbox', 'send-messages', 'manage-sequences', 'manage-workflows',
            'view-quotes', 'create-quotes', 'approve-quotes', 'delete-quotes',
            'view-invoices', 'create-invoices', 'manage-payments', 'manage-products',
            // Marketing
            'manage-campaigns', 'manage-forms', 'manage-cms', 'view-surveys',
            // Service
            'view-tickets', 'create-tickets', 'resolve-tickets', 'manage-knowledge-base',
            // Operations
            'manage-inventory', 'view-inventory', 'manage-purchases', 'use-pos', 'view-accounting', 'manage-expenses',
            'manage-field-sales', 'manage-projects',
            // HR
            'view-employees', 'manage-employees', 'view-attendance', 'sync-attendance',
            'manage-leave', 'manage-payroll', 'manage-okrs',
            // Analytics
            'view-reports', 'create-reports', 'export-data', 'manage-approvals',
            // Portals
            'access-customer-portal', 'access-partner-portal', 'access-employee-portal',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roleDefinitions = [
            'Super Admin' => $permissions,
            'Business Owner' => $permissions,
            'Branch Manager' => [
                'view-dashboard', 'view-leads', 'create-leads', 'edit-leads', 'assign-leads',
                'view-contacts', 'create-contacts', 'edit-contacts',
                'view-deals', 'create-deals', 'edit-deals',
                'view-inbox', 'send-messages', 'view-quotes', 'create-quotes',
                'view-invoices', 'create-invoices', 'view-lead-scores', 'view-followups',
                'approve-followups', 'send-followups', 'use-ai-copilot', 'view-reports',
                'view-attendance', 'manage-leave', 'manage-expenses', 'access-employee-portal',
            ],
            'Sales Manager' => [
                'view-dashboard', 'view-leads', 'create-leads', 'edit-leads', 'delete-leads', 'assign-leads',
                'view-contacts', 'create-contacts', 'edit-contacts',
                'view-deals', 'create-deals', 'edit-deals', 'delete-deals',
                'view-inbox', 'send-messages', 'manage-sequences',
                'view-quotes', 'create-quotes', 'approve-quotes',
                'view-lead-scores', 'recalculate-scores', 'view-followups', 'approve-followups',
                'send-followups', 'use-ai-copilot', 'view-ai-insights', 'view-reports',
                'access-employee-portal',
            ],
            'Sales Rep' => [
                'view-dashboard', 'view-leads', 'create-leads', 'edit-leads',
                'view-contacts', 'create-contacts', 'edit-contacts',
                'view-deals', 'create-deals', 'edit-deals',
                'view-inbox', 'send-messages', 'view-quotes', 'create-quotes',
                'view-lead-scores', 'view-followups', 'send-followups',
                'use-ai-copilot', 'manage-expenses', 'access-employee-portal',
            ],
            'SDR' => [
                'view-dashboard', 'view-leads', 'create-leads', 'edit-leads',
                'view-contacts', 'create-contacts',
                'view-inbox', 'send-messages', 'view-lead-scores', 'view-followups',
                'send-followups', 'use-ai-copilot', 'access-employee-portal',
            ],
            'Marketing Manager' => [
                'view-dashboard', 'view-leads', 'create-leads', 'view-contacts', 'create-contacts',
                'manage-campaigns', 'manage-forms', 'manage-cms', 'view-surveys', 'view-lead-scores',
                'use-ai-copilot', 'view-reports', 'access-employee-portal',
            ],
            'Customer Support Agent' => [
                'view-dashboard', 'view-tickets', 'create-tickets', 'resolve-tickets',
                'manage-knowledge-base', 'view-inbox', 'send-messages', 'view-contacts',
                'use-ai-copilot', 'access-employee-portal',
            ],
            'Finance / Accountant' => [
                'view-dashboard', 'view-invoices', 'create-invoices', 'manage-payments',
                'view-accounting', 'manage-expenses', 'manage-payroll', 'view-reports',
                'export-data', 'access-employee-portal',
            ],
            'HR Manager' => [
                'view-dashboard', 'view-employees', 'manage-employees', 'view-attendance',
                'sync-attendance', 'manage-leave', 'manage-payroll', 'manage-okrs',
                'access-employee-portal',
            ],
            'Inventory Manager' => [
                'view-dashboard', 'manage-inventory', 'manage-products', 'manage-purchases',
                'access-employee-portal',
            ],
            'Purchase Officer' => [
                'view-dashboard', 'manage-purchases', 'manage-products', 'view-inventory',
                'access-employee-portal',
            ],
            'Field Agent' => [
                'view-dashboard', 'manage-field-sales', 'view-leads', 'view-contacts',
                'view-deals', 'view-followups', 'access-employee-portal',
            ],
            'Auditor / Viewer' => [
                'view-dashboard', 'view-audit-logs', 'view-leads', 'view-contacts',
                'view-deals', 'view-invoices', 'view-reports',
            ],
            'Customer' => [
                'access-customer-portal', 'view-quotes', 'view-invoices', 'create-tickets',
            ],
            'Partner' => [
                'access-partner-portal', 'create-leads', 'view-leads', 'view-reports',
            ],
        ];

        foreach ($roleDefinitions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
