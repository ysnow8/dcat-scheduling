<?php

namespace Ysnow\Scheduling;

use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;

class Scheduling
{

    /**
     * @var string out put file for command.
     */
    protected $sendOutputTo;
    /**
     * @throws BindingResolutionException
     */
    protected function getKernelEvents(): array
    {
        app()->make(Kernel::class);

        return app()->make(Schedule::class)->events();
    }


    public function getTasks(): array
    {
        $tasks = [];
        foreach ($this->getKernelEvents() as $key => $event) {
            $tasks[] = [
                'id'          => $key + 1,
                'task'        => $this->formatTask($event)['name'],
                'expression'  => $event->expression,
                'nextRunDate' => $event->nextRunDate()->format('Y-m-d H:i:s'),
                'description' => $event->description,
                //                'readable'      => CronSchedule::fromCronString($event->expression)->asNaturalLanguage(),
            ];
        }

        return $tasks;
    }

    protected function formatTask($event): array
    {
        if ($event instanceof CallbackEvent) {
            return [
                'type' => 'closure',
                'name' => 'Closure',
            ];
        }

        if (Str::contains($event->command, '\'artisan\'')) {
            $exploded = explode(' ', $event->command);

            return [
                'type' => 'artisan',
                'name' => 'artisan ' . implode(' ', array_slice($exploded, 2)),
            ];
        }

        if (PHP_OS_FAMILY === 'Windows' && Str::contains($event->command, '"artisan"')) {
            $exploded = explode(' ', $event->command);

            return [
                'type' => 'artisan',
                'name' => 'artisan ' . implode(' ', array_slice($exploded, 2)),
            ];
        }

        return [
            'type' => 'command',
            'name' => $event->command,
        ];
    }

    /**
     * @throws BindingResolutionException
     * @throws \Throwable
     */
    public function runTask($id)
    {
        set_time_limit(0);
        $event = $this->getKernelEvents()[$id - 1];
        if (PHP_OS_FAMILY === 'Windows') {
            $event->command = Str::of($event->command)->replace('php-cgi.exe', 'php.exe');
        }

        $event->sendOutputTo($this->getOutputTo());
        $event->run(app());
        return $this->readOutput();
    }

    /**
     * @return string
     */
    protected function getOutputTo()
    {
        if (!$this->sendOutputTo) {
            $this->sendOutputTo = storage_path('app/task-schedule.output');
        }

        return $this->sendOutputTo;
    }

    /**
     * Read output info from output file.
     *
     * @return string
     */
    protected function readOutput()
    {
        return file_get_contents($this->getOutputTo());
    }
}