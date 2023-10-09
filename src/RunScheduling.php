<?php

namespace Ysnow\Scheduling;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class RunScheduling extends Form implements LazyRenderable
{

    use LazyWidget;

    public function default()
    {

        $scheduling = new Scheduling();
        $rs = $scheduling->runTask($this->payload['id']);
        return [
            // 展示上个页面传递过来的值
            'name' => $rs ?? '',
        ];
    }

    public function form()
    {
        $this->textarea('name','日志')->disable();
        $this->disableResetButton();
        $this->disableSubmitButton();
    }
}