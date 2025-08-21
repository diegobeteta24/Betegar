<?php

namespace App\Services\Menu;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Representa un enlace simple en el sidebar.
 */
class MenuLink implements MenuElement, Htmlable
{
    protected string $title;
    protected string $icon;
    protected string $route;

    public function __construct(string $title, string $icon, string $route)
    {
        $this->title = $title;
        $this->icon  = $icon;
        $this->route = $route;
    }

    /**
     * Renderiza la vista parcial de enlace (sin <li>).
     */
    public function render(): string
    {
        return view('layouts.includes.admin.menu-link', [
            'title' => $this->title,
            'icon'  => $this->icon,
            'route' => $this->route,
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

    /**
     * Devuelve el nombre de la ruta para detectar activo en MenuGroup.
     */
    public function getRoute(): string
    {
        return $this->route;
    }
}
