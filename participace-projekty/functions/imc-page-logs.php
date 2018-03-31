<?php

/**
 * 10.01
 * Create Admin Page with "All Logs"
 *
 */

add_action('admin_menu', 'imc_on_create_add_logs_page');


function imc_on_create_add_logs_page() {

	add_submenu_page('edit.php?post_type=imc_issues', 'Logs', __('Logs','participace-projekty'), 'manage_options', 'mylogs', 'imc_render_logs');


	function imc_render_logs() {

		echo '<h2>' . __('Logs','participace-projekty') . '</h2>';

		$logs = imc_get_logs(); ?>

		<table id="IMCBackendTableStyle" class="IMCBackendTableStyle paginated">
			<thead id="headings" class="IMCBackendTableHeaderStyle">
			<tr>
				<th id="IssueID"><?php _e('Issue ID','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
				<th id="IssueTitle"><?php _e('Issue Title','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
				<th id="timeLog"><?php _e('Date','participace-projekty'); ?><span class="dashicons dashicons-sort"></span></th>
				<th id="status_transition"><?php _e('Activity','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
				<th id="theUser"><?php _e('User','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
				<th id="content"><?php _e('Reason','participace-projekty') ?><span class="dashicons dashicons-sort"></span></th>
			</tr>
			</thead>
			<tbody id="results">

			<?php

			if ( $logs ) {
				foreach ($logs as $log) { ?>
					<tr>
						<td><?php echo esc_html($log->issueid);?></td>
						<?php $issue_title = get_the_title( $log->issueid );?>
						<td><?php echo esc_html($issue_title);?></td>
						<td><?php
							$timeLocal = get_date_from_gmt($log->created, 'Y-m-d H:i:s');
							echo esc_html($timeLocal);?></td>
						<td><?php echo esc_html($log->transition_title);?></td>
						<?php $user_info = get_userdata($log->created_by);
						$user_name = $user_info->user_login; ?>
						<td><?php echo esc_html($user_name);?></td>
						<td><?php echo esc_html($log->description);?></td>
					</tr>
				<?php }
			} ?>

			</tbody>
		</table>


		<script type="text/javascript" language="javascript" >
			jQuery(document).ready(function($){
				//you can now use $ as your jQuery object.
				/*console.log( "document loaded" );*/
			});

			jQuery(document).ready(function($){

				$('table.paginated').each(function() {
					var currentPage = 0;
					var numPerPage = 10;
					var $table = $(this);
					$table.bind('repaginate', function() {
						$table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
					});
					$table.trigger('repaginate');
					var numRows = $table.find('tbody tr').length;
					var numPages = Math.ceil(numRows / numPerPage);
					var $pager = $('<div class="pager"></div>');
					for (var page = 0; page < numPages; page++) {
						$('<span class="page-number"></span>').text(page + 1).bind('click', {
							newPage: page
						}, function(event) {
							currentPage = event.data['newPage'];
							$table.trigger('repaginate');
							$(this).addClass('active').siblings().removeClass('active');
						}).appendTo($pager).addClass('clickable');
					}
					$pager.insertBefore($table).find('span.page-number:first').addClass('active');
				});
			});
		</script>

		<style>
			div.pager {
				text-align: center;
				margin: 1em 0;
			}

			div.pager span {
				display: inline-block;
				width: 1.8em;
				height: 1.8em;
				line-height: 1.8;
				text-align: center;
				cursor: pointer;
				background: #000;
				color: #fff;
				margin-right: 0.5em;
			}

			div.pager span.active {
				background: #c00;
			}
		</style>
	<?php
	}
}
