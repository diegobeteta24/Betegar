<?php

namespace App\Services\Menu;

/**
 * Interfaz base para todos los elementos del menú.
 */
interface MenuElement
{
    /**
     * Devuelve el HTML a renderizar.
     */
    public function render(): string;

    /**
     * Controla si el elemento se muestra o no.
     */
    public function authorize(): bool;
}
