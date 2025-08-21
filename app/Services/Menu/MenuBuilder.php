<?php

namespace App\Services\Menu;

/**
 * Builder fluido para construir el menú.
 */
class MenuBuilder
{
    /** @var MenuElement[] */
    protected array $items = [];

    /**
     * Inicia un nuevo builder.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Añade un encabezado.
     */
    public function header(string $title): self
    {
        $this->items[] = new MenuHeader($title);
        return $this;
    }

    /**
     * Añade un enlace simple.
     */
    public function link(string $title, string $icon, string $route): self
    {
        $this->items[] = new MenuLink($title, $icon, $route);
        return $this;
    }

    /**
     * Añade un grupo (sub-menú). Recibe un callback para poblar los children.
     */
    public function group(string $title, string $icon, callable $callback): self
    {
        $childBuilder = new self();
        $callback($childBuilder);
        $children = $childBuilder->getItems();
        $this->items[] = new MenuGroup($title, $icon, $children);
        return $this;
    }

    /**
     * Devuelve los elementos acumulados.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Alias de getItems().
     */
    public function build(): array
    {
        return $this->getItems();
    }
}
