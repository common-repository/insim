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
    exit; 
}
?>

<div class="wrap woocommerce">
<!-- <div style="display:flex; justify-content: start; align-items: center;"> -->
<div>
    <div>
<p class="center"><a target = '_blank' href="https://www.ardary-insim.com/"><img src="<?php echo plugin_dir_url(__FILE__) ?>../img/insim-title.png" style="margin-top: 15px; width: 99%;" alt="inSIM"/></a>
</div>
<!-- <div style="padding-top: 10px; ">
<span style ="font-weight: 600;">Low-cost SMS marketing <br/>through your mobile plan</span>
</div> -->
</div>
</p>

    <div class="icon32 icon32-woocommerce-status" id="icon-woocommerce">

    </div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper" style="margin-bottom: 50px;">

        <?php
         if ( null != get_option('setting_change') && get_option('setting_change') == true ) {
            $tabs = array(
                'campaigns' => __('SMS Campaigns', 'insim'),
                 'automations' => __('SMS Automations', 'insim'),
                
                //'history' => __('History', 'insim'),
                 'helpdesk' => __('SMS Helpdesk CRM', 'insim'),
                 'settings2' => __('Settings', 'insim'),
                 'contact_us' => __('Contact us', 'insim')
            );
         } else {
            $tabs = array(
                'settings' => __('Settings', 'insim'),
                'campaigns' => __('SMS Campaigns', 'insim'),
                 'automations' => __('SMS Automations', 'insim'),
                
                //'history' => __('History', 'insim'),
                 'helpdesk' => __('SMS Helpdesk CRM', 'insim'),
                 'contact_us' => __('Contact us', 'insim')
            );
         }
        

        if ($current_action=="sim_to_shop_send_tab" && isset($_REQUEST['newCampaign']) && $_REQUEST['newCampaign']==1) {
            $current_tab="campaigns";
        }
        
        foreach ($tabs as $name => $label) {
            if ($name =="campaigns" || $name == "automations" || $name == "helpdesk") {
                echo '<a style = "border-top: 4px solid #1eacba;" href="' . admin_url('admin.php?page=insim&tab=' . $name) . '" class="nav-tab ';
            } else {
                echo '<a style ="margin-top: 3px;" href="' . admin_url('admin.php?page=insim&tab=' . $name) . '" class="nav-tab ';
            }
            
            if ($current_tab == $name)
                echo 'nav-tab-active';
            echo '">' . __($label,'insim') . '</a>';
        }
        ?>
    </h2>
    <?php
    switch ($current_tab) {
        case "settings" :
            Sim_To_Shop_Admin::sim_to_shop_settings();
            break;
        case "settings2" :
            Sim_To_Shop_Admin::sim_to_shop_settings();
            break;
        case "automations" :
            Sim_To_Shop_Admin::sim_to_shop_messages();
            break;
        case "campaigns" :
            Sim_To_Shop_Admin::sim_to_shop_campaigns();
            break;
        case "contact_us" :
            Sim_To_Shop_Admin::sim_to_shop_history();
            break;
     
        default :
            Sim_To_Shop_Admin::sim_to_shop_news();
            break;
    }
    ?>
</div>

