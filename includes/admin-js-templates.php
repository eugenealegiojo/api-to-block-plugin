<script type="text/html" id="tmpl-eugene-api-table-html">
	<#
	var apiData  = data.data,
		title    = data.hasOwnProperty('title') ? data.title : '',
		headers  = apiData.hasOwnProperty('headers') ? apiData.headers : null,
		dataRows = apiData.hasOwnProperty('rows') ? apiData.rows : null;

	if ( title ) { #>
		<h2>{{title}}</h2>
	<# } #>

	<table class="wp-list-table eugene-api-table-list widefat fixed striped">
	<thead><tr>
		<#
		if ( headers ) {
			for( var header in headers ) { #>
				<th scope="col">{{headers[header]}}</th>
			<# }
		}
		#>
	</tr></thead>
	<tbody>
		<#
		if ( dataRows ) {
			for( var row in dataRows ) {
				#>
				<tr>
					<# if ( 'object' === typeof dataRows[row] ) {
						for( var field in dataRows[row] ) {
							if ( 'date' === field ) {
								var date = new Date( dataRows[row]['date'] * 1000 ).toLocaleDateString('en-US');
							}
							#>
							<td>
							<# if ( ['id', 'fname', 'lname'].includes( field ) ) { #>
								{{dataRows[row][field]}}
							<# } else if ( 'email' === field ) { #>
								<a href="mailto:{{encodeURIComponent(dataRows[row][field])}}">{{dataRows[row][field]}}</a>
							<# } else if ( 'date' === field ) { #>
								{{date}}
							<# } #>
							</td>

						<# }
					}
					#>
				</tr>
				<#
			}
		} else {
		#>
		<tr><td colspan="5" align="center"><?php _e( 'No results found.', 'eugene-api' ); ?></td></tr>
		<#
		}
		#>
	</tbody>
	</table>
</script>
<!-- #tmpl-eugene-api-table-html -->
