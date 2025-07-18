<?php
// app/Services/Menu/MenuHeader.php
namespace App\Services\Menu;

use Illuminate\Contracts\Support\Htmlable;

class MenuHeader implements MenuElement, Htmlable
{
    protected string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * Devuelve el <li> completo con el título.
     */
    public function render(): string
    {
        return view('layouts.includes.admin.menu-header', [
            'title' => $this->title,
        ])->render();
    }

    /** Para que {!! $link !!} también funcione */
    public function toHtml(): string
    {
        return $this->render();
    }

    /** Control de permisos (siempre true en este ejemplo) */
    public function authorize(): bool
    {
        return true;
    }

    /** Permite castear a string */
    public function __toString(): string
    {
        return $this->render();
    }
}
