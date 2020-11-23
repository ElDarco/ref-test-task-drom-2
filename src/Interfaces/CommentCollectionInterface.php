<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider\Interfaces;

/**
 * Interface CommentCollectionInterface
 * Description: Данный интерфейс описывает контейнер итератор для транспорта
 * множества объектов реализующих CommentInterface
 * @package ExampleComApiDataProvider\DTO\Interfaces
 */
interface CommentCollectionInterface extends \IteratorAggregate
{
    /**
     * @param CommentInterface $comment
     * @return $this
     */
    public function add(CommentInterface $comment): self;
}