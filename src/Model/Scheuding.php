<?php

namespace Ysnow\Scheduling\Model;

use Dcat\Admin\Grid\Model;
use Dcat\Admin\Repositories\Repository;
use Ysnow\Scheduling\Scheduling;

class Scheuding extends Repository
{

    public function getPrimaryKeyColumn(): string
    {
        return 'id';
    }


    public function get(Model $model): \Illuminate\Contracts\Pagination\LengthAwarePaginator|array|\Illuminate\Support\Collection
    {
        return (new Scheduling())->getTasks();
    }

}