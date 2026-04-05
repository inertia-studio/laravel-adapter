<?php

namespace InertiaStudio\Fields;

class ImageUploadField extends FileUploadField
{
    protected string $type = 'imageUpload';

    protected ?string $imageCropAspectRatio = null;

    protected ?int $imageResizeTargetWidth = null;

    protected ?int $imageResizeTargetHeight = null;

    public function imageCropAspectRatio(string $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imageResizeTargetWidth(int $width): static
    {
        $this->imageResizeTargetWidth = $width;

        return $this;
    }

    public function imageResizeTargetHeight(int $height): static
    {
        $this->imageResizeTargetHeight = $height;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'imageCropAspectRatio' => $this->imageCropAspectRatio,
            'imageResizeTargetWidth' => $this->imageResizeTargetWidth,
            'imageResizeTargetHeight' => $this->imageResizeTargetHeight,
        ];
    }
}
