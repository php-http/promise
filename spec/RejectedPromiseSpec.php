<?php

namespace spec\Http\Promise;

use Http\Promise\Promise;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RejectedPromiseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \Exception());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Promise\RejectedPromise');
    }

    function it_is_a_promise()
    {
        $this->shouldImplement('Http\Promise\Promise');
    }

    function it_returns_a_fulfilled_promise()
    {
        $exception = new \Exception();
        $this->beConstructedWith($exception);

        $promise = $this->then(null, function (\Exception $exceptionReceived) use($exception) {
            return 'result';
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\FulfilledPromise');
        $promise->getState()->shouldReturn(Promise::FULFILLED);
        $promise->wait()->shouldReturn('result');
    }

    function it_returns_a_rejected_promise()
    {
        $exception = new \Exception();
        $this->beConstructedWith($exception);

        $promise = $this->then(null, function (\Exception $exceptionReceived) use($exception) {
            if (Argument::is($exceptionReceived)->scoreArgument($exception)) {
                throw $exception;
            }
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\RejectedPromise');
        $promise->getState()->shouldReturn(Promise::REJECTED);
        $promise->shouldThrow($exception)->duringWait();
    }

    function it_returns_itself_when_no_on_rejected_callback_is_passed()
    {
        $this->then()->shouldReturn($this);
    }

    function it_is_in_rejected_state()
    {
        $this->getState()->shouldReturn(Promise::REJECTED);
    }

    function it_returns_an_exception()
    {
        $exception = new \Exception();

        $this->beConstructedWith($exception);
        $this->shouldThrow($exception)->duringWait();
    }

    function it_does_not_unwrap_a_value()
    {
        $this->shouldNotThrow('Exception')->duringWait(false);
    }
}
