<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider\DTO;

use ExampleComApiDataProvider\DTO\Interfaces\CommentInterface;

/**
 * Class Comment
 * Description: Данный класс реализовывает интерефейс
 * описывающий тип данных для обмена
 * @package ExampleComApiDataProvider\DTO
 */
class Comment implements CommentInterface
{
    /**
     * @var int
     */
    protected int $id;
    /**
     * @var string
     */
    protected string $name;
    /**
     * @var string
     */
    protected string $text;

    /**
     * @inheritDoc
     */
    function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    function setId(int $id): CommentInterface
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    function setName(string $name): CommentInterface
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    function getText(): string
    {
        return $this->text;
    }

    /**
     * @inheritDoc
     */
    function setText(string $text): CommentInterface
    {
        $this->text = $text;
        return $this;
    }
}