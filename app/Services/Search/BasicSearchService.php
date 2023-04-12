<?php

namespace App\Services\Search;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BasicSearchService
{
    protected $query;
    protected $offset;
    protected $limit;

    public function __construct()
    {
        $this->offset = 0;
        $this->limit = 10;
    }

    public function setUserId($id): static
    {
        if (! empty($id)) {
            $this->query->where('user_id', $id);
        }
        return $this;
    }

    public function setDateFrom($from): static
    {
        if (!empty($from)) {
            $this->query->where('created_at', '>=', (new Carbon($from))->tz('Europe/Moscow'));
        }
        return $this;
    }

    public function setDateTo($to): static
    {
        if (!empty($to)) {
            $this->query->where('created_at', '<=', (new Carbon($to))->tz('Europe/Moscow'));
        }
        return $this;
    }

    public function setOffset($offset): static
    {
        $this->offset = $offset ?? 0;
        return $this;
    }

    public function setLimit($limit): static
    {
        $this->limit = $limit ?? 10;
        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getCount()
    {
        return $this->query->count();
    }

    public function getResult()
    {
        return (clone($this->query))
            ->offset($this->offset)
            ->limit($this->limit)
            ->get();
    }

    public function getSql()
    {
        return Str::replaceArray('?', $this->query->getBindings(), $this->query->toSql());
    }
}
