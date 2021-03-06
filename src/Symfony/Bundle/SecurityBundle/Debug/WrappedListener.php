<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\Debug;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\VarDumper\Caster\ClassStub;

/**
 * Wraps a security listener for calls record.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 *
 * @internal
 */
final class WrappedListener
{
    use TraceableListenerTrait;

    public function __construct(callable $listener)
    {
        $this->listener = $listener;
    }

    public function __invoke(RequestEvent $event)
    {
        $startTime = microtime(true);
        ($this->listener)($event);
        $this->time = microtime(true) - $startTime;
        $this->response = $event->getResponse();
    }

    public function getInfo(): array
    {
        return [
            'response' => $this->response,
            'time' => $this->time,
            'stub' => $this->stub ?? $this->stub = ClassStub::wrapCallable($this->listener),
        ];
    }
}
