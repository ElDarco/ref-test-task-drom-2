<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider\Interfaces;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface ResponseHandlerInterface
 * Description: Данный интерфейс описывает основные методы по разбору ответа
 * @package ExampleComApiDataProvider\Interfaces
 */
interface ResponseHandlerInterface
{
    /**
     * Description: Данный метод должен реализовать обработку ответа
     * возвращающий множество Comment
     * @param ResponseInterface $response
     * @return CommentCollectionInterface
     */
    public function handleManyComments(
        ResponseInterface $response
    ): CommentCollectionInterface;

    /**
     * Description: Данный метод должен реализовать обработку ответа
     * модифицируя переданный Comment
     * @param ResponseInterface $response
     * @param CommentInterface $comment
     * @return CommentInterface
     */
    public function handleSingleComment(
        ResponseInterface $response,
        CommentInterface $comment
    ): CommentInterface;
}