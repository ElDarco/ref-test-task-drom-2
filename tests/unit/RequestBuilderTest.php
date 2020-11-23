<?php

declare(strict_types=1);

namespace UnitTest;

use ExampleComApiDataProvider\DTO\Comment;
use ExampleComApiDataProvider\Exceptions\InvalidCommentException;
use ExampleComApiDataProvider\RequestBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class RequestBuilderTest extends TestCase
{
    public function testPublishRequestIfCommentIsPublish()
    {
        $requestFactoryStub = $this
            ->createStub(RequestFactoryInterface::class);
        $streamFactoryStub = $this
            ->createStub(StreamFactoryInterface::class);
        $requestBuilder = new RequestBuilder(
            $requestFactoryStub,
            $streamFactoryStub,
            'http://example.com/'
        );
        $testComment = new Comment();
        $testComment->setId(1);
        $this->expectException(InvalidCommentException::class);
        $requestBuilder->createPublishCommentRequest($testComment);
    }

    public function testUpdateRequestIfCommentUnexpected()
    {
        $requestFactoryStub = $this
            ->createStub(RequestFactoryInterface::class);
        $streamFactoryStub = $this
            ->createStub(StreamFactoryInterface::class);
        $requestBuilder = new RequestBuilder(
            $requestFactoryStub,
            $streamFactoryStub,
            'http://example.com/'
        );
        $testComment = new Comment();
        $testComment->setText('some text');
        $testComment->setName('some name');
        $this->expectException(InvalidCommentException::class);
        $requestBuilder->createUpdateCommentRequest($testComment);
    }
}