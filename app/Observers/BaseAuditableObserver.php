<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class BaseAuditableObserver
{
    /**
     * @var array<int, array{old_values: array<string, mixed>, new_values: array<string, mixed>}>
     */
    private static array $pendingUpdates = [];

    public function created(Model $model): void
    {
        $this->logger()->logModelEvent(
            module: $this->module(),
            action: 'create',
            model: $model,
            oldValues: null,
            newValues: $this->sanitize($model->attributesToArray()),
        );
    }

    public function updating(Model $model): void
    {
        $dirty = $this->sanitize($model->getDirty());

        if ($dirty === []) {
            return;
        }

        self::$pendingUpdates[spl_object_id($model)] = [
            'old_values' => Arr::only($this->sanitize($model->getOriginal()), array_keys($dirty)),
            'new_values' => $dirty,
        ];
    }

    public function updated(Model $model): void
    {
        $key = spl_object_id($model);
        $changeSet = self::$pendingUpdates[$key] ?? null;
        unset(self::$pendingUpdates[$key]);

        if ($changeSet === null || $changeSet['new_values'] === []) {
            return;
        }

        $this->logger()->logModelEvent(
            module: $this->module(),
            action: 'update',
            model: $model,
            oldValues: $changeSet['old_values'],
            newValues: $changeSet['new_values'],
        );
    }

    public function deleted(Model $model): void
    {
        $before = $this->sanitize($model->getOriginal());

        if ($before === []) {
            $before = $this->sanitize($model->attributesToArray());
        }

        $this->logger()->logModelEvent(
            module: $this->module(),
            action: 'delete',
            model: $model,
            oldValues: $before,
            newValues: null,
        );
    }

    abstract protected function module(): string;

    /**
     * @return list<string>
     */
    protected function ignoredKeys(): array
    {
        return [];
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private function sanitize(array $values): array
    {
        return Arr::except($values, $this->ignoredKeys());
    }

    private function logger(): AuditLogService
    {
        /** @var AuditLogService $service */
        $service = app(AuditLogService::class);

        return $service;
    }
}
