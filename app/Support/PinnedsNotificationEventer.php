<?php

namespace Core\Support;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;

class PinnedsNotificationEventer
{
    protected $events;

    // 默认填充字段格式
    protected $fillable = [
        'name' => '',
        'namespace' => '',
        'owner_prefix' => '',
        'wherecolumn' => '',
    ];

    protected $prefix = 'pinneds_notifications';

    /**
     * create eventer instance.
     *
     * @param Dispatcher $events
     * @author GEO <dev@kaifa.me>
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * register a listener.
     *
     * @param Closure $callback [description]
     * @return [type] [description]
     * @author GEO <dev@kaifa.me>
     */
    public function listen(Closure $callback)
    {
        return $this->events->listen($this->prefix, $callback);
    }

    /**
     * call the listeners.
     *
     * @return [type] [description]
     * @author GEO <dev@kaifa.me>
     */
    public function dispatch()
    {
        $notifications = collect($this->events->dispatch($this->prefix));

        return $notifications->reject(function ($notification): bool {
            if (! is_array($notification)) {
                return true;
            } elseif (! isset($notification['namespace'])) {
                return true;
            } elseif (! class_exists($notification['namespace'])) {
                return true;
            } elseif (array_diff_key($this->fillable, $notification)) {
                return true;
            }

            return false;
        });
    }
}
