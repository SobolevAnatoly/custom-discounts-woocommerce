<?php
/**
 * Copyright (c) 2020 Anatolii S.
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Adsfwc\Admin;

if ( ! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly;

class RegisterAdminPage
{

    public static function register()
    {
        $instance = new self;

        add_action('admin_menu', [$instance, 'registerDiscountSettingsPage'], 99);
        add_action('admin_init', [$instance, 'registerSettings']);
    }

    public function registerDiscountSettingsPage()
    {
        add_options_page(
            __('Advanced Discounts Page', 'advanced-discounts'),
            __('Advanced Discounts', 'advanced-discounts'),
            'manage_options',
            'custom-discounts-page',
            [$this, 'discountSettingsPage']
        );
        /**
         * add_submenu_page(
         * 'woocommerce',
         * __('Advanced Discounts Page', 'advanced-discounts'),
         * __('Advanced Discounts', 'advanced-discounts'),
         * 'manage_options',
         * 'custom-discounts-page',
         * [$this, 'discountSettingsPage']
         * );
         *
         */
    }

    public function discountSettingsPage()
    {
        ?>
        <h2>Advanced Discounts Plugin Settings</h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('advanced_discounts_plugin_options');
            do_settings_sections('advanced_discounts_plugin'); ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php
            esc_attr_e('Save'); ?>"/>
        </form>
        <?php
    }

    public function registerSettings()
    {
        register_setting(
            'advanced_discounts_plugin_options',
            'advanced_discounts_plugin_options'
        );

        add_settings_section(
            'discount_settings',
            __('Discount Settings', 'advanced-discounts'),
            [$this, 'discountSectionText'],
            'advanced_discounts_plugin'
        );

        add_settings_field(
            'discount_setting_registered_amount',
            __('Registered User Discount', 'advanced-discounts'),
            [$this, 'discountSettingRegisteredAmount'],
            'advanced_discounts_plugin',
            'discount_settings'
        );
        add_settings_field(
            'discount_setting_unregistered_amount',
            __('Unregistered user discount', 'advanced-discounts'),
            [$this, 'discountSettingUnregisteredAmount'],
            'advanced_discounts_plugin',
            'discount_settings'
        );
        add_settings_field(
            'discount_setting_on_leaving',
            __('Departure Discount', 'advanced-discounts'),
            [$this, 'discountSettingOnLeaving'],
            'advanced_discounts_plugin',
            'discount_settings'
        );
    }

    public function discountSectionText()
    {
        echo "<p>" . __(
                'Please enter the discount amount as a percentage from 0 to 100',
                'advanced-discounts'
            ) . "</p>";
    }

    public function discountSettingRegisteredAmount()
    {
        $settings = (array)get_option('advanced_discounts_plugin_options');
        $field    = "discount_setting_registered_amount";
        $value    = !empty(esc_attr($settings[$field])) ? esc_attr($settings[$field]) : 0;

        echo "<input type='number' name='advanced_discounts_plugin_options[$field]' min='0' max='100' step='1' value='$value' />";
    }

    public function discountSettingUnregisteredAmount()
    {
        $settings = (array)get_option('advanced_discounts_plugin_options');
        $field    = "discount_setting_unregistered_amount";
        $value    = !empty(esc_attr($settings[$field])) ? esc_attr($settings[$field]) : 0;

        echo "<input type='number' name='advanced_discounts_plugin_options[$field]' min='0' max='100' step='1' value='$value' />";
    }

    public function discountSettingOnLeaving()
    {
        $settings = (array)get_option('advanced_discounts_plugin_options');
        $field    = "discount_setting_on_leaving";
        $value    = !empty(esc_attr($settings[$field])) ? esc_attr($settings[$field]) : 0;

        echo "<input type='number' name='advanced_discounts_plugin_options[$field]' min='0' max='100' step='1' value='$value' />";
    }
}