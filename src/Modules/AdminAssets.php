<?php

namespace WebTheory\SortOrder\Modules;

use Leonidas\Contracts\Ui\Asset\ScriptCollectionInterface;
use Leonidas\Contracts\Ui\Asset\StyleCollectionInterface;
use Leonidas\Framework\Modules\AbstractAdminAssetProviderModule;
use Leonidas\Library\Core\Asset\ScriptBuilder;
use Leonidas\Library\Core\Asset\ScriptCollection;
use Leonidas\Library\Core\Asset\StyleBuilder;
use Leonidas\Library\Core\Asset\StyleCollection;

final class AdminAssets extends AbstractAdminAssetProviderModule
{
    protected function styles(): StyleCollectionInterface
    {
        return StyleCollection::with(

            StyleBuilder::for('sortorder')
                ->src($this->asset('css/backalley-sortable-objects.css'))
                ->version($this->version('1.0.0'))
                ->enqueue(true)
                ->done()

        );
    }

    protected function scripts(): ScriptCollectionInterface
    {
        return ScriptCollection::with(

            ScriptBuilder::for('sortorder')
                ->src($this->asset('js/backalley-sortable-objects.js'))
                ->version($this->version('1.0.0'))
                ->dependencies('jquery', 'jquery-ui-sortable')
                ->inFooter(true)
                ->enqueue(true)
                ->done()

        );
    }
}
