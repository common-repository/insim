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


if (!class_exists('Sim_To_Shop_Campaign_List')) {

    class Sim_To_Shop_Campaign_List extends WP_List_Table {
        
        /**
         * @var _status the campaign with this status are displayed
         */
        public static $_status = array();

        const PER_PAGE = 25;

        function __construct($args = null) {

            global $status, $page;

            //Set parent defaults
            parent::__construct(
                    array(
                        'singular'  => __('campaign','insim'),
                        'plural'    => __('campaigns','insim'),
                        'ajax' => true,
                        'screen' => 'insim'
                    )
            );
            self::$_status = $args['status'];
        }

        function get_columns() {
            $columns = array(
                'ticket' => __('Campaign id', 'insim'),
                'title' => __('Campaign name', 'insim'),
                'status' => __('Status', 'insim'),
                'nb_recipients' => __('Recipients', 'insim'),
                'date_send' => __('Sending date', 'insim'),
                'user_actions' => __('', 'insim')
            );

            return $columns;
        }

        function prepare_items() {
            global $wpdb;
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

            //orderby and order
            $orderby = !empty($_REQUEST['orderby']) && '' != $_REQUEST['orderby'] ? sanitize_key($_REQUEST['orderby']) : 'id_sendsms_campaign';
            $order = !empty($_REQUEST['order']) && '' != $_REQUEST['order'] ? sanitize_key($_REQUEST['order']) : 'desc';

            //pagination
            $current_page = absint($this->get_pagenum());

            $s=! empty( $_REQUEST['s'] ) && '' != $_REQUEST['s'] ? sanitize_key($_REQUEST['s']) : '';
            $total_items = Sim_To_Shop_Campaign_Model::count_campaigns([0, 1, 2, 3, 4, 5],$s);
            $per_page = 10;
            // print_r($total_items);
            // echo('< br/>');
            // print_r($per_page);
            // echo('< br/>');
            // print_r(ceil($total_items / $per_page));
            // echo('< br/>');
            // print_r($orderby);
            // echo('< br/>');
            // print_r($order);
            // echo('< br/>');
            // print_r($s);
            // die();
            $this->set_pagination_args(array(
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page, //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items / $per_page),
                // Set ordering values if needed (useful for AJAX)
                'orderby' => $orderby,
                'order' => $order,
                's'     => $s,
            ));
            
            $this->items = Sim_To_Shop_Campaign_Model::get_campaigns(self::$_status, $s,$orderby, $order, $this->get_pagenum(), 10);
        }

        /**
         * Display the list
         */
        function display() {

            wp_nonce_field('ajax-custom-list-nonce', '_ajax_custom_list_nonce');

            echo '<input id="order" type="hidden" name="order" value="' . $this->_pagination_args['order'] . '" />';
            echo '<input id="orderby" type="hidden" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
            echo '<input id="s" type="hidden" name="s" value="' . $this->_pagination_args['s'] . '" />';
            parent::display();
        }

        function ajax_response() {
            if (WP_DEBUG) {
                error_log("request: ".print_r($_REQUEST,true));
            }
            check_ajax_referer('ajax-custom-list-nonce', '_ajax_custom_list_nonce');

            $this->prepare_items();
            
            if (WP_DEBUG) {
                error_log("_args: ".print_r($this->_args,true));
            }
            if (WP_DEBUG) {
                error_log("_pagination_args: ".print_r($this->_pagination_args,true));
            }

            //extract($this->_args);
            //extract($this->_pagination_args, EXTR_SKIP);

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

     

        function column_price($item) {
            return $item->price . ' €';
        }

        function column_status($item) {
            return Sim_To_Shop_Admin::get_instance()->get_status($item->status);
        }

        function column_default($item, $column_name) {
            switch ($column_name) {
                case 'ticket':
                case 'title':
                case 'status' :
                case 'simulation' :
                case 'nb_recipients' :
                case 'nb_sms' :
                case 'price':
                case 'date_send':
                    if (isset($item->$column_name))
                        return $item->$column_name;
                    else {
                        return '';
                    }
                case 'user_actions' :
                    echo '<p style ="display: inline-block;">';
                    $msg = $item->status ==3 ? 'disabled ': '';
                    printf('<a type = "submit" '.$msg.'class="button tips %s" '.($msg =='' ? 'href="%s"' :  '"%s"').'title="%s" data-tip="%s">%s<i class="fa fa-edit"></i></a>', 'edit', $msg=='' ? Sim_To_Shop_Send_Tab::get_form_url() . '&action=Sim_To_Shop_Send_Tab&id_sendsms_campaign=' . $item->id_sendsms_campaign : '', 'Edit the campaign', '', '');
                    //<td><a href="' . $this->get_form_url() . '&action=' . get_class($this) . '&id_sendsms_campaign=' . $campaign->id_sendsms_campaign . '"><img src="../img/admin/edit.gif" class="edit"></a></td>
                    echo '</p>';
                    echo '<p style ="margin-left: 7px; display: inline-block;">';
                    $msg = $item->status ==3 || !get_option('isConnectedToInsim') ? 'disabled ': '';
                    $msg2 = $item->status ==3 ? " inline-block; ": " none;";
                    $msg3 = $item->status !=3 && !get_option('isConnectedToInsim') ? " inline-block; ": " none;";
                    printf('<a '.$msg.' type="submit" name="sendsms_save" id="send_directly" style="background-color: #2271b1; color: white;" class="button tips %s" %s title="%s" data-tip="%s">%s%s</a>',
                    'send',
                    $msg ? '' : 'href="' . Sim_To_Shop_Send_Tab::get_form_url() . '&sendsms_save2=true&action=Sim_To_Shop_Send_Tab&id_sendsms_campaign=' . $item->id_sendsms_campaign . '"',
                    __('Send the campaign', 'insim'),
                    '','', // An empty string to add nothing before the <i> element
                    __('<i class="fa fa-paper-plane" aria-hidden="true"></i>', 'insim')
                    );
                    print_r('<p style ="margin-left: 7px; display: inline-block;">');
                    printf('<a '.$msg.' type="submit" name="sendsms_duplicate" id="send_directly" style="background-color: grey; color: white;" class="button tips %s" %s title="%s" data-tip="%s">%s%s</a>',
                    'Duplicate',
                    $msg ? '' : 'href="' . Sim_To_Shop_Send_Tab::get_form_url() . '&sendsms_save2=true&action=Sim_To_Shop_Send_Tab&id_sendsms_campaign=' . $item->id_sendsms_campaign . '"',
                    __('Duplicate the campaign', 'insim'),
                    '','', // An empty string to add nothing before the <i> element
                    __('<i class="fa fa-clone" aria-hidden="true"></i>', 'insim')
                    );
                    printf('<a type = "submit" style="margin-left: 7px; color: red; background: none;" name="sendsms_delete" id ="send_delete" style ="background-color: #2271b1; color: white;" '.$msg.'class="button tips %s" '.($msg =='' ? 'href="%s"' :  '"%s"').'title="%s" data-tip="%s">%s<i class="fa fa-trash"></i></a>', 'send', $msg=='' ? Sim_To_Shop_Send_Tab::get_form_url() . '&sendsms_save2=true&action=Sim_To_Shop_Send_Tab&id_sendsms_campaign=' . $item->id_sendsms_campaign : '', 'Delete the campaign', '', '');
                    print_r('</p>');
                    print_r('<p style = "display : '.$msg2.'"><a id ="see_analytics" style ="margin-left: 5px;" title="See stats" target= "_blank" href ="https://insim.app">'.__('<i class="fa fa-bar-chart" aria-hidden="true"></i>', 'insim').'<i class="fa fa-external-link" style="margin-left: 5px;"></i></a></p>');
                    // echo();
                    echo '</p>';
                    echo '<p style="font-weight: bold; color: red; display: '.$msg3.'"> To send the campaign, please connect your eShop to your mobile</p>';
                    break;
                default:
                    return "PB:" . print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }

    }

}
