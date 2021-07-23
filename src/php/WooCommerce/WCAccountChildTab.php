<?php

namespace Titan21\SportingInfluence\WooCommerce;

use Titan21\SportingInfluence\Data\ChildData;

class WCAccountChildTab
{
    public function __construct()
    {
        add_filter('woocommerce_account_menu_items', [$this, 'add_tab']);
        add_action('init', [$this, 'add_endpoint']);
        add_action('woocommerce_account_children_endpoint', [$this, 'display_children_manager']);
        add_action('woocommerce_account_addchild_endpoint', [$this, 'display_addchild']);
        add_action('woocommerce_account_editchild_endpoint', [$this, 'display_editchild']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('rwmb_frontend_after_process', [$this, 'attach_user_as_parent'], 10, 2);
        add_filter('rwmb_frontend_validate', [$this, 'validate'], 10, 2);
    }

    public function add_tab($menuitems)
    {
        unset($menuitems['downloads']);
        $menuitems['children'] = 'Children';
        //$menuitems['addchild'] = 'Add Child';
        return $menuitems;
    }

    public function add_endpoint()
    {
        //endpoint for woocommerce tab
        add_rewrite_endpoint( 'children', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'addchild', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'editchild', EP_ROOT | EP_PAGES );
        add_rewrite_tag( '%child_id%', '(.*)' );
    }

    public function display_children_manager()
    {
        //echo do_shortcode("[oxygen-template id='153']");
        $children = ChildData::get_current_users_children();

        echo "<p>Manage your childrens details here.</p>";

        echo "<table id='children_table'>";
        /*
        echo "<thead>";
        echo "<th></th>";
        echo "<th>Name</th>";
        echo "</thead>";
        */

        echo "<tbody>";
        foreach($children as $child)
        {
            echo "<tr>";

                echo "<td>";
                echo "<a class='button' href='/my-account/editchild?child_id={$child->ID}'>Edit</a>";
                echo "</td>";

                echo "<td>";
                echo $child->post_title;
                echo "</td>";


            echo "</tr>";
        }

        echo "</tbody>";

        echo "<tfoot>";
        echo "<td><a class='button' href='/my-account/addchild'>Add</a></td>";
        echo "</tfoot>";
        echo "</table>";
    }

    public function display_addchild()
    {
        //echo "adding_child...";
        echo do_shortcode("[mb_frontend_form id='88' post_fields='title' post_type='child' edit='true' allow_delete='true' label_title='Name' confirmation='Your data has been saved.']");
    }

    public function display_editchild()
    {
        $child_id =  get_query_var('child_id');
        if(ChildData::is_parent_of($child_id))
        {
            echo do_shortcode("[mb_frontend_form id='88' post_fields='title' post_id='".$child_id."' post_type='child' edit='true' allow_delete='true' label_title='Name' confirmation='Your data has been saved.']");
        }
        else
        {
            echo "";
        }
    }

    public function validate($validate, $config)
    {
        if($config['id'] != "88")
        {
            return;
        }

        if ( empty( $_POST['post_title'] ) )
        {
            $validate = 'A Child must have a name';
        }

        return $validate;
    }

    public function attach_user_as_parent($config, $child_id)
    {
        if($config['id'] != "88")
        {
            return;
        }

        if(ChildData::is_parent_of($child_id))
        {
            return;
        }
        
        $current_user = wp_get_current_user();

        \MB_Relationships_API::add($child_id, $current_user->ID, 'children-to-user');
    }

    public function enqueue_scripts()
    {
        if(is_account_page())
        {
            wp_register_style('child_form_css',WP_PLUGIN_URL.'/sportinginfluence/dist/css/child_form_css.min.css');
            wp_enqueue_style('child_form_css');
        }
    }
}