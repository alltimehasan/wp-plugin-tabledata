<?php
/*
 * Plugin Name:       Data Tale
 * Plugin URI:        https://hasan4web.com/plugins/data-table/
 * Description:       Working with WP list table
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Hasan Ali
 * Author URI:        https://hasan4web.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       tabledata
 * Domain Path:       /languages/
 */

use Stripe\Person;

/**
 * Activation Hook
 */
function tabledata_activation_hook() {

}
register_activation_hook( __FILE__, 'tabledata_activation_hoo' );

/**
 * Deactivation Hook
 */
function tabledata_deactivation_hook() {

}
register_deactivation_hook( __FILE__, 'tabledata_deactivation_hook' );

/**
 * Load Text Domain
 */
function tabledata_load_textdomain() {
    load_plugin_textdomain( 'tabledata', false, dirname(__FILE__) . '/languages' );
}
add_action( 'plugins_loaded', 'tabledata_load_textdomain' );

require_once( 'class.persons-table.php' );

// Add Admin Menu
function datatable_admin_menu() {
    add_menu_page( 
        __( 'Data Table', 'tabledata' ),
        __( 'Data Table', 'tabledata' ),
        'manage_options',
        'datatable',
        'datatable_display_table'
    );
}

function datatable_search_by_name($item) {
    $name = strtolower( $item['name'] );
    $search_name = sanitize_text_field( $_REQUEST['s'] );
    if( strpos( $name, $search_name ) !== false  ) {
        return true;
    }
    return false;
}

function datatable_filter_sex($item){
    $sex = $_REQUEST['filter_s']??'all';
    if('all'==$sex){
        return true;
    }else{
        if( $sex==$item['sex']){
            return true;
        }
    }
    return false;
}

function datatable_display_table() {
    include_once('dataset.php');
    $orderby = $_REQUEST['orderby']?? '';
    $order = $_REQUEST['order']?? '';
    $table = new Persons_table();

    if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
        $data = array_filter($data, 'datatable_search_by_name');
    }

    if ( isset( $_REQUEST['filter_s'] ) && !empty($_REQUEST['filter_s']) ) {
		$data = array_filter( $data, 'datatable_filter_sex' );
	}

    if('age' == $orderby) {
        if('asc' == $order) {
            usort($data, function($item1, $item2){
                return $item2['age'] <=> $item1['age'];
            });
        } else {
            usort($data, function($item1, $item2){
                return $item1['age'] <=> $item2['age'];
            });
        }
    }

    if('name' == $orderby) {
        if('asc' == $order) {
            usort($data, function($item1, $item2){
                return $item2['name'] <=> $item1['name'];
            });
        } else {
            usort($data, function($item1, $item2){
                return $item1['name'] <=> $item2['name'];
            });
        }
    }

    if('sex' == $orderby) {
        if('asc' == $order) {
            usort($data, function($item1, $item2){
                return $item2['sex'] <=> $item1['sex'];
            });
        } else {
            usort($data, function($item1, $item2){
                return $item1['sex'] <=> $item2['sex'];
            });
        }
    }

    if('email' == $orderby) {
        if('asc' == $order) {
            usort($data, function($item1, $item2){
                return $item2['email'] <=> $item1['email'];
            });
        } else {
            usort($data, function($item1, $item2){
                return $item1['email'] <=> $item2['email'];
            });
        }
    }

    $table->set_data($data);
    $table->prepare_items();
    ?>

    <div class="wrap">
        <h2><?php echo _e( 'Persons', 'tabledata' ) ?></h2>
        <form method="GET" action="">
            <?php
            $table->search_box( 'search', 'search_id' );
            $table->display();
            ?>
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        </form>
    </div>

    <?php
}
add_action( 'admin_menu', 'datatable_admin_menu' );