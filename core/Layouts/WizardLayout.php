<?php

namespace InertiaStudio\Layouts;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class WizardLayout implements JsonSerializable
{
    use HasSchema;

    /** @var array<WizardStep> */
    protected array $steps = [];

    protected bool $showStepNumbers = true;

    protected bool $allowSkip = false;

    /**
     * @param  array<WizardStep>  $steps
     */
    public function __construct(array $steps = [])
    {
        $this->steps = $steps;
    }

    /**
     * @param  array<WizardStep>  $steps
     */
    public function steps(array $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    public function showStepNumbers(bool $show = true): static
    {
        $this->showStepNumbers = $show;

        return $this;
    }

    public function allowSkip(bool $allow = true): static
    {
        $this->allowSkip = $allow;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'wizard',
            'showStepNumbers' => $this->showStepNumbers,
            'allowSkip' => $this->allowSkip,
            'steps' => array_map(fn (WizardStep $step) => $step->toArray(), $this->steps),
        ];
    }
}
