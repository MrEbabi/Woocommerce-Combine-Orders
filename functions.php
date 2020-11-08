add_action( 'manage_posts_extra_tablenav', 'admin_order_list_top_combine_button', 20, 1 );

function admin_order_list_top_combine_button( $which ) {
    global $pagenow, $typenow;
    if ( 'shop_order' === $typenow && 'edit.php' === $pagenow && 'top' === $which ) {
        $content = "<div class='alignleft actions custom'>
                        <input type='number' name='first_order' min=0 placeholder='Order ID - 1'>
                        <input type='number' name='second_order' min=0 placeholder='Order ID - 2'>
                        <button type='submit' name='combineOrders' style='height:32px;' class='button' value='yes'>Combine Orders</button>
                    </div>";
        echo $content;
    }
}

add_action( 'restrict_manage_posts', 'display_admin_shop_order_combine_orders' );

function display_admin_shop_order_combine_orders() {
    global $pagenow, $typenow;

    if ( 'shop_order' === $typenow && 'edit.php' === $pagenow &&
    isset($_GET['combineOrders']) && $_GET['combineOrders'] === 'yes' )
    {
        if(isset($_GET['first_order']) && !empty($_GET['first_order']) && isset($_GET['second_order']) && !empty($_GET['second_order']))
        {
            $first_order_id = $_GET['first_order'];
            $second_order_id = $_GET['second_order'];
            
            $first_order = wc_get_order($first_order_id);
            $second_order = wc_get_order($second_order_id);
            
            foreach( $first_order->get_items() as $item_id => $item )
            {
                $product_id = $item->get_product_id();
                $variation_id = $item->get_variation_id();
                $product_quantity = $item->get_quantity();
                
                //if product type is simple
                if($variation_id == 0)
                {
                    $product = wc_get_product($product_id);
                    $second_order->add_product($product, $product_quantity);
                }
                //if product type is variable
                else
                {
                    $variation = wc_get_product($variation_id);
                    $second_order->add_product($variation, $product_quantity);
                }
            }
            $status = $second_order->get_status();
            $second_order->calculate_totals();
            $second_order->update_status('pending');
            $second_order->update_status($status);
            $first_order->update_status("cancelled", "This order is cancelled since it is combined with order ID: $second_order_id.");
            $second_order->add_order_note("Order ID: $first_order_id is cancelled and combined with this order.");
        }
        $URL = get_admin_url();
        $URL .= "edit.php?post_type=shop_order";
        echo "<script>if(!alert('Order ID: $first_order_id is cancelled and combined with Order ID: $second_order_id')){ window.location.href = '$URL'; }</script>";
    }
}
