<?php

/**
 * @package Backalley-Starter
 */

namespace WebTheory\SortOrder;

abstract class SortableObjectsBase
{
    public static function order_objects_array($objects, $object_type, $orderby_apex, $orderby_hierarchy)
    {
        $properties = static::infer_object_properties($object_type);

        $object_id = $properties['object_id'];
        $object_parent = $properties['object_parent'];

        $apex_objects = [];
        $apex_order = [];

        $hierarchy_objects = [];
        $hierarchy_order = [];

        foreach ($objects as $object) {
            if (empty($object->$object_parent)) {
                $apex_objects[] = $object;
                $apex_order[$object->$object_id] = (int) get_metadata($object_type, $object->$object_id, $orderby_apex, true);
            }

            if (!empty($object->$object_parent)) {
                $hierarchy_objects[] = $object;
                $hierarchy_order[$object->$object_id] = (int) get_metadata($object_type, $object->$object_id, $orderby_hierarchy, true);
            }
        }

        $apex_objects = static::sort_objects_array($apex_objects, $apex_order, $object_id);
        $hierarchy_objects = static::sort_objects_array($hierarchy_objects, $hierarchy_order, $object_id);

        return array_merge($apex_objects, $hierarchy_objects);
    }

    public static function sort_objects_array(array $objects_array, array $order_array, string $order_key)
    {
        usort($objects_array, function ($a, $b) use ($order_array, $order_key) {

            foreach ([&$a, &$b] as &$obj) {
                $obj = (int) $order_array[$obj->{$order_key}] >= 0 ? $order_array[$obj->{$order_key}] : 0;
            }

            if ($a === $b) {
                return 0;
            }

            if ($a < $b && $a === 0) {
                return 1;
            }

            if ($a > $b && $b === 0) {
                return -1;
            }

            return $a > $b ? 1 : -1;
        });

        return $objects_array;
    }

    public static function infer_object_properties($object_type)
    {
        switch ($object_type) {
            case 'post':
                $object_id = 'ID';
                $object_parent = 'post_parent';

                break;
            case 'term':
                $object_id = 'term_id';
                $object_parent = 'parent';

                break;
        }

        return ['object_id' => $object_id, 'object_parent' => $object_parent];
    }
}
