<?php

namespace InertiaStudio\Laravel\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait HasAuthorization
{
    protected function authorizeModuleAction(string $moduleClass, string $ability, mixed ...$arguments): bool
    {
        $model = $moduleClass::getModel();

        $policy = Gate::getPolicyFor($model);

        if (! $policy) {
            return true;
        }

        return Gate::allows($ability, $arguments ?: [$model]);
    }

    protected function canViewAny(string $moduleClass): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'viewAny');
    }

    protected function canView(string $moduleClass, Model $record): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'view', $record);
    }

    protected function canCreate(string $moduleClass): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'create');
    }

    protected function canUpdate(string $moduleClass, Model $record): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'update', $record);
    }

    protected function canDelete(string $moduleClass, Model $record): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'delete', $record);
    }

    protected function canDeleteAny(string $moduleClass): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'deleteAny');
    }

    protected function canRestore(string $moduleClass, Model $record): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'restore', $record);
    }

    protected function canForceDelete(string $moduleClass, Model $record): bool
    {
        return $this->authorizeModuleAction($moduleClass, 'forceDelete', $record);
    }
}
