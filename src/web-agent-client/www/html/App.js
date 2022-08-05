const APP = {
	run: function() {
		document.querySelector('#id-PhpinfoClientContainer')
			.querySelector('button')
			.addEventListener(
				'click',
				this.handlerPhpinfoClient.bind(this)
			);
		document.querySelector('#id-ApiGetServerInfoContainer')
			.querySelector('button[itemid="request-get"]')
			.addEventListener(
				'click',
				this.handlerApiGetServerInfo.bind(this)
			);
		document.querySelector('#id-ApiPhotoContainer')
			.querySelector('button[itemid="request-get"]')
			.addEventListener(
				'click',
				this.handlerApiPhotoGet.bind(this)
			);
		document.querySelector('#id-ApiJournalContainer')
			.querySelector('button[itemid="request-get"]')
			.addEventListener(
				'click',
				this.handlerApiJournalGet.bind(this)
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
		this.cleanResult();
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
	handlerApiGetServerInfo: function() {
		this.apiGetServerInfo();
	},
	handlerApiPhotoGet: function() {
		this.apiGetPhoto();
	},
	handlerApiJournalGet: function() {
		this.apiJournalGet();
	},
	Fields: {
		ApiJournal: {
			getRowId: function() {
				const
					el = document.querySelector('#id-ApiJournalContainer input[name="rowId"]'),
					result = el.value
				;
				return result;
			},
			getTraceId: function() {
				const
					el = document.querySelector('#id-ApiJournalContainer input[name="traceId"]'),
					result = el.value
				;
				return result;
			}
		}
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
	getElResultText: function() {
		return document.querySelector('[itemid="RESULT_TEXT"]');
	},
	getElResultImage: function() {
		return document.querySelector('[itemid="RESULT_IMAGE"]');
	},
	getElResultIframe: function() {
		return document.getElementById('idResultIframe');
	},
	getStandardHeaders: function() {
		return {
			'X-API-VERSION': this.getApiVersion(),
			'X-DEBUG-IS-ALLOW-ORIGIN': this.getHeaderXDebugIsAllowOrigin(),
			'Accept-Language': this.getHeaderAcceptLanguage()
		};
	},
	apiGetServerInfo: function() {
		this.cleanResult();
		const
			url = this.getBaseUrl(),
			headers = {
				'Accept-Language': this.getHeaderAcceptLanguage(),
				'X-DEBUG-IS-ALLOW-ORIGIN': this.getHeaderXDebugIsAllowOrigin(),
			},
			options = {
				method: 'GET',
				url: url,
				headers: headers,
				onSuccess: this.writeXhrResult.bind(this),
				onFailure: this.writeXhrResult.bind(this)
			}
		;
		Helper.sendByXhr(options);
	},
	apiGetPhoto: function() {
		const
			elResultImage = this.getElResultImage()
		;
		this.cleanResult();
		setTimeout(() => {
			const
				url = this.getBaseUrl() + '/photo',
				headers = this.getStandardHeaders(),
				onSuccess = function(xhr, json) {
					this.writeXhrResult(xhr);
					if (typeof (json.result) === 'object') {
						if (json.result.data !== undefined
							&& typeof (json.result.mimetype) === 'string'
						) {
							elResultImage.src = 'data:' + json.result.mimetype + ';base64,' + json.result.data; //data:'image/jpeg;base64
						}
					}
				}.bind(this),
				options = {
					method: 'GET',
					url: url,
					headers: headers,
					onSuccess: onSuccess,
					onFailure: this.writeXhrResult.bind(this)
				}
			;
			Helper.sendByXhr(options);
		}, 3000)
	},
	apiJournalGet: function() {
		this.cleanResult();
		const
			url = (
				(url) => {
					let
						searchParams = new URLSearchParams(),
						rowId = this.Fields.ApiJournal.getRowId(),
						traceId = this.Fields.ApiJournal.getTraceId()
					;
					if('' !== rowId){
						searchParams.set('id', rowId)
					}
					if('' !== traceId){
						searchParams.set('traceId', traceId)
					}
					url = url + '?' + searchParams.toString();
					return url;
				}
			)(this.getBaseUrl() + '/journal'),
			headers = this.getStandardHeaders(),
			options = {
				method: 'GET',
				url: url,
				headers: headers,
				onSuccess: this.writeXhrResult.bind(this),
				onFailure: this.writeXhrResult.bind(this)
			}
		;
		Helper.sendByXhr(options);
	},
	cleanResult: function() {
		var
			elResultText = this.getElResultText(),
			elResultImage = this.getElResultImage(),
			elResultIframe = this.getElResultIframe()
		;
		elResultText.innerHTML = '';
		elResultImage.src = '';
		elResultIframe.src = '';
	},
	/**
	 *
	 * @param {XMLHttpRequest} xhr
	 */
	writeXhrResult: function(xhr) {
		var
			elResultText = this.getElResultText(),
			elResultIframe = this.getElResultIframe()
		;
		elResultIframe.src = 'data:application/json;text' + xhr.responseText

		elResultText.innerHTML = 'http code: ' + xhr.status
			+ '<hr>' + xhr.getAllResponseHeaders()
			+ '<hr>' + Helper.escapeHtml(xhr.responseText)
		;
	}
}