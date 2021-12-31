<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TodoListRepository::class)
 */
class TodoList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $todoId;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $estimated;

    /**
     * @ORM\Column(type="string")
     */
    private $hash;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTodoId(): ?string
    {
        return $this->todoId;
    }

    public function setTodoId(string $todoId): self
    {
        $this->todoId = $todoId;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getEstimated(): ?int
    {
        return $this->estimated;
    }

    public function setEstimated(int $estimated): self
    {
        $this->estimated = $estimated;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }
}
