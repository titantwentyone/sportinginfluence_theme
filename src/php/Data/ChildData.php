<?php

namespace Titan21\SportingInfluence\Data;

abstract class ChildData
{
    public static function get_current_users_children()
    {
        $currentuser = wp_get_current_user();

        $children = \MB_Relationships_API::get_connected([
            'id' => 'children-to-user',
            'to' => $currentuser->ID
        ]);

        return $children;
    }

    public static function is_child_age_valid($child)
    {
        $child_dob = rwmb_meta('date_of_birth', [], $child->ID);
        

        if(!$child_dob)
        {
            return false;
        }

        $child_dob = \DateTime::createFromFormat('Y-m-d', $child_dob)->format('U');
        $born_on_or_after = rwmb_meta('born_on_or_after', ['object_type' => 'setting'], 'site-settings');
        $born_on_or_after = \DateTime::createFromFormat('Y-m-d', $born_on_or_after)->format('U');
        $born_on_or_before = rwmb_meta('born_on_or_before', ['object_type' => 'setting'], 'site-settings');
        $born_on_or_before = \DateTime::createFromFormat('Y-m-d', $born_on_or_before)->format('U');

        if($child_dob >= $born_on_or_after && $child_dob <= $born_on_or_before)
        {
            return true;
        }

        return false;
    }

    /** is the current user the parent of the given child */
    public static function is_parent_of($child_id)
    {
        $current_user = wp_get_current_user();
        return \MB_Relationships_API::has( $child_id, $current_user->ID, 'children-to-user');
    }
}