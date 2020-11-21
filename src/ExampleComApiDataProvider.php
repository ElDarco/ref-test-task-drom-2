<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider;

use ExampleComApiDataProvider\DTO\Comment;
use ExampleComApiDataProvider\DTO\CommentCollection;
use ExampleComApiDataProvider\DTO\Interfaces\CommentCollectionInterface;
use ExampleComApiDataProvider\DTO\Interfaces\CommentInterface;
use ExampleComApiDataProvider\Exceptions\InvalidCommentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class ExampleComApiDataProvider
 * Description: Класс провайдера данных для сервиса example.com
 * осуществляющий подготовку запроса, транспорт, и обработку ответа
 * для отделения бизнес-логики от технической части приложения
 * @package ExampleComApiDataProvider
 */
class ExampleComApiDataProvider
{
    /**
     * @var ClientInterface
     */
    protected ClientInterface $httpClient;

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
     * ExampleComApiDataProvider constructor.
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param string $baseUrl
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $baseUrl
    ) {
        $this->httpClient = $httpClient;
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
    protected function createGetCommentRequest(): RequestInterface
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
    protected function createPublishCommentRequest(
        CommentInterface $comment
    ): RequestInterface {
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
    protected function createUpdateCommentRequest(
        CommentInterface $comment
    ): RequestInterface {
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

    /**
     * Description: Данный метод парсит ответ от веб-сервиса преобразуя его в объект CommentCollection
     * Метод ожидает что ответ от веб-сервиса будет иметь вид:
     * {"comments": [{"id": number, "name": string, "text": string}]}
     * @param ResponseInterface $response
     * @return CommentCollectionInterface
     * @throws InvalidCommentException
     */
    protected function handleGetCommentResponse(
        ResponseInterface $response
    ): CommentCollectionInterface {
        $commentCollection = new CommentCollection();
        $responseBody = json_decode($response->getBody()->getContents());
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
     * Description: Данный метод парсит ответ от веб-сервиса преобразуя его в объект Comment
     * Метод ожидает что ответ от веб-сервиса будет иметь вид:
     * {"comment": {"id": number, "name": string, "text": string}}
     * @param ResponseInterface $response
     * @param CommentInterface $comment
     * @return CommentInterface
     * @throws InvalidCommentException
     */
    protected function handlePublishCommentResponse(
        ResponseInterface $response,
        CommentInterface $comment
    ): CommentInterface {
        $responseBody = json_decode($response->getBody()->getContents());
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

    /**
     * Description: Данный метод парсит ответ от веб-сервиса преобразуя его в объект Comment
     * Метод ожидает что ответ от веб-сервиса будет иметь вид:
     * {"comment": {"id": number, "name": string, "text": string}}
     * @param ResponseInterface $response
     * @param CommentInterface $comment
     * @return CommentInterface
     * @throws InvalidCommentException
     */
    protected function handleUpdateCommentResponse(
        ResponseInterface $response,
        CommentInterface $comment
    ): CommentInterface {
        return $this->handlePublishCommentResponse($response, $comment);
    }

    /**
     * Description: Данный метод запрашивает комментрии и возвращает в виде коллекции
     * @return CommentCollectionInterface
     * @throws ClientExceptionInterface|InvalidCommentException
     */
    public function getComments(): CommentCollectionInterface
    {
        //TODO Добавить кэширование и логирование
        $request = $this->createGetCommentRequest();
        $response = $this->httpClient->sendRequest($request);
        return $this->handleGetCommentResponse($response);
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
        $request = $this->createPublishCommentRequest($comment);
        $response = $this->httpClient->sendRequest($request);
        return $this->handlePublishCommentResponse($response, $comment);
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
        $request = $this->createUpdateCommentRequest($comment);
        $response = $this->httpClient->sendRequest($request);
        return $this->handleUpdateCommentResponse($response, $comment);
    }
}