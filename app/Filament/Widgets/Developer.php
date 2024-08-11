<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class Developer extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected static string $view = 'filament.widgets.developer';
    protected int | string | array $columnSpan = 1;
    protected int | string | array $columnStart = 2;
}
