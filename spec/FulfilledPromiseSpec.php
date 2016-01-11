<?php

namespace spec\Http\Promise;

use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FulfilledPromiseSpec extends ObjectBehavior
{
    function let(ResponseInterface $response)
    {
        $this->beConstructedWith($response);
    }

    function it_is_initializable(ResponseInterface $response)
    {
        $this->shouldHaveType('Http\Promise\FulfilledPromise');
    }

    function it_is_a_promise()
    {
        $this->shouldImplement('Http\Promise\Promise');
    }

    function it_returns_a_fulfilled_promise(ResponseInterface $response)
    {
        $promise = $this->then(function (ResponseInterface $responseReceived) use ($response) {
            if (Argument::is($responseReceived)->scoreArgument($response->getWrappedObject())) {
                return $response->getWrappedObject();
            }
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\FulfilledPromise');
        $promise->getState()->shouldReturn(Promise::FULFILLED);
        $promise->wait()->shouldReturn($response);
    }

    function it_returns_a_rejected_promise(RequestInterface $request, ResponseInterface $response)
    {
        $exception = new \Exception();

        $promise = $this->then(function (ResponseInterface $responseReceived) use ($response, $exception) {
            if (Argument::is($responseReceived)->scoreArgument($response->getWrappedObject())) {
                throw $exception;
            }
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\RejectedPromise');
        $promise->getState()->shouldReturn(Promise::REJECTED);
        $promise->shouldThrow($exception)->duringWait();
    }

    function it_is_in_fulfilled_state()
    {
        $this->getState()->shouldReturn(Promise::FULFILLED);
    }

    function it_has_a_response(ResponseInterface $response)
    {
        $this->wait()->shouldReturn($response);
    }

    function it_does_not_unwrap_a_value(ResponseInterface $response)
    {
        $this->wait(false)->shouldNotReturn($response);
    }
}
