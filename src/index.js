import { registerBlockType } from "@wordpress/blocks";
import "./style.scss";

import metadata from "./block.json";
import EditWithDispatch from "./edit";

registerBlockType(metadata.name, {
	attributes: {
		headers: {
			type: "object",
			default: {},
		},
		rows: {
			type: "object",
			default: {},
		},
		columnVisibility: {
			type: 'object',
			default: {},
		},
	},
	edit: EditWithDispatch,
});
