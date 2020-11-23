<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider;

use ExampleComApiDataProvider\DTO\Comment;
use ExampleComApiDataProvider\DTO\CommentCollection;
use ExampleComApiDataProvider\Interfaces\CommentCollectionInterface;
use ExampleComApiDataProvider\Interfaces\CommentInterface;
use ExampleComApiDataProvider\Exceptions\InvalidCommentException;
use ExampleComApiDataProvider\Interfaces\ResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseHandler
 * Description: Данный класс реализует логику ResponseHandlerInterface
 * @package ExampleComApiDataProvider
 */
class ResponseHandler implements ResponseHandlerInterface
{
    /**
     * Description: Данный метод реализует интерфейс обработки ответа с множеством комментариев
     * Метод ожидает что ответ от веб-сервиса будет иметь вид:
     * {"comments": [{"id": number, "name": string, "text": string}]}
     * @param ResponseInterface $response
     * @return CommentCollectionInterface
     * @throws InvalidCommentException
     */
    public function handleManyComments(
        ResponseInterface $response
    ): CommentCollectionInterface {
        $commentCollection = new CommentCollection();
        $responseBody = json_decode($response->getBody()->getContents(), true);
        if (array_key_exists('comments', $responseBody)) {
            foreach ($responseBody['comments'] as $rawComment) {
                if (!array_key_exists('id', $rawComment)) {
                    throw new InvalidCommentException(
                        'Unprocessed comment. Key "id" not found'
                    );
                }
                $comment = new Comment();
                $comment
                    ->setId((int) $rawComment['id'])
                    ->setName($rawComment['name'] ?? '')
                    ->setText($rawComment['text'] ?? '');
                $commentCollection->add($comment);
            }
        }
        return $commentCollection;
    }

    /**
     * Description: Данный метод реализует интерфейс обработки ответа с единичным комментарием
     * Метод ожидает что ответ от веб-сервиса будет иметь вид:
     * {"comment": {"id": number, "name": string, "text": string}}
     * @param ResponseInterface $response
     * @param CommentInterface $comment
     * @return CommentInterface
     * @throws InvalidCommentException
     */
    public function handleSingleComment(
        ResponseInterface $response,
        CommentInterface $comment
    ): CommentInterface {
        $responseBody = json_decode($response->getBody()->getContents(), true);
        if (array_key_exists('comment', $responseBody)) {
            if (array_key_exists('id', $responseBody['comment'])) {
                $comment
                    ->setId((int) $responseBody['id'])
                    ->setName($responseBody['name'] ?? '')
                    ->setText($responseBody['text'] ?? '');
                return $comment;
            }
            throw new InvalidCommentException(
                'Unprocessed comment. Key "id" not found'
            );
        }
        throw new InvalidCommentException(
            'Unprocessed comment. Document "comment" not found'
        );
    }
}