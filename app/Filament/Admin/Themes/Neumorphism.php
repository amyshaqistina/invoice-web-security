<?php

namespace App\Filament\Admin\Themes;

use Filament\Panel;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;
use Hasnayeen\Themes\Contracts\Theme;
use Filament\Support\Colors\Color;

class Neumorphism implements CanModifyPanelConfig, Theme
{
    public static function getName(): string
    {
        return 'neumorphism';
    }

    public static function getPath(): string
    {
        return 'resources/css/filament/admin/themes/neumorphism.css';
    }

    public function getThemeColor(): array
    {
        return Color::all();
    }

    public function modifyPanelConfig(Panel $panel): Panel
    {
        return $panel
            ->viteTheme($this->getPath());
    }
}
