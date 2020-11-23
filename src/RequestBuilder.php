<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider;

use ExampleComApiDataProvider\Interfaces\CommentInterface;
use ExampleComApiDataProvider\Exceptions\InvalidCommentException;
use ExampleComApiDataProvider\Interfaces\RequestBuilderInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class DataProvider
 * Description: Данный класс описывает реализуем методы по подготовке запросов к сервису
 * @package ExampleComApiDataProvider
 */
class RequestBuilder implements RequestBuilderInterface
{
    /**
     * @var RequestFactoryInterface
     */
    protected RequestFactoryInterface $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    protected StreamFactoryInterface $streamFactory;

    /**
     * @var string
     */
    protected string $baseUrl;

    /**
     * DataProvider constructor.
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param string $baseUrl
     */
    public function __construct(
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $baseUrl
    ) {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Description: Метод нормализует url возвращая его без закрывающего слеша
     * @return string
     */
    protected function getBaseUrlWithoutTS(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    /**
     * @return RequestInterface
     */
    public function createGetCommentsRequest(): RequestInterface
    {
        return $this->requestFactory->createRequest(
            'GET',
            $this->getBaseUrlWithoutTS() . '/comments'
        );
    }

    /**
     * @param CommentInterface $comment
     * @return RequestInterface
     * @throws InvalidCommentException
     */
    public function createPublishCommentRequest(CommentInterface $comment): RequestInterface
    {
        if ($comment->getId()) {
            throw new InvalidCommentException(
                'Comment No' . $comment->getId() . 'already posted'
            );
        }
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->getBaseUrlWithoutTS() . '/comment'
        );
        $bodyRequest = [
            'name' => $comment->getName(),
            'text' => $comment->getText(),
        ];
        $streamBody = $this->streamFactory->createStream(json_encode($bodyRequest));
        return $request->withBody($streamBody);
    }

    /**
     * @param CommentInterface $comment
     * @return RequestInterface
     * @throws InvalidCommentException
     */
    public function createUpdateCommentRequest(CommentInterface $comment): RequestInterface
    {
        if (!$comment->getId()) {
            throw new InvalidCommentException(
                'Can not edit comment. No identifier specified'
            );
        }
        $request = $this->requestFactory->createRequest(
            'PATCH',
            $this->getBaseUrlWithoutTS() . '/comment/' . $comment->getId()
        );
        $bodyRequest = [
            'name' => $comment->getName(),
            'text' => $comment->getText(),
        ];
        $streamBody = $this->streamFactory->createStream(json_encode($bodyRequest));
        return $request->withBody($streamBody);
    }
}