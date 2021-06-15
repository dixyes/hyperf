<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Utils\Coroutine;

use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Traits\Container;

class Locker
{
    use Container;

    /**
     * @var array
     */
    protected static $container = [];

    public static function add($key, $id): void
    {
        self::$container[$key][] = $id;
    }

    public static function clear($key): void
    {
        unset(self::$container[$key]);
    }

    public static function lock($key): bool
    {
        if (\Hyperf\Engine\Constant::ENGINE === 'Swoole') {
            if (! self::has($key)) {
                self::add($key, 0);
                return true;
            }
            self::add($key, Coroutine::id());
        } else if (\Hyperf\Engine\Constant::ENGINE === 'Swow') {
            if (! self::has($key)) {
                self::add($key, null);
                return true;
            }
            self::add($key, \Swow\Coroutine::getCurrent());
        } else {
            // TODO: warning
        }
        \Hyperf\Engine\Coroutine::yield();
        return false;
    }

    public static function unlock($key): void
    {
        if (self::has($key)) {
            $handles = self::get($key);
            foreach ($handles as $handle) {
                if (\Hyperf\Engine\Constant::ENGINE === 'Swoole') {
                    if ($handle > 0) {
                        \Swoole\Coroutine::resume($handle);
                    }
                } else if (\Hyperf\Engine\Constant::ENGINE === 'Swow') {
                    if ($handle !== null) {
                        $handle->resume();
                    }
                } else {
                    // TODO: warning
                }
            }
            self::clear($key);
        }
    }
}
