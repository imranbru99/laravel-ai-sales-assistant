<?php

namespace App\Core\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $branchId = app(TenantContext::class)->getBranchId();

        if ($branchId !== null) {
            $builder->where(function (Builder $query) use ($model, $branchId) {
                $query->where($model->qualifyColumn('branch_id'), $branchId)
                      ->orWhereNull($model->qualifyColumn('branch_id'));
            });
        }
    }
}
