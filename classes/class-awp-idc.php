<?php
class AffiliateWP_IDC_Integration {
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = 'affiliatewp-ignitiondeck-referrals';
		$this->version = '1.0.0';
	}

	public function run(){
		add_action('idmember_receipt', array($this, 'add_referral'), 10, 6 );
		add_filter( 'affwp_referral_reference_column', array($this, 'add_referral_link'), 5, 2 );
	}

	public function add_referral( $user_id, $price, $product_id, $gateway, $order_id, $fields)
	{
		// See if affiliate ID present in cookie
		$affiliate_id = affiliate_wp()->tracking->get_affiliate_id();
		if(!$affiliate_id){

			// No affiliate ID in cookie. See if the logged in user was referred on signup
			$referrals = affiliate_wp()->referrals->get_referrals( array(
				'reference' => $user_id,
				'context'       => 'ultimate_member_signup'
			) );

			if(empty($referrals)){
				return;
			}
			$referral = reset($referrals);
			$affiliate_id = $referral->affiliate_id;

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

		if( $referral_id )
			affiliate_wp()->visits->update( $visit_id , array( 'referral_id' => $referral_id ), '', 'visit' );
	}

	function add_referral_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || $referral->context != 'ignitiondeck' ) {
			return $reference;
		}

		// link to the order search
		$url = admin_url('admin.php?s=' . $reference . '&page=idc-orders&p=1');
		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}