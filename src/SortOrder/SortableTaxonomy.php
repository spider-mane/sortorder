<?php
// Copyright 2021 chris
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.


namespace WebTheory\SortOrder;

use WebTheory\WpLibrary;

class SortableTaxonomy extends SortableObjectsBase
{
    public $taxonomy;
    public $submenu_args;
    public $terms = [];
    public $admin_uri;
    public $parent_slug;
    public $submenu_slug;

    static $admin_page_slug;

    public function __construct(string $taxonomy, string $post_type, array $ui_args)
    {
        $this->set_taxonomy($taxonomy);
        $this->set_post_type($post_type);
        $this->set_terms();
        $this->set_ui_args($ui_args);
        $this->add_admin_page();

        add_action("get_terms", [$this, 'order_terms_query'], null, 4);
    }

    /**
     *
     */
    public function set_taxonomy(string $taxonomy)
    {
        $this->taxonomy = get_taxonomy($taxonomy);

        return $this;
    }

    /**
     *
     */
    public function set_post_type(string $post_type)
    {
        $this->post_type = get_post_type_object($post_type);

        return $this;
    }

    /**
     *
     */
    public function set_terms()
    {
        $terms = get_terms([
            'taxonomy' => $this->taxonomy->name,
            'hide_empty' => false
        ]);

        foreach ($terms as $term) {
            $this->terms[$term->slug] = $term;
        }

        ksort($this->terms);

        return $this;
    }

    /**
     *
     */
    public function set_ui_args($ui_args)
    {
        $this->submenu_args = $ui_args['submenu_page'] ?? null;

        return $this;
    }

    /**
     *
     */
    public function add_admin_page()
    {
        if (!empty($this->submenu_args) && $this->submenu_args !== false) {
            add_action('admin_menu', [$this, 'add_submenu_page']);
            add_filter('admin_title', [$this, 'fix_subpage_title'], null, 2);
            add_filter('submenu_file', [$this, 'fix_submenu_file'], null, 2);
        }

        return $this;
    }

    /**
     *
     */
    public function order_terms_query($terms, $taxonomy, $args, $query)
    {
        $orderby_apex = "_{$this->post_type->name}_display_position";
        $orderby_hierarchy = "_{$this->post_type->name}_hierarchy_display_position";

        $orderby = $query->query_vars['orderby'];

        if ($orderby !== $orderby_apex && $orderby !== $orderby_hierarchy) {
            return $terms;
        }

        $terms = $this::order_objects_array($terms, 'term', $orderby_apex, $orderby_hierarchy);

        return $query->query_vars['order'] !== 'DESC' ? $terms : array_reverse($terms);
    }

    /**
     *
     */
    public function admin_notices($a)
    {
        $message = null;

        if (get_transient('display_orders_bulk_updated')) {
            $message = "display positions updated";
            $transient = "display_orders_bulk_updated";
        } elseif (get_transient("single_display_order_updated")) {
            $message = "display order successfully updated";
            $transient = "single_display_order_updated";
        }

        if (isset($transient)) {
            delete_transient($transient); ?>

            <div id="message" class="notice notice-success is-dismissible">
                <p><?= $message ?></p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>

<?php

        }
    }

    /**
     *
     */
    public function add_submenu_page($context)
    {
        $this->parent_slug = $this->submenu_args['parent_slug'] ?? "edit.php?post_type={$this->post_type->name}";

        $this->submenu_slug = "{$this::$admin_page_slug}&taxonomy={$this->taxonomy->name}";

        $this->admin_uri = "{$this->parent_slug}&page={$this->submenu_slug}";

        $parent_slug = htmlspecialchars($this->parent_slug);
        $menu_title = $this->submenu_args['menu_title'] ?? "";
        $capability = $this->submenu_args['capability'] ?? ""; // needs workaround
        $menu_slug = htmlspecialchars($this->submenu_slug);

        add_submenu_page($parent_slug, null, $menu_title, $capability, $menu_slug, [$this, 'load_admin_page']);

        if (isset($this->submenu_args['display']) && $this->submenu_args['display'] === false) {
            remove_submenu_page($parent_slug, $menu_slug);
        }
    }

    /**
     * creating a submenu page requires a top level admin page to be created for it to display
     */
    public static function register_admin_page($menu_slug = null)
    {
        Self::$admin_page_slug = 'ba_sort_terms';

        add_menu_page(null, null, 'manage_options', Self::$admin_page_slug, [static::class, 'load_admin_page']);
        remove_menu_page(Self::$admin_page_slug);
    }

    /**
     *
     */
    public function fix_submenu_file($submenu_file, $parent_file)
    {
        $screen = get_current_screen();

        if (
            $screen->base === "toplevel_page_{$this::$admin_page_slug}"
            && $screen->post_type === $this->post_type->name
            && $screen->taxonomy === $this->taxonomy->name
        ) {
            return htmlspecialchars($this->submenu_args['submenu_file'] ?? $this->submenu_slug);
        }

        return $submenu_file;
    }

    /**
     *
     */
    public function fix_subpage_title($admin_title, $title)
    {
        $screen = get_current_screen();

        if ($screen->base === "toplevel_page_{$this::$admin_page_slug}" && $screen->post_type === $this->post_type->name && $screen->taxonomy === $this->taxonomy->name) {

            $page_title = $this->submenu_args['page_title'] ?? "Sort {$this->taxonomy->name} for {$this->post_type->label}";

            return $page_title . $admin_title;
        }

        return $admin_title;
    }

    /**
     *
     */
    public static function load_admin_page()
    {
        $taxonomy = filter_has_var(INPUT_GET, 'taxonomy') ? sanitize_key($_GET['taxonomy']) : '';
        $post_type = filter_has_var(INPUT_GET, 'post_type') ? sanitize_key($_GET['post_type']) : '';

        $apex_position_meta_key = "_{$post_type}_display_position";
        $hierarchy_position_meta_key = "_{$post_type}_hierarchy_display_position";

        $apex_position_input_name = 'ba_order';
        $hierarchy_position_input_name = 'ba_hierarchy_order';

        // process input data
        if (filter_has_var(INPUT_POST, $apex_position_input_name) || filter_has_var(INPUT_POST, $hierarchy_position_input_name)) {
            $apex_positions = $_REQUEST[$apex_position_input_name] ?? [];
            $hierarchy_positions = $_REQUEST[$hierarchy_position_input_name] ?? [];

            foreach ($apex_positions as $term_id => $position) {
                update_term_meta($term_id, $apex_position_meta_key, (int) $position);
            }

            foreach ($hierarchy_positions as $term_id => $position) {
                update_term_meta($term_id, $hierarchy_position_meta_key, (int) $position);
            }
        }
        // end process input data

        $terms = [
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'orderby' => $apex_position_meta_key
        ];

        if (empty($terms = get_terms($terms))) {
            echo "<h1>There is currently nothing to sort</h1>";
            return;
        }

        $terms_walker = new SortableObjectsWalker;
        $terms_walker->set_object_type('term');


        $taxonomy_object = get_taxonomy($taxonomy);
        $post_type_object = get_post_type_object($post_type);

        $terms_walker_args = [
            'apex_meta_key' => $apex_position_meta_key,
            'hierarchy_meta_key' => $hierarchy_position_meta_key,
            'ul_classes' => 'hierarchy sortable sortable--group',
            'li_classes' => 'sortable--item-container',
            'object_div_classes' => '',
            'common_input_classes' => 'order-input small 0hide-if-js',
            'apex_input_classes' => 'order-input--apex',
            'hierarchy_input_classes' => 'order-input--hierarchy',
        ];

        // create array of values to pass template
        $template_data['objects'] = $terms;
        $template_data['title'] = "Sort {$taxonomy_object->label} for {$post_type_object->labels->name}";
        $template_data['sorted_sortables'] = $terms_walker->walk($terms, 0, $terms_walker_args);

        // render template
        echo WpLibrary::renderTemplate('admin-page__sortable-objects', $template_data);
    }

    /**
     *
     */
    public static function update_meta_keys($old_post_type_name, $new_post_type_name)
    {
        $old_meta_key = "_{$old_post_type_name}_display_position";
    }
}
