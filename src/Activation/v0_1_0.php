<?php

namespace WebTheory\SortOrder\Activation;

use Leonidas\Contracts\Extension\PluginActivatorInterface;
use Leonidas\Contracts\Extension\WpExtensionInterface;

final class v0_1_0 implements PluginActivatorInterface
{
    /**
     * @var WpExtensionInterface
     */
    protected $base;

    public function __construct(WpExtensionInterface $base)
    {
        $this->base = $base;
    }

    public function install(bool $networkWide): void
    {
        //
    }

    public function update(): void
    {
        //
    }

    public function uninstall(): void
    {
        //
    }
}
