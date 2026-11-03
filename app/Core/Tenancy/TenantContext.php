<?php

namespace App\Core\Tenancy;

use App\Models\Branch;
use App\Models\Tenant;

class TenantContext
{
    protected ?Tenant $tenant = null;
    protected ?Branch $branch = null;

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getTenant(): ?Tenant
    {
        if ($this->tenant !== null) {
            return $this->tenant;
        }

        if (auth()->check() && auth()->user()->tenant_id) {
            $this->tenant = auth()->user()->tenant;
        }

        return $this->tenant;
    }

    public function getTenantId(): ?int
    {
        return $this->getTenant()?->id ?? (auth()->check() ? auth()->user()->tenant_id : null);
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;
        return $this;
    }

    public function getBranch(): ?Branch
    {
        if ($this->branch !== null) {
            return $this->branch;
        }

        if (auth()->check() && auth()->user()->branch_id) {
            $this->branch = auth()->user()->branch;
        }

        return $this->branch;
    }

    public function getBranchId(): ?int
    {
        return $this->getBranch()?->id ?? (auth()->check() ? auth()->user()->branch_id : null);
    }

    public function hasTenant(): bool
    {
        return $this->getTenantId() !== null;
    }

    public function hasBranch(): bool
    {
        return $this->getBranchId() !== null;
    }

    public function clear(): void
    {
        $this->tenant = null;
        $this->branch = null;
    }
}
