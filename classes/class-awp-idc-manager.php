<?php
class AWP_IDC_Manager {
	public function add_referral( $user_id, $price, $product_id, $order_id)
	{
		// See if affiliate ID present in cookie
		$affiliate_id = affiliate_wp()->tracking->get_affiliate_id();
		$affiliate_id = apply_filters('awp_idc_get_affiliate_id', $affiliate_id);

		if(!$affiliate_id){
			return;
		}

		// Ensure affiliate is active
		if ( !affwp_is_active_affiliate( $affiliate_id ) ) {
			return;
		}

		// Get visit ID from cookie
		$visit_id =  affiliate_wp()->tracking->get_visit_id();

		// Get referral rate
		$affiliate_rate = affiliate_wp()->settings->get( 'referral_rate', 15 );

		// Fetch order to get transaction ID and level name
		$order = new ID_Member_Order($order_id);
		$order_details = $order->get_order();
		$transaction_id = $order_details->transaction_id;
		$level = ID_Member_Level::get_level($product_id);
		$level_name = $level->level_name;

		// Log affiliate purchase
		$referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $affiliate_id,
			'amount'       => $price*$affiliate_rate/100,
			'status'       => 'unpaid',
			'description'  => "IgnitionDeck Package - $level_name \$$price",
			'context'      => 'ignitiondeck',
			'campaign'     => '',
			'reference'    => $transaction_id,
			'visit_id'	   => $visit_id
		) );

		if( $referral_id ) {
			affiliate_wp()->visits->update( $visit_id, array( 'referral_id' => $referral_id ), '', 'visit' );
		}

		do_action('awp_idc_referral_created', $referral_id);

	}

}