<?php

namespace MythicalSystemsFramework\Cli;

class Colors
{
    public static function Black(): string
    {
        return "\033[0;30m";
    }

    public static function DarkBlue(): string
    {
        return "\033[0;34m";
    }

    public static function DarkGreen(): string
    {
        return "\033[0;32m";
    }

    public static function DarkAqua(): string
    {
        return "\033[0;36m";
    }

    public static function DarkRed(): string
    {
        return "\033[0;31m";
    }

    public static function DarkPurple(): string
    {
        return "\033[0;35m";
    }

    public static function Gold(): string
    {
        return "\033[0;33m";
    }

    public static function Gray(): string
    {
        return "\033[0;37m";
    }

    public static function DarkGray(): string
    {
        return "\033[1;30m";
    }

    public static function Blue(): string
    {
        return "\033[1;34m";
    }

    public static function Green(): string
    {
        return "\033[1;32m";
    }

    public static function Aqua(): string
    {
        return "\033[1;36m";
    }

    public static function Red(): string
    {
        return "\033[1;31m";
    }

    public static function LightPurple(): string
    {
        return "\033[1;35m";
    }

    public static function Yellow(): string
    {
        return "\033[1;33m";
    }

    public static function White(): string
    {
        return "\033[1;37m";
    }

    public static function Reset(): string
    {
        return "\033[0m";
    }

    public static function Bold(): string
    {
        return "\033[1m";
    }

    public static function Strike(): string
    {
        return "\033[9m";
    }

    public static function Underline(): string
    {
        return "\033[4m";
    }

    public static function NewLine(): string
    {
        return "\n";
    }

    public static function translateColorsCode(string $message): string
    {
        $pattern = '/&([0-9a-fklmnor])/i';
        $message = preg_replace_callback($pattern, function ($matches) {
            return self::getColorCode($matches[1]);
        }, $message);

        return $message;
    }

    private static function getColorCode(string $colorCode): string
    {
        switch ($colorCode) {
            case '0':
                return self::Black();
            case '1':
                return self::DarkBlue();
            case '2':
                return self::DarkGreen();
            case '3':
                return self::DarkAqua();
            case '4':
                return self::DarkRed();
            case '5':
                return self::DarkPurple();
            case '6':
                return self::Gold();
            case '7':
                return self::Gray();
            case '8':
                return self::DarkGray();
            case '9':
                return self::Blue();
            case 'a':
                return self::Green();
            case 'b':
                return self::Aqua();
            case 'c':
                return self::Red();
            case 'd':
                return self::LightPurple();
            case 'e':
                return self::Yellow();
            case 'f':
                return self::White();
            case 'k':
                return self::Strike();
            case 'l':
                return self::Bold();
            case 'm':
                return self::Strike();
            case 'n':
                return self::Underline();
            case 'r':
                return self::Reset();
            case 'o':
                return self::NewLine();
            default:
                return '';
        }
    }
}
