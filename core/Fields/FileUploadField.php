<?php

namespace InertiaStudio\Fields;

class FileUploadField extends BaseField
{
    protected string $type = 'fileUpload';

    protected ?string $disk = null;

    protected ?string $directory = null;

    protected ?int $maxSize = null;

    /** @var string[]|null */
    protected ?array $acceptedFileTypes = null;

    protected bool $multiple = false;

    protected ?int $maxFiles = null;

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function maxSize(int $size): static
    {
        $this->maxSize = $size;

        return $this;
    }

    /** @param string[] $types */
    public function acceptedFileTypes(array $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function multiple(): static
    {
        $this->multiple = true;

        return $this;
    }

    public function maxFiles(int $count): static
    {
        $this->maxFiles = $count;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'disk' => $this->disk,
            'directory' => $this->directory,
            'maxSize' => $this->maxSize,
            'acceptedFileTypes' => $this->acceptedFileTypes,
            'multiple' => $this->multiple,
            'maxFiles' => $this->maxFiles,
        ];
    }
}
