<?php

namespace App\Services\MenuT;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Representa un grupo de enlaces (sub-menú) en el sidebar.
 */
class MenuGroup implements MenuElement, Htmlable
{
    protected string $title;
    protected string $icon;
    /** @var MenuLink[] */
    protected array $children;

    public function __construct(string $title, string $icon, array $children)
    {
        $this->title    = $title;
        $this->icon     = $icon;
        $this->children = $children;
    }

    /**
     * Renderiza la vista parcial de grupo (sin <li>).
     */
    public function render(): string
    {
        return view('layouts.includes.app.menu-group', [
            'title'    => $this->title,
            'icon'     => $this->icon,
            'children' => $this->children,
        ])->render();
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
