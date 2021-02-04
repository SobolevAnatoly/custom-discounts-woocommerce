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

/**
 * Class ChangePrice
 * @package Adsfwc\Api
 * class for recalculation and replacement of the base price taking into account
 * the user role discount (on frontend)
 */
class ChangePrice
{
    /**
     * The static method for registering filter action in init.php file
     */
    public static function register()
    {
        $instance = new self;

        add_filter('woocommerce_get_price_html', [$instance, 'showAdvancedDiscountAmount'], 10, 2);
        add_action('woocommerce_before_calculate_totals', [$instance, 'showAdvancedDiscountAmountCart'], 999);
    }

    /**
     * @param $price
     * @param $product
     * @return string|null
     * The method recalculates the price taking into account the discount and
     * replaces the basic price. (single product and loop)
     * Returns the base price if in the admin panel.
     */
    public function showAdvancedDiscountAmount($price, $product)
    {
        if (is_admin()) return $price;

        $product_id = $product->get_id();
        $discount_data = $this->getRolesAndDiscounts($product_id);
        $role = $discount_data->role ?? null;
        $discount_amount = !empty($discount_data->discount) ? $discount_data->discount : null;

        if ($discount_amount && wc_current_user_has_role($role)) {
            $orig_price = $product->get_regular_price();
            $price = !empty($orig_price) ? wc_price($orig_price - ($orig_price * ($discount_amount / 100))) : null;
        }


        return $price;
    }

    /**
     * @param $cart
     * The method recalculates the price taking into account the discount
     * and replaces the basic price. (In Cart)
     */
    public function showAdvancedDiscountAmountCart($cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) return;

        if (did_action('woocommerce_before_calculate_totals') >= 2) return;

        $role = $this->getRolesAndDiscounts()->role ?? null;
        if (!wc_current_user_has_role($role)) return;

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $product_id = $product->get_id();
            $discount_data = $this->getRolesAndDiscounts($product_id);
            $discount_amount = !empty($discount_data->discount) ? $discount_data->discount : null;
            $orig_price = isset($discount_amount) ? $product->get_regular_price() : null;
            $price = !empty($orig_price) ? $orig_price - ($orig_price * ($discount_data->discount / 100)) : null;

            if ($price)
                $cart_item['data']->set_price($price);


        }

    }

    /**
     * @param null $product_id
     * @return mixed
     * The method determines the role of the current visitor and returns an object
     * containing the role and discount amount
     */
    public function getRolesAndDiscounts($product_id = null)
    {
        if (is_user_logged_in()) {
            $roles = [];
            $user_roles = wp_get_current_user()->roles;

            foreach ($user_roles as $role) {
                $role = strtolower(preg_replace('/\s+/', '_', $role));
                $role_discount = !empty(
                get_post_meta(
                    $product_id,
                    '_' . $role . '_discount_input',
                    true
                )
                ) ? get_post_meta(
                    $product_id,
                    '_' . $role . '_discount_input',
                    true
                ) : '';

                $roles = ['role' => $role, 'discount' => $role_discount];

            }

            return json_decode(json_encode($roles));
        }
    }

}