<?php

namespace spec\Http\Promise;

use Http\Promise\Promise;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FulfilledPromiseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('result');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Promise\FulfilledPromise');
    }

    function it_is_a_promise()
    {
        $this->shouldImplement('Http\Promise\Promise');
    }

    function it_returns_a_fulfilled_promise()
    {
        $promise = $this->then(function ($result) {
            return $result;
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\FulfilledPromise');
        $promise->getState()->shouldReturn(Promise::FULFILLED);
        $promise->wait()->shouldReturn('result');
    }

    function it_returns_a_rejected_promise()
    {
        $exception = new \Exception();

        $promise = $this->then(function () use ($exception) {
            throw $exception;
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\RejectedPromise');
        $promise->getState()->shouldReturn(Promise::REJECTED);
        $promise->shouldThrow($exception)->duringWait();
    }

    function it_returns_itself_when_no_on_fulfilled_callback_is_passed()
    {
        $this->then()->shouldReturn($this);
    }

    function it_is_in_fulfilled_state()
    {
        $this->getState()->shouldReturn(Promise::FULFILLED);
    }

    function it_has_a_result()
    {
        $this->wait()->shouldReturn('result');
    }

    function it_does_not_unwrap_a_value()
    {
        $this->wait(false)->shouldNotReturn('result');
    }
}
