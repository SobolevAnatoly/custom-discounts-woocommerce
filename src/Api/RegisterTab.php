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

namespace Adsfwc\Api;

defined('ABSPATH') || exit;

use WP_Roles;

/**
 * Class RegisterTab
 * @package Adsfwc\Api
 *
 * Class contain registration of additional custom tab
 * for WooCommerce оn the product edit\create page.
 */
class RegisterTab
{
    /**
     * The static method for registering filter action in init.php file
     */
    public static function register()
    {
        $instance = new self;

        add_filter('woocommerce_product_data_tabs', [$instance, 'regProductDiscountTab']);
        add_filter('woocommerce_product_data_panels', [$instance, 'discountTabContent']);
        add_action('woocommerce_process_product_meta', [$instance, 'saveTabContent']);

    }

    /**
     * @param $tabs
     * @return mixed
     * Method hooked to a woocommerce_product_data_tabs filter action.
     * Add the new tab to the $tabs array.
     * Create the custom tab (the tab is the clickable element down the left of the Product data section).
     */
    public static function regProductDiscountTab($tabs)
    {
        $tabs['discounts_tab'] = [
            'label' => __('Role discounts', 'advanced-discounts'),
            'target' => 'discounts_product_data',
            'class' => [],
            'priority' => 9999
        ];

        return $tabs;
    }

    /**
     * Method hooked to a woocommerce_product_data_panels filter action.
     * Add the custom fields to the custom panel (the panel is the element that's displayed when you click a tab).
     * @see https://woocommerce.github.io/code-reference/namespaces/default.html#function_woocommerce_wp_text_input
     */
    public static function discountTabContent()
    {
        $wp_roles = new WP_Roles();
        ?>
        <div id="discounts_product_data" class="panel woocommerce_options_panel hidden">
            <div class="options_group">
                <h1><?php _e('There you can set discount for different users role.', 'advanced-discounts'); ?></h1>
                <p><?php _e('Discount calculate in % and must be typed as an integer from 0 to 100. If the field is left blank, will be displayed the price that specified in the General tab', 'advanced-discounts'); ?></p>
                <?php
                foreach ($wp_roles->get_names() as $role) {

                    $role_code = strtolower(preg_replace('/\s+/', '_', $role));
                    woocommerce_wp_text_input([
                        'id' => '_' . $role_code . '_discount_input',
                        'label' => __('Discount for ' . $role, 'advanced-discounts'),
                        'wrapper_class' => 'discount_input',
                        'type' => 'number',
                        'custom_attributes' => [
                            'step' => '1',
                            'min' => '0',
                            'max' => '100',
                            'autocomplete' => 'off',
                            'size' => '3',
                            'pattern' => '[0-9]{,3}'
                        ]
                    ]);
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * @param $post_id
     * Method sanitizes and saves the custom fields using CRUD method
     */
    public function saveTabContent($post_id)
    {
        $wp_roles = new WP_Roles();
        $product = wc_get_product($post_id);

        foreach ($wp_roles->get_names() as $role) {

            $role = strtolower(preg_replace('/\s+/', '_', $role));
            $discount_amount = filter_input(INPUT_POST, '_' . $role . '_discount_input', FILTER_SANITIZE_NUMBER_INT) ?? '0';
            $product->update_meta_data('_' . $role . '_discount_input', $discount_amount);
        }

        $product->save();
    }

}