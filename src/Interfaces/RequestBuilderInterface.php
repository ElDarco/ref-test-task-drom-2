<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider\Interfaces;

use ExampleComApiDataProvider\Exceptions\InvalidCommentException;
use Psr\Http\Message\RequestInterface;

/**
 * Interface RequestBuilderInterface
 * Description: Данный интерфейс описывает основные методы подготовки запросов к сервису
 * @package ExampleComApiDataProvider\Interfaces
 */
interface RequestBuilderInterface
{
    /**
     * Description: Данный метод должен возвращать запрос на получение списка коментариев
     * @return RequestInterface
     */
    public function createGetCommentsRequest(): RequestInterface;

    /**
     * Description: Данный метод должен возвращать запрос на создание коментария
     * @param CommentInterface $comment
     * @return RequestInterface
     * @throws InvalidCommentException
     */
    public function createPublishCommentRequest(CommentInterface $comment): RequestInterface;

    /**
     * Description: Данный метод должен возвращать запрос на обновление коментария
     * @param CommentInterface $comment
     * @return RequestInterface
     * @throws InvalidCommentException
     */
    public function createUpdateCommentRequest(CommentInterface $comment): RequestInterface;
}