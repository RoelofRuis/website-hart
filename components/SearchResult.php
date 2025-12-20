<?php

namespace app\components;

class SearchResult
{
    private array $items;
    private bool $hasNextPage;
    private ?int $nextPage;
    private ?string $q;

    public function __construct(
        array   $items,
        bool    $hasNextPage,
        ?int    $nextPage = null,
        ?string $q = null,
    ) {
        $this->items = $items;
        $this->hasNextPage = $hasNextPage;
        $this->nextPage = $nextPage;
        $this->q = $q;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }

    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    public function getQ(): ?string
    {
        return $this->q;
    }
}
