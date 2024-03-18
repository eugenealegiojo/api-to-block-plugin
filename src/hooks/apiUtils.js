import { __ } from "@wordpress/i18n";
import { useEffect, useState } from 'react';

const useAPIData = ((apiEndpoint) => {
	const [data, setData] = useState({});
	const [error, setError] = useState(null);

	useEffect(() => {
		const fetchApiData = async () => {
			try {
				const response = await wp.apiFetch({ path: apiEndpoint });

				if (!response.data) {
					throw new Error(__('Invalid API response', 'eugene-api'));
				}

				setData(response.data);
			} catch (error) {
				setError(error.message || __('Something is wrong', 'eugene-api'));
			}
		}

		fetchApiData();

		// Clean up
		return () => {

		}
	}, []);

	return { data, error };
});

export default useAPIData;
