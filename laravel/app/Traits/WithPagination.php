<?php

namespace App\Traits;

trait WithPagination
{
    public readonly int $page;

    public readonly int $perPage;

    public readonly ?string $search;

    public readonly ?string $sortBy;

    public readonly ?string $sortOrder;

    protected function initializePagination(array $data = []): void
    {
        $this->page = $data['page'] ?? 1;
        $this->perPage = $data['perPage'] ?? 10;
        $this->search = $data['search'] ?? null;
        $this->sortBy = $data['sortBy'] ?? 'created_at';
        $this->sortOrder = $data['sortOrder'] ?? 'desc';
    }

    public function toRepositoryParams(): array
    {
        return [
            'page' => $this->page,
            'perPage' => $this->perPage,
            'search' => $this->search,
            'sortBy' => $this->sortBy,
            'sortOrder' => $this->sortOrder,
        ];
    }
}
