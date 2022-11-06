/**
 * AJAX call
 * @param url
 * @param parameters
 * @param method
 * @returns {Promise<any>}
 */
export function ajax(url, parameters = {}, method = 'POST') {
	let formData = new FormData();

	for (let [key, value] of Object.entries(parameters)) {
		formData.append(key, value);
	}

	const options = {
		method: method,
		body  : formData,
	};

	return fetch(url, options)
			.then((response) => {
				return response.json();
			})
			.then((data) => {
				return data;
			});
}
