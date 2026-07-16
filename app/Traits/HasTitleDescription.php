<?php

namespace App\Traits;

/**
 * Trait to provide common title and description handling for models.
 */
trait HasTitleDescription
{
    /**
     * Get the title attribute.
     */
    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    /**
     * Set the title attribute.
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the description attribute.
     */
    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    /**
     * Set the description attribute.
     */
    public function setDescription(?string $description = null)
    {
        $this->description = $description;
        return $this;
    }
}
