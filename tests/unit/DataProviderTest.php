<?php

declare(strict_types=1);

namespace UnitTest;

use ExampleComApiDataProvider\DataProvider;
use ExampleComApiDataProvider\DTO\Comment;
use ExampleComApiDataProvider\Interfaces\RequestBuilderInterface;
use ExampleComApiDataProvider\Interfaces\ResponseHandlerInterface;
use ExampleComApiDataProvider\RequestBuilder;
use ExampleComApiDataProvider\ResponseHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class DataProviderTest extends TestCase
{
    function testPublishComment()
    {
        $comment = new Comment();
        $comment
            ->setName('some name')
            ->setText('some text');

        $expectedComment = new Comment();
        $expectedComment
            ->setId(1)
            ->setName('some name')
            ->setText('some text');

        $requestBuilder = $this->createStub(RequestBuilderInterface::class);
        $request = $this->createStub(RequestInterface::class);
        $requestBuilder->method('createPublishCommentRequest')->willReturn($request);

        $responseHandler = $this->createStub(ResponseHandlerInterface::class);
        $responseHandler->method('handleSingleComment')->willReturn($expectedComment);

        $httpClientStub = $this
            ->createStub(ClientInterface::class);


        $dataProvider = new DataProvider($httpClientStub, $requestBuilder, $responseHandler);
        $resultComment = $dataProvider->publishComment(clone $comment);


        $this->assertEquals($comment->getName(), $resultComment->getName());
        $this->assertEquals($comment->getText(), $resultComment->getText());
        $this->assertEquals(1, $resultComment->getId());
    }
}