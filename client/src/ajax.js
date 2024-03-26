



import $ from 'jquery';
import Utils from 'utils';

let isConfigured = false;

let defaultTimeout;

let apiUrl;

let beforeSend;

let onSuccess;

let onError;

let onTimeout;





const baseUrl = Utils.obtainBaseUrl();



const Ajax = Espo.Ajax = {

    
    request: function (url, method, data, options) {
        options = options || {};

        const timeout = 'timeout' in options ? options.timeout : defaultTimeout;
        const contentType = options.contentType || 'application/json';
        let body;

        if (options.data && !data) {
            data = options.data;
        }

        if (apiUrl) {
            url = Espo.Utils.trimSlash(apiUrl) + '/' + url;
        }

        if (!['GET', 'OPTIONS'].includes(method) && data) {
            body = data;

            if (contentType === 'application/json' && typeof data !== 'string') {
                body = JSON.stringify(data);
            }
        }

        if (method === 'GET' && data) {
            const part = $.param(data);

            url.includes('?') ?
                url += '&' :
                url += '?';

            url += part;
        }

        const urlObj = new URL(baseUrl + url);

        const xhr = new Xhr();
        xhr.timeout = timeout;
        xhr.open(method, urlObj);
        xhr.setRequestHeader('Content-Type', contentType);

        if (options.headers) {
            for (const key in options.headers) {
                xhr.setRequestHeader(key, options.headers[key]);
            }
        }

        if (beforeSend) {
            beforeSend(xhr, options);
        }

        const promiseWrapper = {};

        const promise = new AjaxPromise((resolve, reject) => {
            const onErrorGeneral = (isTimeout) => {
                if (options.error) {
                    options.error(xhr, options);
                }

                reject(xhr, options);

                if (isTimeout) {
                    if (onTimeout) {
                        onTimeout(xhr, options);
                    }

                    return;
                }

                if (onError) {
                    onError(xhr, options);
                }
            };

            xhr.ontimeout = () => onErrorGeneral(true);
            xhr.onerror = () => onErrorGeneral();

            xhr.onload = () => {
                if (xhr.status >= 400) {
                    onErrorGeneral();

                    return;
                }

                let response = xhr.responseText;

                if ((options.dataType || 'json') === 'json') {
                    try {
                        response = JSON.parse(xhr.responseText);
                    }
                    catch (e) {
                        console.error('Could not parse API response.');

                        onErrorGeneral();
                    }
                }

                if (options.success) {
                    options.success(response);
                }

                onSuccess(xhr, options);

                if (options.resolveWithXhr) {
                    response = xhr;
                }

                resolve(response)
            }

            xhr.send(body);

            if (promiseWrapper.promise) {
                promiseWrapper.promise.xhr = xhr;

                return;
            }

            promiseWrapper.xhr = xhr;
        });

        promiseWrapper.promise = promise;
        promise.xhr = promise.xhr || promiseWrapper.xhr;

        return promise;
    },

    
    postRequest: function (url, data, options) {
        if (data) {
            data = JSON.stringify(data);
        }

        return  Ajax.request(url, 'POST', data, options);
    },

    
    patchRequest: function (url, data, options) {
        if (data) {
            data = JSON.stringify(data);
        }

        return  Ajax.request(url, 'PATCH', data, options);
    },

    
    putRequest: function (url, data, options) {
        if (data) {
            data = JSON.stringify(data);
        }

        return  Ajax.request(url, 'PUT', data, options);
    },

    
    deleteRequest: function (url, data, options) {
        if (data) {
            data = JSON.stringify(data);
        }

        return  Ajax.request(url, 'DELETE', data, options);
    },

    
    getRequest: function (url, data, options) {
        return  Ajax.request(url, 'GET', data, options);
    },

    
    configure: function (options) {
        if (isConfigured) {
            throw new Error("Ajax is already configured.");
        }

        apiUrl = options.apiUrl;
        defaultTimeout = options.timeout;
        beforeSend = options.beforeSend;
        onSuccess = options.onSuccess;
        onError = options.onError;
        onTimeout = options.onTimeout;

        isConfigured = true;
    },
};


class AjaxPromise extends Promise {

    
    xhr = null

    isAborted = false

    
    fail(...args) {
        return this.catch(args[0]);
    }
    
    done(...args) {
        return this.then(args[0]);
    }

    
    abort() {
        this.isAborted = true;

        if (this.xhr) {
            this.xhr.abort();
        }
    }

    
    getReadyState() {
        if (!this.xhr) {
            return 0;
        }

        return this.xhr.readyState || 0;
    }

    
    getStatus() {
        if (!this.xhr) {
            return 0;
        }

        return this.xhr.status;
    }
}


class Xhr extends XMLHttpRequest {
    
    errorIsHandled = false
}

export default Ajax;
