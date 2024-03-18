<?php
/**
 * Block template
 */
use Eugene\API\Route;

$attributes   = $block->parsed_block['attrs'];
$response     = ( new Route() )->get_request_data();
$visible_cols = isset( $attributes['columnVisibility'] ) ? $attributes['columnVisibility'] : [];

?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>

	<?php if ( isset( $response->data ) && isset( $response->data['data'] ) ) { ?>
		<div class="eugene-api-block-wrap">
			<table class="eugene-api-block-table">
				<thead><tr>
					<?php
					if ( $response->data['data']['headers'] ) {
						foreach ( $response->data['data']['headers'] as $field => $label ) {
							if ( isset( $visible_cols[ $field ] ) && false !== $visible_cols[ $field ] ) {
								echo '<th>' . esc_html( $label ) . '</th>';
							}
						}
					}
					?>
				</tr></thead>
				<tbody>
					<?php
					if ( $response->data['data']['rows'] ) {
						foreach ( $response->data['data']['rows'] as $i => $row ) {
							echo '<tr>';
							if ( ! is_array( $row ) ) {
								continue;
							}

							foreach ( $row as $field => $value ) {
								// Check for visibility
								if ( ! isset( $visible_cols[ $field ] ) || false === $visible_cols[ $field ] ) {
									continue;
								}

								$value = esc_html( $value );
								if ( 'date' === $field ) {
									$value = date( 'd/m/Y', (int) $value );
								}
								echo '<td>' . $value . '</td>';
							}
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	} else {
		echo '<p>' . __( 'No data found.', 'eugene-api' ) . '</p>';
	}
	?>
</div>
