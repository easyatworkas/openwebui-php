<?php

namespace EasyAtWork\OpenWebUi\Models;

class Chat extends Model
{
    /** @var string */
    protected $model;

    /** @var string[] From the model's "knowledge" metadata. */
    protected $files;

    /** @var Message[] The chat messages. */
    protected $messages = [];

    /**
     * @param string $model Model ID.
     * @return $this
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string Model ID.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param array $files
     * @return $this
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param Message[] $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @param Message $message
     * @return $this
     */
    public function addMessage(Message $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return Message|null
     */
    public function getLastMessage()
    {
        return end($this->messages);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'messages' => $this->getMessages(),
        ];
    }
}
