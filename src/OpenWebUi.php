<?php

namespace EasyAtWork\OpenWebUi;

use Exception;
use EasyAtWork\OpenWebUi\Models\Chat;
use EasyAtWork\OpenWebUi\Models\Message;
use EasyAtWork\OpenWebUi\Traits\StaticConstructor;
use EasyAtWork\OpenWebUi\Traits\UnsafeOptions;

/**
 * @see ApiClient
 * @method string signin(string $email, string $password)
 * @method ApiClient useAuthorizationToken(string $token)
 */
class OpenWebUi
{
    use StaticConstructor;
    use UnsafeOptions;

    const OPTION_CLIENT_OPTIONS = 'client';

    /** @var ApiClient */
    protected $client;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->client = ApiClient::create($options[static::OPTION_CLIENT_OPTIONS] ?? []);

        unset($options[static::OPTION_CLIENT_OPTIONS]);

        $this->absorbOptions($options);
    }

    /**
     * @return ApiClient
     */
    public function getClient(): ApiClient
    {
        return $this->client;
    }

    /**
     * @param bool $includeHidden
     * @return array<string, string> ID/name pairs.
     * @throws Exception
     */
    public function getModels(bool $includeHidden = false): array
    {
        $models = [];

        foreach ($this->getClient()->models() as $model) {
            if (!$includeHidden && ($model['info']['meta']['hidden'] ?? false)) {
                continue;
            }

            $models[$model['id']] = $model['name'];
        }

        return $models;
    }

    /**
     * @param string $id
     * @return array
     * @throws Exception
     */
    public function getModel(string $id): ?array
    {
        foreach ($this->getClient()->models() as $model) {
            if ($model['id'] == $id) {
                return $model;
            }
        }

        return null;
    }

    /**
     * @param string $modelId
     * @return Chat Context.
     * @throws Exception
     */
    public function newChat(string $modelId): Chat
    {
        $model = $this->getModel($modelId);

        if (!$model) {
            throw new Exception('Unknown model.');
        }

        return Chat::create()
            ->setModel($model['id'])
            ->setFiles($model['info']['meta']['knowledge'] ?? []);
    }

    /**
     * @param Chat $chat Context.
     * @param string $content Message from user.
     * @return string Response.
     * @throws Exception
     */
    public function sendMessage(Chat $chat, string $content): string
    {
        $userMessage = Message::create()
            ->setRole(Message::ROLE_USER)
            ->setContent($this->trim($content));

        $chat->addMessage($userMessage);

        $response = $this->getClient()->processChat([
            'model' => $chat->getModel(),
            'files' => $chat->getFiles(),
            'messages' => $chat->getMessages(),
            'stream' => false,
        ]);

        $content = $this->trim($response['message']['content']);

        $assistantMessage = Message::create()
            ->setRole(Message::ROLE_ASSISTANT)
            ->setContent($content);

        $chat->addMessage($assistantMessage);

        $response = $this->getClient()->postProcessChat([
            'chat_id' => null,
            'session_id' => null,
            'id' => null,
            'model' => $chat->getModel(),
            'messages' => [
                $chat->getLastMessage(),
            ],
        ]);

        $content = $this->trim(end($response['messages'])['content']);

        return $this->trim($content);
    }

    /**
     * @param string $content Content to trim.
     * @return string Trimmed content.
     */
    protected function trim(string $content): string
    {
        do {
            $content = preg_replace('/(?:^\s+|\s+$|(\s)\s+|\n\W|(\W){2,}$)/', '$1$2', $content, -1, $count);
        } while ($count);

        return $content;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->getClient()->{$method}(... $arguments);
    }
}
