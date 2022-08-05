const Helper = {
	escapeHtml: function escapeHtml(text) {
		return text
			.replace(/&/g, "&amp;")
			.replace(/</g, "&lt;")
			.replace(/>/g, "&gt;")
			.replace(/"/g, "&quot;")
			.replace(/'/g, "&#039;");
	},
	/**
	 *
	 * @param {Object} options
	 * @param {string} options.method
	 * @param {string} options.url
	 * @param {object|undefined} options.headers
	 * @param {object|string} options.body
	 * @param {function} options.onSuccess
	 * @param {function} options.onFailure
	 */
	sendByXhr: function(options) {
		if (typeof (options.headers) !== 'object') {
			options.headers = {};
		}
		let xhr = new XMLHttpRequest();
		xhr.open(options.method, options.url);
		for (headerName in options.headers) {
			xhr.setRequestHeader(headerName, options.headers[headerName]);
		}
		if (!('Accept-Language' in options.headers)) {
			xhr.setRequestHeader('Accept-Language', 'uk');
		}
		if (!('Content-Type' in options.headers)) {
			xhr.setRequestHeader('Content-Type', 'application/json');
		}
		var body = typeof (options.body) === 'object' ? JSON.stringify(options.body) : options.body;
		xhr.send(body);

		xhr.addEventListener('readystatechange', () => {
			if (xhr.readyState !== 4) {
				return;
			}
			if (xhr.status !== 200) {
				return options.onFailure(xhr);
			}
			var json;
			try {
				json = JSON.parse(xhr.responseText);
			} catch (e) {
				console.log(e)
			}
			options.onSuccess(xhr, json);
		});
	}
}