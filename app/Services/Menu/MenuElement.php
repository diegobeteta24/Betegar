<?php
// app/Services/Menu/MenuElement.php
namespace App\Services\Menu;

interface MenuElement
{
    /** 
     * Renderiza este elemento como HTML 
     */
    public function render(): string;

    /**
     * Controla si el usuario puede verlo
     */
    public function authorize(): bool;
}
