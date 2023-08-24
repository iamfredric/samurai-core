<?php

namespace Boil\Acf;

abstract class AcfOptionsPage
{
    abstract public function id(): string;

    abstract public function title(): string;

    public function menuTitle(): ?string
    {
        return null;
    }

    public function parentSlug(): ?string
    {
        return null;
    }

    public function capability(): string
    {
        return 'edit_posts';
    }

    public function iconUrl(): string
    {
        return '';
    }

    public function redirect(): bool
    {
        return false;
    }

    public function autoload(): bool
    {
        return false;
    }

    public function updateButtonLabel(): ?string
    {
        return null;
    }

    public function updateMessage(): ?string
    {
        return null;
    }

    public function slug(): ?string
    {
        return null;
    }

    public function position(): ?int
    {
        return null;
    }

    public function share(): ?callable
    {
        return null;
    }
}
