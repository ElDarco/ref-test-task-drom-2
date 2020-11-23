<?php

declare(strict_types=1);

namespace UnitTest;

use ExampleComApiDataProvider\DTO\Comment;
use ExampleComApiDataProvider\Exceptions\InvalidCommentException;
use ExampleComApiDataProvider\Interfaces\CommentCollectionInterface;
use ExampleComApiDataProvider\Interfaces\CommentInterface;
use ExampleComApiDataProvider\ResponseHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseHandlerTest extends TestCase
{
    public function testParsedManyComment()
    {
        $response = $this->createStub(ResponseInterface::class);
        $responseBodyJson = '{"comments": [{"id": 1, "name": "some name", "text": "some text"}]}';
        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn($responseBodyJson);
        $response->method('getBody')->willReturn($responseBody);
        $responseHandler = new ResponseHandler();

        $parsedResponse = $responseHandler->handleManyComments($response);
        $this->assertInstanceOf(CommentCollectionInterface::class, $parsedResponse);
        foreach ($parsedResponse as $parsedComment) {
            $this->assertInstanceOf(CommentInterface::class, $parsedComment);
            $this->assertEquals('some name', $parsedComment->getName());
            $this->assertEquals('some text', $parsedComment->getText());
        }
    }
    public function testParsedManyThrowExceptionIfCommentDontHaveId()
    {
        $response = $this->createStub(ResponseInterface::class);
        $responseBodyJson = '{"comments": [{"name": "some name", "text": "some text"}]}';
        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn($responseBodyJson);
        $response->method('getBody')->willReturn($responseBody);
        $responseHandler = new ResponseHandler();

        $this->expectException(InvalidCommentException::class);
        $responseHandler->handleManyComments($response);
    }

    public function testParsedSingleComment()
    {
        $response = $this->createStub(ResponseInterface::class);
        $responseBodyJson = ' {"comment": {"id": 1, "name": "some name", "text": "some text"}}';
        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn($responseBodyJson);
        $response->method('getBody')->willReturn($responseBody);
        $responseHandler = new ResponseHandler();

        $comment = $responseHandler->handleSingleComment($response);

        $this->assertInstanceOf(CommentInterface::class, $comment);
        $this->assertEquals(1, $comment->getId());
        $this->assertEquals('some name', $comment->getName());
        $this->assertEquals('some text', $comment->getText());
    }
}