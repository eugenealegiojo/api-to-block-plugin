import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import { CheckboxControl, PanelBody } from "@wordpress/components";
import { withDispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import useAPIData from './hooks/apiUtils';


const Edit = ({ attributes, setAttributes }) => {
	const { headers, rows, columnVisibility } = attributes;
	const { data, error } = useAPIData('/eugene-api/v1/challenge');

	const blockProps = useBlockProps();

	useEffect(() => {
		if (!data || error) {
			console.error(__("Invalid API response", 'eugene-api'), error);
			return;
		}

		let initialVisibility = {};

		// Default to saved values if exists.
		if ( Object.keys(attributes.columnVisibility).length > 0 ) {
			initialVisibility = attributes.columnVisibility;
		}
		else if (data.headers && Object.keys(data.headers).length) {
			Object.keys(data.headers).map((header) => {
				initialVisibility[header] = true;
			});
		}

		setAttributes({
			headers: data.headers,
			rows: data.rows,
			columnVisibility: initialVisibility,
		});

	}, [data, error]);

	const toggleColumn = (columnKey) => {
		const updatedVisibility = {
			...columnVisibility,
			[columnKey]: !columnVisibility[columnKey],
		};
		setAttributes({ columnVisibility: updatedVisibility });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Column Visibility", "eugene-api")}
					initialOpen={true}
					description={__("Check to show column.", "eugene-api")}
				>
					<fieldset>
						{headers && Object.keys(headers).map((fieldKey) => (
							<CheckboxControl
								key={fieldKey}
								label={headers[fieldKey]}
								checked={columnVisibility[fieldKey]}
								onChange={() => toggleColumn(fieldKey)}
							/>
						))}
					</fieldset>
				</PanelBody>
			</InspectorControls>


			<div {...blockProps}>
				<div className="eugene-api-block-wrap">
					<table className="eugene-api-block-table">
						<thead>
							<tr>
								{headers && Object.keys(headers).map( fieldKey =>
									columnVisibility[fieldKey] && (
										<th key={fieldKey}>{headers[fieldKey]}</th>
									),
								)}
							</tr>
						</thead>
						<tbody>
							{rows && Object.keys(rows).map((rowKey, index) => (
								<tr key={index}>
									{headers && Object.keys(headers).map((fieldKey) => {
										if (columnVisibility[fieldKey]) {
											// Check date to format
											if (fieldKey === "date") {
												const date = new Date(rows[rowKey][fieldKey] * 1000);
												const formattedDate = `${date.getDate()}/${date.getMonth() + 1
													}/${date.getFullYear()}`;
												return <td key={fieldKey}>{formattedDate}</td>;
											} else {
												return <td key={fieldKey}>{rows[rowKey][fieldKey]}</td>;
											}
										}
										return null;
									})}
								</tr>
							))}
						</tbody>
					</table>
				</div>
			</div>
		</>
	);
};

// Need withDispatch to update attributes
const EditWithDispatch = withDispatch((dispatch, props) => {
	const { setAttributes } = props;
	return { setAttributes };
})(Edit);

export default EditWithDispatch;
