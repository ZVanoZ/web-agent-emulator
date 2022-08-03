const APP = {
	run: function() {
		document.querySelector('#idPhpinfoClientContainer')
			.querySelector('button')
			.addEventListener(
				'click',
				this.handlerPhpinfoClient.bind(this)
			);
		document.querySelector('#idApiPhotoClientContainer')
			.querySelector('button[itemid="get-request"]')
			.addEventListener(
				'click',
				this.handlerApiPhotoGet.bind(this)
			);
	},
	mask: {
		getEl: function() {
			let
				el = document.getElementById('idMaskContainer'),
				result = bootstrap.Modal.getOrCreateInstance(el, {
					keyboard: false
				});
			return result;
		},
		show: function() {
			//this.getEl().modal();
			//this.getEl().show();
		},
		hide: function() {
			//this.getEl().modal('hide');
		}
	},
	handlerPhpinfoClient: function() {
		let
			baseUrl = this.getBaseUrl(),
			iframeEl = document.getElementById('idResultIframe')
		;
		iframeEl.removeAttribute('src');
		iframeEl.onload = () => {
			console.log('iframe loaded')
			setTimeout(() => {
				this.mask.hide();
			}, 3000)
		}
		iframeEl.onerror = () => {
			console.log('iframe error')
			setTimeout(() => {
				this.mask.hide();
			}, 3000)
		}
		this.mask.show();
		//iframeEl.src = baseUrl;
		iframeEl.src = '/phpinfo.php'
	},
	handlerApiPhotoGet:function(){
		this.getPhoto();
	},
	getBaseUrl: function() {
		const result = document.querySelector('input[name="BASE_URL"]').value;
		return result;
	},
	getApiVersion: function() {
		const result = document.querySelector('input[name="API_VERSION"]').value;
		return result;
	},
	getHeaderAcceptLanguage: function() {
		const result = document.querySelector('select[name="HEADER_ACCEPT_LANGUAGE"]').value;
		return result;
	},
	getHeaderXDebugIsAllowOrigin: function() {
		const result = document.querySelector('input[name="HEADER_X_DEBUG_IS_ALLOW_ORIGIN"]').checked;
		return result;
	},
	getServerInfo: function() {
		this.sendByXhr({
			method: 'GET',
			url: this.getBaseUrl(),
			headers: {
				'Accept-Language': this.getHeaderAcceptLanguage(),
				'X-DEBUG-IS-ALLOW-ORIGIN': this.getHeaderXDebugIsAllowOrigin(),
			},
			onSuccess: function(xhr, json) {
				elResultText.innerHTML = xhr.responseText;
				if (typeof (json.result) === 'object') {
					if (json.result.data !== undefined
						&& typeof (json.result.mimetype) === 'string'
					) {
						elResultImage.src = 'data:' + json.result.mimetype + ';base64,' + json.result.data; //data:'image/jpeg;base64
					}
				}
			}.bind(this),
			onFailure: function(xhr) {
				elResultText.innerHTML = xhr.responseText;
			}.bind(this),
		});
	},
	getElResultText : function(){
		return document.querySelector('[itemid="RESULT_TEXT"]');
	},
	getElResultImage : function(){
		return document.querySelector('[itemid="RESULT_IMAGE"]');
	},
	getElResultIframe : function(){
		return document.getElementById('idResultIframe');
	},
	getPhoto: function() {
		var
			elResultText = this.getElResultText(),
			elResultImage = this.getElResultImage(),
			elResultIframe = this.getElResultIframe()
		;
		elResultText.innerHTML = '';
		elResultImage.src = '';
		elResultIframe.src = '';
		setTimeout(()=>{
			this.sendByXhr({
				method: 'GET',
				url: this.getBaseUrl() + '/photo',
				body: {
					hello: 'world'
				},
				headers: {
					'X-API-VERSION': this.getApiVersion(),
					'X-DEBUG-IS-ALLOW-ORIGIN': this.getHeaderXDebugIsAllowOrigin(),
					'Accept-Language': this.getHeaderAcceptLanguage()
				},
				onSuccess: function(xhr, json) {
					elResultText.innerHTML = xhr.responseText;
					elResultIframe.src = 'data:application/json;text' + xhr.responseText
					if (typeof (json.result) === 'object') {
						if (json.result.data !== undefined
							&& typeof (json.result.mimetype) === 'string'
						) {
							elResultImage.src = 'data:' + json.result.mimetype + ';base64,' + json.result.data; //data:'image/jpeg;base64
						}
					}
				}.bind(this),
				onFailure: function(xhr) {
					elResultText.innerHTML = xhr.responseText;
				}.bind(this),
			});
		}, 3000)
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