<?php
	require_once(WP_PLUGIN_DIR.'/woocommerce/includes/admin/reports/class-wc-admin-report.php');
	class WCGoReportSalesByCustomer extends WC_Admin_Report {

		/**
		 * Output the report.
		 */
		public function output_report() {
			$ranges = array(
				'year'         => __( 'Year', 'woocommerce' ),
				'last_month'   => __( 'Last month', 'woocommerce' ),
				'month'        => __( 'This month', 'woocommerce' ),
			);
			$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : 'month';
			if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', '7day' ) ) ) {
				$current_range = 'month';
			}
			$this->check_current_range_nonce( $current_range );
			$this->calculate_current_range( $current_range );
			$hide_sidebar = true;
			include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
		}

		/**
		 * Get the main chart.
		 */
		public function get_main_chart() {
			$query_data = array(
				'_customer_user' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'customer_user',
				),
				'_billing_email' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'user_email'
				),
				'_billing_country' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'billing_country'
				),
				'_billing_first_name' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'first_name'
				),
				'_billing_last_name' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'last_name'
				),
				'ID' => array(
					'type'      => 'post_data',
					'function'  => 'COUNT',
					'name'      => 'orders',
                    'distinct' => true
				),
				'_order_total'   => array(
					'type'      => 'meta',
					'function'  => 'SUM',
					'name'      => 'order_total'
				),
			);

			$sales_by_country_orders = $this->get_order_report_data( array(
				'data'                  => $query_data,
				'query_type'            => 'get_results',
				'group_by'              => 'customer_user, user_email', 'billing_country',
				'order_by'              => 'orders DESC',
				'filter_range'          => true,
				'order_types'           => wc_get_order_types( 'sales-reports' ),
				'order_status'          => array( 'completed' ),
				'parent_order_status'   => false,
			) );
			?>
			<table class="widefat sales-by-customer">
				<thead>
				<tr>
					<th><strong><?php _e('Customer ID', 'wc-go-customer-reports');?></strong></th>
					<th><strong><?php _e('Customer', 'wc-go-customer-reports');?></strong></th>
					<th><strong><?php _e('Email', 'wc-go-customer-reports');?></strong></th>
					<th><strong><?php _e('Country', 'wc-go-customer-reports');?></strong></th>
					<th><strong><?php _e('Orders', 'wc-go-customer-reports');?></strong></th>
					<th><strong><?php _e('Total', 'wc-go-customer-reports');?></strong></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach( $sales_by_country_orders as $order ) {
					?>
					<tr>
						<td><?php echo $order->customer_user; ?></td>
						<td><?php
								echo $order->first_name . " " . $order->last_name;
                               if((int)$order->customer_user !== 0 ){
                                   echo "<a href='/wp-admin/user-edit.php?user_id=".$order->customer_user."'> ".
                                            __('Profile', 'wc-go-customer-reports').
                                        "</a>";

                               }
							?></td>
						<td><?php echo $order->user_email; ?></td>
						<td><?php echo $order->billing_country; ?></td>
						<td><?php echo $order->orders; ?></td>
						<td><?php echo number_format($order->order_total, 2); ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<?php

		}
	}
