<?php

namespace app\components;

class SearchResult
{
    private array $items;
    private bool $hasNextPage;
    private ?int $nextPage;
    private ?string $q;
    private bool $suppressEmpty;

    public function __construct(
        array   $items,
        bool    $hasNextPage,
        ?int    $nextPage = null,
        ?string $q = null,
        bool    $suppressEmpty = false
    ) {
        $this->items = $items;
        $this->hasNextPage = $hasNextPage;
        $this->nextPage = $nextPage;
        $this->q = $q;
        $this->suppressEmpty = $suppressEmpty;
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

    public function getSuppressEmpty(): bool
    {
        return $this->suppressEmpty;
    }
}
