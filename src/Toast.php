<?php

declare(strict_types=1);

namespace Ketchalegend\LaravelGoeyToast;

final readonly class Toast
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public string $id,
        public string $type,
        public string $message,
        public ?string $title,
        public ?string $description,
        /** @var array<string, mixed>|null */
        public ?array $action,
        public ?bool $spring,
        public int $duration,
        public bool $dismissible,
        public array $meta = [],
    ) {}

    /**
     * @return array{
     *     id: string,
     *     type: string,
     *     message: string,
     *     title: ?string,
     *     description: ?string,
     *     action: array<string, mixed>|null,
     *     spring: ?bool,
     *     duration: int,
     *     dismissible: bool,
     *     meta: array<string, mixed>
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'message' => $this->message,
            'title' => $this->title,
            'description' => $this->description,
            'action' => $this->action,
            'spring' => $this->spring,
            'duration' => $this->duration,
            'dismissible' => $this->dismissible,
            'meta' => $this->meta,
        ];
    }
}
