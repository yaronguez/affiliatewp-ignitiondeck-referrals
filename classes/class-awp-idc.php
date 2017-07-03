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
	public function __construct() {

		$this->plugin_name = 'affiliatewp-ignitiondeck-referrals';
		$this->version = '1.0.0';

		require_once plugin_dir_path( __FILE__ ) . 'class-awp-idc-manager.php';
	}

	public function run(){
		add_action('idmember_receipt', array($this, 'add_referral'), 10, 6 );
		add_filter( 'affwp_referral_reference_column', array($this, 'add_referral_link'), 5, 2 );
	}

	public function add_referral( $user_id, $price, $product_id, $gateway, $order_id, $fields)
	{
		$awp_idc_manager = new AWP_IDC_Manager();
		$awp_idc_manager->add_referral($user_id, $price, $product_id, $order_id);
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