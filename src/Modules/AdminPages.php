<?php

namespace WebTheory\SortOrder\Modules;

use Leonidas\Framework\Modules\AbstractModule;

class AdminPages extends AbstractModule
{
    public function hook(): void
    {
        add_action('admin_menu', function () {
            SortableTaxonomy::register_admin_page();
            SortablePostsInTerm::register_admin_page();
        });
    }
}
