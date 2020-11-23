<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider;

use ExampleComApiDataProvider\Interfaces\CommentCollectionInterface;
use ExampleComApiDataProvider\Interfaces\CommentInterface;
use ExampleComApiDataProvider\Exceptions\InvalidCommentException;
use ExampleComApiDataProvider\Interfaces\RequestBuilderInterface;
use ExampleComApiDataProvider\Interfaces\ResponseHandlerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

/**
 * Class DataProvider
 * Description: Класс провайдера данных для сервиса example.com
 * осуществляющий подготовку запроса, транспорт, и обработку ответа
 * для отделения бизнес-логики от технической части приложения
 * @package ExampleComApiDataProvider
 */
class DataProvider
{
    /**
     * @var ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * @var RequestBuilderInterface
     */
    protected RequestBuilderInterface $requestBuilder;

    /**
     * @var ResponseHandlerInterface
     */
    protected ResponseHandlerInterface $responseHandler;

    /**
     * DataProvider constructor.
     * @param ClientInterface $httpClient
     * @param RequestBuilderInterface $requestBuilder
     * @param ResponseHandlerInterface $responseHandler
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestBuilderInterface $requestBuilder,
        ResponseHandlerInterface $responseHandler
    ) {
        $this->httpClient = $httpClient;
        $this->requestBuilder = $requestBuilder;
        $this->responseHandler = $responseHandler;
    }

    /**
     * Description: Данный метод запрашивает комментрии и возвращает в виде коллекции
     * @return CommentCollectionInterface
     * @throws ClientExceptionInterface
     */
    public function getComments(): CommentCollectionInterface
    {
        //TODO Добавить кэширование и логирование
        $request = $this->requestBuilder->createGetCommentsRequest();
        $response = $this->httpClient->sendRequest($request);
        return $this->responseHandler->handleManyComments($response);
    }

    /**
     * Description: Данный метод отправляет запрос на публикцию комментария
     * @param CommentInterface $comment
     * @return CommentInterface
     * @throws ClientExceptionInterface
     * @throws InvalidCommentException
     */
    public function publishComment(CommentInterface $comment)
    {
        //TODO Добавить кэширование и логирование
        $request = $this->requestBuilder->createPublishCommentRequest($comment);
        $response = $this->httpClient->sendRequest($request);
        return $this->responseHandler->handleSingleComment($response, $comment);
    }

    /**
     * Description: Данный метод отправляет запрос на публикцию комментария
     * @param CommentInterface $comment
     * @return CommentInterface
     * @throws ClientExceptionInterface
     * @throws InvalidCommentException
     */
    public function updateComment(CommentInterface $comment)
    {
        //TODO Добавить кэширование и логирование
        $request = $this->requestBuilder->createUpdateCommentRequest($comment);
        $response = $this->httpClient->sendRequest($request);
        return $this->responseHandler->handleSingleComment($response, $comment);
    }
}