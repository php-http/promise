<?php

namespace Http\Promise;

/**
 * A promise already fulfilled.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 *
 * @template-covariant T = mixed
 *
 * @implements Promise<T>
 */
final class FulfilledPromise implements Promise
{
    /**
     * @var T
     */
    private $result;

    /**
     * @param T $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return ($onFulfilled is null ? self<T> : Promise<mixed>)
     */
    public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
    {
        if (null === $onFulfilled) {
            return $this;
        }

        try {
            return new self($onFulfilled($this->result));
        } catch (\Exception $e) {
            return new RejectedPromise($e);
        }
    }

    public function getState()
    {
        return Promise::FULFILLED;
    }

    public function wait($unwrap = true)
    {
        if ($unwrap) {
            return $this->result;
        }

        return null;
    }
}
