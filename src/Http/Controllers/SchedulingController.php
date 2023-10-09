<?php

namespace Ysnow\Scheduling\Http\Controllers;

use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Ysnow\Scheduling\Model\Scheuding;
use Ysnow\Scheduling\RunScheduling;
use Ysnow\Scheduling\Scheduling;

class SchedulingController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('任务调度')
            ->description('Description')
            ->body($this->grid());
    }

    protected function grid(): Grid
    {
        return Grid::make(new Scheuding(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('task', '任务');
            $grid->column('expression', '表达式');
            $grid->column('nextRunDate', '下次执行时间');
            $grid->column('description', '描述');
            $grid->column('', '操作')->display('执行任务')->modal(function (Grid\Displayers\Modal $modal) {
                $modal->title('任务日志');
                $modal->icon('feather icon-edit');
                return RunScheduling::make()->payload(['id' => $this->id]);
            });
            $grid->disableCreateButton();
            $grid->disablePagination();
            $grid->disableActions();
        });
    }
}