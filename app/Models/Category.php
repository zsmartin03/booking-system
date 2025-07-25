<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Category extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'color'];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the translated name for this category
     */
    public function getNameAttribute()
    {
        return __("categories.{$this->slug}.name", [], app()->getLocale()) ?: $this->slug;
    }

    /**
     * Get the translated description for this category
     */
    public function getTranslatedDescriptionAttribute()
    {
        return __("categories.{$this->slug}.description", [], app()->getLocale()) ?: $this->slug;
    }

    /**
     * Check if translations exist for this category
     */
    public function hasTranslations()
    {
        return \Illuminate\Support\Facades\Lang::has("categories.{$this->slug}");
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class);
    }

    /**
     * Generate badge styling using the category's color
     */
    public function getBadgeStyleAttribute()
    {
        $color = $this->color ?? '#8B5CF6';

        return [
            'background' => "linear-gradient(135deg, {$color}20, {$color}10)",
            'color' => $color,
            'border-color' => $color . '50',
            'hover-background' => "linear-gradient(135deg, {$color}30, {$color}20)"
        ];
    }

    /**
     * Get inline CSS for badge styling
     */
    public function getBadgeCssAttribute()
    {
        $color = $this->color ?? '#8B5CF6';

        return "
            background: linear-gradient(135deg, {$color}20, {$color}10);
            color: {$color};
            border-color: {$color}50;
        ";
    }

    /**
     * Get hover CSS for badge styling
     */
    public function getBadgeHoverCssAttribute()
    {
        $color = $this->color ?? '#8B5CF6';

        return "background: linear-gradient(135deg, {$color}30, {$color}20);";
    }

    /**
     * Convert hex color to CSS custom properties for dynamic styling
     */
    public function getTailwindClassesAttribute()
    {
        $color = $this->color ?? '#8B5CF6';

        $rgb = $this->hexToRgb($color);

        return [
            'color' => $color,
            'rgb' => $rgb,
            'style' => $this->generateInlineStyles($color, $rgb)
        ];
    }

    /**
     * Convert hex to RGB values
     */
    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Generate inline styles for dynamic coloring
     */
    private function generateInlineStyles($color, $rgb)
    {
        $r = $rgb['r'];
        $g = $rgb['g'];
        $b = $rgb['b'];

        $hsl = $this->rgbToHsl($r, $g, $b);
        $complementaryHsl = [
            'h' => ($hsl['h'] + 30) % 360,
            's' => max(20, $hsl['s'] - 10),
            'l' => min(70, $hsl['l'] + 10)
        ];
        $complementaryRgb = $this->hslToRgb($complementaryHsl['h'], $complementaryHsl['s'], $complementaryHsl['l']);
        $complementaryHex = sprintf('#%02x%02x%02x', $complementaryRgb['r'], $complementaryRgb['g'], $complementaryRgb['b']);

        return [
            'background' => "linear-gradient(135deg, rgba({$r}, {$g}, {$b}, 0.2), rgba({$complementaryRgb['r']}, {$complementaryRgb['g']}, {$complementaryRgb['b']}, 0.1))",
            'color' => $color,
            'border-color' => "rgba({$r}, {$g}, {$b}, 0.3)",
            'hover-background' => "linear-gradient(135deg, rgba({$r}, {$g}, {$b}, 0.3), rgba({$complementaryRgb['r']}, {$complementaryRgb['g']}, {$complementaryRgb['b']}, 0.2))"
        ];
    }

    /**
     * Convert RGB to HSL
     */
    private function rgbToHsl($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }
            $h /= 6;
        }

        return [
            'h' => round($h * 360),
            's' => round($s * 100),
            'l' => round($l * 100)
        ];
    }

    /**
     * Convert HSL to RGB
     */
    private function hslToRgb($h, $s, $l)
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $hue2rgb = function ($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1 / 2) return $q;
                if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
                return $p;
            };

            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $hue2rgb($p, $q, $h + 1 / 3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1 / 3);
        }

        return [
            'r' => round($r * 255),
            'g' => round($g * 255),
            'b' => round($b * 255)
        ];
    }

    /**
     * Get badge classes and inline styles for display
     */
    public function getBadgeClassesAttribute()
    {
        return "inline-flex items-center px-2 py-1 rounded-full text-xs font-medium transition-all cursor-pointer border";
    }

    /**
     * Get inline styles for badge
     */
    public function getBadgeStylesAttribute()
    {
        $styles = $this->tailwind_classes['style'];

        return "background: {$styles['background']}; color: {$styles['color']}; border-color: {$styles['border-color']};";
    }

    /**
     * Get hover styles for badge
     */
    public function getBadgeHoverStylesAttribute()
    {
        $styles = $this->tailwind_classes['style'];

        return "background: {$styles['hover-background']};";
    }
}
