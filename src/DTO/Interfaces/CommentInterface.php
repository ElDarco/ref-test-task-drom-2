<?php

declare(strict_types=1);

namespace ExampleComApiDataProvider\DTO\Interfaces;

/**
 * Interface CommentInterface
 * Description: Данный интерфейс описывает тип данных для обмена
 * @package ExampleComApiDataProvider\DTO\Interfaces
 */
interface CommentInterface
{
    /**
     * @return int
     */
    function getId(): int;

    /**
     * @param int $id
     * @return $this
     */
    function setId(int $id): self;

    /**
     * @return string
     */
    function getName(): string;

    /**
     * @param string $name
     * @return $this
     */
    function setName(string $name): self;

    /**
     * @return string
     */
    function getText(): string;

    /**
     * @param string $text
     * @return $this
     */
    function setText(string $text): self;
}