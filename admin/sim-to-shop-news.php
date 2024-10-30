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
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Sim_To_Shop_News')) :

    /**
     * WC_Admin_Settings_General
     */
    class Sim_To_Shop_News {

        /**
         * Constructor.
         */
        public function output() {
                    echo '
                        <div class="row updated">
                    ';
                    echo '
                        <div id="message" class="col-md-8"><h2 style="margin-top: 30px;><strong">' . __('SMS conversation CRM :
                            ','insim') . '</strong></h2>
                            <b>inSIM CRM for WooCommerce is a customer support platform through your phone number.</b>
                            <br /><br /><b>Use the best channel to answer your customers.</b>
                            <br /><button class= "button-primary" style = "height: 40px; margin-bottom: 30px;margin-top: 15px;" type ="button" formtarget= "_blank" onClick= "javascript:window.open(\'https://insim.app/\', \'_blank\')">Go to inSIM helpdesk CRM <i class="fa fa-send"></i></button><i style= "margin-left: 15px;">( use the same credentials as your mobile app )</i>
                        </div>
                        <div class="col-md-4" style="padding: 10px;" >
                            <video width="540" height="260" controls autoplay=true muted loop>
                                <source src="https://ardary-insim.com/wp-content/uploads/2022/12/insim-crm1.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>                   
                        </div> ';
                    echo '</div>';
                    return;
            
        }

    }

    endif;

return new Sim_To_Shop_News();

