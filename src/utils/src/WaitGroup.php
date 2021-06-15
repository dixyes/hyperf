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
namespace Hyperf\Utils;

// maybe this should be in engine?
if (\Hyperf\Engine\Constant::ENGINE === 'Swoole') {
    class WaitGroup extends \Swoole\Coroutine\WaitGroup
    {
    }
} else if (\Hyperf\Engine\Constant::ENGINE === 'Swow') {
    class WaitGroup extends \Swow\Sync\WaitGroup
    {
    }
} else {
    // TODO: warning
}


