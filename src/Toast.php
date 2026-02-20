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
        public int $duration,
        public bool $dismissible,
        public array $meta = [],
    ) {}

    /**
     * @return array{
     *     id: string,
     *     type: string,
     *     message: string,
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
            'duration' => $this->duration,
            'dismissible' => $this->dismissible,
            'meta' => $this->meta,
        ];
    }
}
