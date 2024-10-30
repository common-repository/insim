<?php
/* 
 * Copyright (C) 2022 sim_to_shop
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Description of class-sim-to-shop-campaign-list
 *
 * @author mathieu
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if (!class_exists('Sim_To_Shop_Recipient_List')) {

    class Sim_To_Shop_Recipient_List extends WP_List_Table {

        protected static $_campaign;

        const PER_PAGE = 25;

        function __construct($args = null) {

            global $status, $page;

            parent::__construct(
                    array(
                        'singular' => __('recipient', 'insim'),
                        'plural' => __('recipients', 'insim'),
                        'ajax' => true,
                        'screen' => 'sim-to-shop-campaign'
                    )
            );
            self::$_campaign = $args['campaign'];
        }

        public function set_campaign($campaign) {
            self::$_campaign = $campaign;
        }

        function get_columns() {
            $columns = array(
                'id_customer' => __('ID', 'insim'),
                //'firstname' => __('Firstname', 'insim'),
                //'lastname' => __('Lastname', 'insim'),
                'lastname' =>__('Full name'),
                'phone' => __('Phone', 'insim'),
                'iso_country' => __('Country', 'insim'),
                    
            );
            if (intval(self::$_campaign->status) == 0) {
                $column_to_add = array('user_actions' => __('Actions', 'insim'));
                $columns = array_merge($columns, $column_to_add);
            } else if (intval(self::$_campaign->status) >= 1) {
                $column_to_add = array(
                   
                    'status' => __('Status / Error', 'insim'),
                );
                $columns = array_merge($columns, $column_to_add);
            }
            return $columns;
        }

        function prepare_items() {
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

       
            $orderby = !empty($_REQUEST['orderby']) && '' != $_REQUEST['orderby'] ? sanitize_key($_REQUEST['orderby']) : 'lastname';
            $order = !empty($_REQUEST['order']) && '' != $_REQUEST['order'] ? sanitize_key($_REQUEST['order']) : 'desc';

            $current_page = absint($this->get_pagenum());

            $s = !empty($_REQUEST['s']) && '' != $_REQUEST['s'] ? sanitize_key($_REQUEST['s']) : '';

            $per_page = self::PER_PAGE;

            if (WP_DEBUG)
                error_log("before get_recipients");

            $results = self::$_campaign->get_recipients($current_page, $per_page, $orderby, $order, $s);
            $this->items = $results['recipients'];
            update_option('recipients', $this->items);
            $total_items = $results['total_items'];
            $this->set_pagination_args(array(
                'total_items' => $total_items, 
                'per_page' => $per_page, 
                'total_pages' => ceil($total_items / $per_page),
                'orderby' => $orderby,
                'order' => $order,
                's' => $s,
            ));

        }

        function display() {
            echo '<form method="post" id="search-form">
                <input type="hidden" name="page" value="my_list_recipient" />';
            $this->search_box(__('Search Recipients', 'insim'), 'search_id');


            wp_nonce_field('ajax-custom-list-nonce', '_ajax_custom_list_nonce');


            echo '<input id="order" type="hidden" name="order" value="' . esc_attr($this->_pagination_args['order']) . '" />';
            echo '<input id="orderby" type="hidden" name="orderby" value="' .esc_attr( $this->_pagination_args['orderby']) . '" />';
            echo '<input id="s" type="hidden" name="s" value="' . esc_attr($this->_pagination_args['s']) . '" />';
            echo '<input id="id_sendsms_campaign" type="hidden" name="id_sendsms_campaign" value="' .esc_attr(self::$_campaign->id_sendsms_campaign) . '" />';
            echo '<input id="list" type="hidden" name="list" value="' . esc_attr(get_class($this)) . '" />';


            parent::display();
            echo '</form>';
        }

        function ajax_response() {
            if (WP_DEBUG)
                error_log("ajax_response");

            check_ajax_referer('ajax-custom-list-nonce', '_ajax_custom_list_nonce');

            if (WP_DEBUG)
                error_log("ajax_response");

            $this->prepare_items();

       

            ob_start();
            if (!empty($_REQUEST['no_placeholder']))
                $this->display_rows();
            else
                $this->display_rows_or_placeholder();
            $rows = ob_get_clean();

            ob_start();
            $this->print_column_headers();
            $headers = ob_get_clean();

            ob_start();
            $this->pagination('top');
            $pagination_top = ob_get_clean();

            ob_start();
            $this->pagination('bottom');
            $pagination_bottom = ob_get_clean();

            $response = array('rows' => $rows);
            $response['pagination']['top'] = $pagination_top;
            $response['pagination']['bottom'] = $pagination_bottom;
            $response['column_headers'] = $headers;

            if (isset($total_items))
                $response['total_items_i18n'] = sprintf(_n('1 item', '%s items', $total_items), number_format_i18n($total_items));

            if (isset($total_pages)) {
                $response['total_pages'] = $total_pages;
                $response['total_pages_i18n'] = number_format_i18n($total_pages);
            }

            die(json_encode($response));
        }

        function get_sortable_columns() {
            $sortable_columns = array();
           
            return $sortable_columns;
        }

        function column_price($item) {
            return $item->price . ' €';
        }

        function column_id_customer($item) {
            if ($item->id_customer == 0) {
                return '';
            } else {
                return $item->id_customer;
            }
        }
        
        function column_status($item) {
            return Sim_To_Shop_Admin::get_instance()->get_error_SMS($item->status);
        }

        function column_transmitted($item) {
            if (intval($item->transmitted) == 0)
                return __('no', 'sim_to_shop');
            return __('yes', 'sim_to_shop');
            return Sim_To_Shop_Admin::get_instance()->get_status($item->status);
        }

        function column_default($item, $column_name) {
            switch ($column_name) {
                case 'user_actions' :
                    echo '<p>';
                    printf('<a id="%s" class="button tips %s" href="%s" data-tip="%s">%s</a>', $item->id_sendsms_recipient, 'delete', Sim_To_Shop_Send_Tab::get_form_url() . '&action=Sim_To_Shop_Send_Tab&id_sendsms_recipient=' . $item->id_sendsms_recipient, __('delete', 'insim'), __('delete', 'insim'));
                    echo '</p>';
                    break;
                case 'id_customer':
                case 'firstname':
                case 'lastname' :
                case 'phone' :
                case 'price' :
                case 'transmitted':
                case 'status':
                case 'iso_country' :
                    if (isset($item->$column_name))
                        return $item->$column_name;
                    else {
                        return '';
                    }
                default:
                    return print_r($item, true);
            }
        }

    }

}
