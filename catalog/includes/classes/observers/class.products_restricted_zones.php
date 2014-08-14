<?php
/**
 * class.minimum_order_amount.php
 *
 * @copyright Copyright 2005-2007 Andrew Berezin eCommerce-Service.com
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: config.minimum_order_amount.php 1.0.1 20.09.2007 0:06 AndrewBerezin $
 */

/**
 * Observer class used to check minimum order amount
 *
 */
class productsRestrictedZones extends base {
  /**
   * constructor method
   *
   * Attaches our class to the ... and watches for 4 notifier events.
   */
  function __construct(){
    global $zco_notifier;
    $zco_notifier->attach($this, array('NOTIFY_HEADER_START_CHECKOUT','NOTIFY_HEADER_START_CHECKOUT_SHIPPING'));
  }
  /**
   * Update Method
   *
   * Called by observed class when any of our notifiable events occur
   *
   * @param object $class
   * @param string $eventID
   */
  function update(&$class, $eventID, $paramsArray) {
    global $messageStack;
	global $db;
        
        if($_SESSION['cart']->count_contents() > 0 && isset($_SESSION['customer_zone_id']) && PRODUCTS_RESTRICTED_ZONE_ENABLED == 'true'){
            $customers_zone = $_SESSION['customer_zone_id'];
            $cartProducts = $_SESSION['cart']->get_products();
            $_SESSION['debug'] = 'products restricted zones';
        
            foreach($cartProducts as $product )
            {
                $product_id = $product['id'];
                    if(product_restricted_zone_only($product_id,$customers_zone) == false){
                        $error_text = sprintf(TEXT_PRODUCTS_RESTRICTED_ZONE, zen_get_products_name($product_id));
                        $messageStack->add('shopping_cart', $error_text . '<br />', 'caution');
                        
                        zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
                        return;
                    }
                    if(product_restricted_zone_cant($product_id,$customers_zone) == true){
                        $error_text = sprintf(TEXT_PRODUCTS_RESTRICTED_ZONE, zen_get_products_name($product_id));
                        $messageStack->add('shopping_cart', $error_text . '<br />', 'caution');
                        
                        zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
                        return;
                    }
            }
            
            return;
        }
        return;
  }
}
