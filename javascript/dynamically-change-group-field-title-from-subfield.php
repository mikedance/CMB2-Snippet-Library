<?php
/**
 * Custom Repeatable Group.
 * The "group_title" will display the value of the 'title' sub-field, if it exists,
 * or fall back to the default CMB2 group title method.
 */

add_action( 'cmb2_init', 'yourprefix_register_repeatable_group_field_title_example' );
/**
 * Hook in and add a metabox to demonstrate repeatable grouped fields
 */
function yourprefix_register_repeatable_group_field_title_example() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_yourprefix_group_titles_';

	/**
	 * Repeatable Field Groups
	 */
	$cmb_group = new_cmb2_box( array(
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Repeating Field Group with Updating Titles', 'cmb2' ),
		'object_types' => array( 'page', ),
		'show_in_rest' => 'read_and_write',
	) );

	// $group_field_id is the field id string, so in this case: $prefix . 'demo'
	$group_field_id = $cmb_group->add_field( array(
		'id'          => $prefix . 'demo',
		'type'        => 'group',
		'description' => __( 'Generates reusable form entries', 'cmb2' ),
		'options'     => array(
			'group_title'    => __( 'Entry {#}', 'cmb2' ), // {#} gets replaced by row number
			'add_button'     => __( 'Add Another Entry', 'cmb2' ),
			'remove_button'  => __( 'Remove Entry', 'cmb2' ),
			'sortable'       => true, // beta
		),
		'after_group' => 'yourprefix_add_js_for_repeatable_titles',
	) );

	$cmb_group->add_group_field( $group_field_id, array(
		'name' => 'Title',
		'id'   => 'title',
		'type' => 'text',
	) );

	$cmb_group->add_group_field( $group_field_id, array(
		'name' => 'Description',
		'id'   => 'description',
		'type' => 'textarea_small',
	) );

}

function yourprefix_add_js_for_repeatable_titles() {
	add_action( 'admin_footer', 'yourprefix_add_js_for_repeatable_titles_to_footer' );
}

function yourprefix_add_js_for_repeatable_titles_to_footer() {
	?>
	<script type="text/javascript">
	jQuery( function( $ ) {
		var $box = $( document.getElementById( '_yourprefix_group_titles_metabox' ) );

		var replaceTitles = function() {
			$box.find( '.cmb-group-title' ).each( function() {
				var $this = $( this );
				var txt = $this.next().find( '[id$="title"]' ).val();
				var rowindex;

				if ( ! txt ) {
					txt = $box.find( '[data-grouptitle]' ).data( 'grouptitle' );
					if ( txt ) {
						rowindex = $this.parents( '[data-iterator]' ).data( 'iterator' );
						txt = txt.replace( '{#}', ( rowindex + 1 ) );
					}
				}

				if ( txt ) {
					$this.text( txt );
				}
			});
		};

		var replaceOnKeyUp = function( evt ) {
			var $this = $( evt.target );
			var id = 'title';

			if ( evt.target.id.indexOf(id, evt.target.id.length - id.length) !== -1 ) {
				console.log( 'val', $this.val() );
				$this.parents( '.cmb-row.cmb-repeatable-grouping' ).find( '.cmb-group-title' ).text( $this.val() );
			}
		};

		$box
			.on( 'cmb2_add_row cmb2_shift_rows_complete', function( evt ) {
				replaceTitles();
			})
			.on( 'keyup', replaceOnKeyUp );

		replaceTitles();
	});
	</script>
	<?php
}
