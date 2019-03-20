 <div id="learn-press-order-review" class="checkout-review-order">

	<?php
	/**
	 * @deprecated
	 */
	//do_action( 'learn_press_checkout_order_review' );

	/**
	 * @since 3.0.0
	 *
	 * @see   learn_press_order_review()
	 * @see   learn_press_order_comment()
	 * @see   learn_press_order_payment()
	 */
	do_action( 'learn-press/checkout-order-review' );
	?>

</div>