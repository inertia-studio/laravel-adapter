<?php

namespace InertiaStudio;

class StudioNotification
{
    protected string $title;

    protected ?string $body = null;

    protected ?string $icon = null;

    protected string $color = 'info';

    protected ?string $url = null;

    protected ?string $time = null;

    public static function make(string $title): static
    {
        $notification = new static;
        $notification->title = $title;

        return $notification;
    }

    public function body(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function time(string $time): static
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'color' => $this->color,
            'url' => $this->url,
            'time' => $this->time,
        ];
    }
}
