<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider\DTO;

use ExampleComApiDataProvider\DTO\Interfaces\CommentCollectionInterface;
use ExampleComApiDataProvider\DTO\Interfaces\CommentInterface;

/**
 * Class CommentCollection
 * Description: Данный класс реализовывает контейнер итератор для хранения
 * множества объектов реализующих CommentInterface
 * @package ExampleComApiDataProvider\DTO
 */
class CommentCollection implements CommentCollectionInterface
{
    /**
     * @var array
     */
    protected array $storage = [];

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->storage);
    }

    /**
     * @inheritDoc
     */
    public function add(CommentInterface $comment): CommentCollectionInterface
    {
        $this->storage[] = $comment;
    }
}