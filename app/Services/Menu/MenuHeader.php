<?php

namespace App\Services\Menu;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Representa un encabezado de sección en el sidebar.
 */
class MenuHeader implements MenuElement, Htmlable
{
    protected string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * Renderiza la vista parcial de header (sin <li>).
     */
    public function render(): string
    {
        return view('layouts.includes.admin.menu-header', [
            'title' => $this->title,
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
