<?php

namespace WebTheory\SortOrder;

use Leonidas\Contracts\Extension\WpExtensionInterface;
use Leonidas\Framework\Exceptions\InvalidCallToPluginMethodException;
use Twig\Environment;

final class SortOrder
{
    /**
     * @var WpExtensionInterface
     */
    protected $base;

    /**
     * @var SortOrder
     */
    private static $instance;

    private function __construct(WpExtensionInterface $base)
    {
        $this->base = $base;
    }

    public static function launch(WpExtensionInterface $base): void
    {
        if (!self::isLoaded()) {
            self::construct($base);
        } else {
            self::throwAlreadyLoadedException(__METHOD__);
        }
    }

    public static function renderTemplate($template, $context)
    {
        $twig = static::$instance->base->get(Environment::class);

        return $twig->render("{$template}.twig", $context);
    }

    private static function isLoaded(): bool
    {
        return isset(self::$instance) && (self::$instance instanceof self);
    }

    private static function construct(WpExtensionInterface $base): void
    {
        self::$instance = new self($base);
    }

    private static function throwAlreadyLoadedException(callable $method): void
    {
        throw new InvalidCallToPluginMethodException(
            self::$instance->base->getName(),
            $method
        );
    }
}
