<?php
	/**
	 * Plugin Name:     Customer Reports Woocommerce
	 * Description:     Adds new report: Sales by Customer. View, find and export your best customers.
	 * Version:         1.0
	 * Author:          goliver79@gmail.com
	 * Text Domain:     wc-go-customer-reports
	 * Domain Path:     /languages
	 * License:         GPL2
	 */

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	if( !class_exists('WoocommerceGoCustomerReports')) {
		include 'includes/WCGoReportSalesByCustomer.php';
		class WoocommerceGoCustomerReports {
			public function __construct() {
				add_filter( 'woocommerce_admin_reports', array($this,'sales_by_customer_admin_reports'), 10, 1 );

				add_action('admin_enqueue_scripts', array($this,'enqueue_scripts_styles'));
			}

			function sales_by_customer_admin_reports( $reports ) {
				$sales_by_customer = array(
					'sales_by_customer' => array(
						'title'       => __('Sales by Customer', 'wc-go-customer-reports'),
						'description' => '',
						'hide_title'  => TRUE,
						'function'    => array( __CLASS__, 'sales_by_customer_callback'),
					),
				);

				// This can be: orders, customers, stock or taxes, based on where we want to insert our new reports page
				$reports[ 'orders' ][ 'reports' ] = array_merge( $reports[ 'orders' ][ 'reports' ], $sales_by_customer );

				return $reports;
			}

			function sales_by_customer_callback() {
				$report = new WCGoReportSalesByCustomer();
				$report->output_report();
			}

			function enqueue_scripts_styles($hook){
				if ( 'woocommerce_page_wc-reports' != $hook ) {
					return;
				}

				wp_enqueue_script( 'wc-go-cr-datatables', plugins_url( '/js/datatables.min.js', __FILE__ ), array('jquery') );
				wp_enqueue_script('wc-go-cr-reports-customers', plugins_url( '/js/main.js', __FILE__ ), array('wc-go-cr-datatables'));
				wp_enqueue_style( 'wc-go-cr-datatables-css', plugins_url( '/css/datatables.min.css', __FILE__ ) );
//				wp_enqueue_style( 'wc-go-cr-datatables-css', '//cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.css', array(), '1.0','all');
			}
		}

		new WoocommerceGoCustomerReports();
	}
