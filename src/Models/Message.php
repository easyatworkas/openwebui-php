<?php

namespace EasyAtWork\OpenWebUi\Models;

class Message extends Model
{
    const ROLE_USER = 'user';
    const ROLE_ASSISTANT = 'assistant';

    /** @var string Sent by "user" or "assistant". */
    protected $role;

    /** @var string Message content. */
    protected $content;

    /**
     * @param string $role See ROLE_* constants.
     * @return $this
     */
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string See ROLE_* constants.
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $content Message content.
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string Message content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'role' => $this->getRole(),
            'content' => $this->getContent(),
        ];
    }
}
