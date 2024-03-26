



const IS_MAC = /Mac/.test(navigator.userAgent);


Espo.Utils = {

    
    handleAction: function (view, event, element, actionData) {
        actionData = actionData || {};

        const $target = $(element);
        const action = actionData.action || $target.data('action');

        const name = $target.data('name') || action;

        let method;
        let handler;

        if (
            name &&
            actionData.actionItems &&
            (
                !actionData.className ||
                element.classList.contains(actionData.className)
            )
        ) {
            const data = actionData.actionItems.find(item => {
                return item.name === name || item.action === name;
            });

            if (data && data.onClick) {
                data.onClick();

                return true;
            }

            if (data) {
                handler = data.handler;
                method = data.actionFunction;
            }
        }

        if (!action && !actionData.actionFunction && !method) {
            return false;
        }

        if (event.ctrlKey || event.metaKey || event.shiftKey) {
            const href = $target.attr('href');

            if (href && href !== 'javascript:') {
                return false;
            }
        }

        const data = $target.data();
        method = actionData.actionFunction || method || 'action' + Espo.Utils.upperCaseFirst(action);
        handler = actionData.handler || handler || data.handler;

        let fired = false;

        if (handler) {
            event.preventDefault();
            event.stopPropagation();

            fired = true;

            Espo.loader.require(handler, Handler => {
                const handler = new Handler(view);

                handler[method].call(handler, data, event);
            });
        }
        else if (typeof view[method] === 'function') {
            view[method].call(view, data, event);

            event.preventDefault();
            event.stopPropagation();

            fired = true;
        }

        if (!fired) {
            return false;
        }

        this._processAfterActionDropdown($target);

        return true;
    },

    
    _processAfterActionDropdown: function ($target) {
        const $dropdown = $target.closest('.dropdown-menu');

        if (!$dropdown.length) {
            return;
        }

        const $dropdownToggle = $dropdown.parent().find('[data-toggle="dropdown"]');

        if (!$dropdownToggle.length) {
            return;
        }

        let isDisabled = false;

        if ($dropdownToggle.attr('disabled')) {
            isDisabled = true;

            $dropdownToggle.removeAttr('disabled').removeClass('disabled');
        }

        
        $dropdownToggle.dropdown('toggle');

        $dropdownToggle.focus();

        if (isDisabled) {
            $dropdownToggle.attr('disabled', 'disabled').addClass('disabled');
        }
    },

    

    
    checkActionAvailability: function (helper, item) {
        const config = helper.config;

        if (item.configCheck) {
            let configCheck = item.configCheck;

            let opposite = false;

            if (configCheck.substring(0, 1) === '!') {
                opposite = true;

                configCheck = configCheck.substring(1);
            }

            let configCheckResult = config.getByPath(configCheck.split('.'));

            if (opposite) {
                configCheckResult = !configCheckResult;
            }

            if (!configCheckResult) {
                return false;
            }
        }

        return true;
    },

    

    
    checkActionAccess: function (acl, obj, item, isPrecise) {
        let hasAccess = true;

        if (item.acl) {
            if (!item.aclScope) {
                if (obj) {
                    if (typeof obj === 'string' || obj instanceof String) {
                        hasAccess = acl.check(obj, item.acl);
                    }
                    else {
                        hasAccess = acl.checkModel(obj, item.acl, isPrecise);
                    }
                }
                else {
                    hasAccess = acl.check(item.scope, item.acl);
                }
            }
            else {
                hasAccess = acl.check(item.aclScope, item.acl);
            }
        }
        else if (item.aclScope) {
            hasAccess = acl.checkScope(item.aclScope);
        }

        return hasAccess;
    },

    

    
    checkAccessDataList: function (dataList, acl, user, entity, allowAllForAdmin) {
        if (!dataList || !dataList.length) {
            return true;
        }

        for (const i in dataList) {
            const item = dataList[i];

            if (item.scope) {
                if (item.action) {
                    if (!acl.check(item.scope, item.action)) {
                        return false;
                    }
                } else {
                    if (!acl.checkScope(item.scope)) {
                        return false;
                    }
                }
            } else if (item.action) {
                if (entity) {
                    if (!acl.check(entity, item.action)) {
                        return false;
                    }
                }
            }

            if (item.teamIdList) {
                if (user && !(allowAllForAdmin && user.isAdmin())) {
                    let inTeam = false;

                    user.getLinkMultipleIdList('teams').forEach(teamId => {
                        if (~item.teamIdList.indexOf(teamId)) {
                            inTeam = true;
                        }
                    });

                    if (!inTeam) {
                        return false;
                    }
                }
            }

            if (item.portalIdList) {
                if (user && !(allowAllForAdmin && user.isAdmin())) {
                    let inPortal = false;

                    user.getLinkMultipleIdList('portals').forEach(portalId => {
                        if (~item.portalIdList.indexOf(portalId)) {
                            inPortal = true;
                        }
                    });

                    if (!inPortal) {
                        return false;
                    }
                }
            }

            if (item.isPortalOnly) {
                if (user && !(allowAllForAdmin && user.isAdmin())) {
                    if (!user.isPortal()) {
                        return false;
                    }
                }
            }
            else if (item.inPortalDisabled) {
                if (user && !(allowAllForAdmin && user.isAdmin())) {
                    if (user.isPortal()) {
                        return false;
                    }
                }
            }

            if (item.isAdminOnly) {
                if (user) {
                    if (!user.isAdmin()) {
                        return false;
                    }
                }
            }
        }

        return true;
    },

    
    convert: function (string, p) {
        if (string === null) {
            return string;
        }

        let result = string;

        switch (p) {
            case 'c-h':
            case 'C-h':
                result = Espo.Utils.camelCaseToHyphen(string);

                break;

            case 'h-c':
                result = Espo.Utils.hyphenToCamelCase(string);

                break;

            case 'h-C':
                result = Espo.Utils.hyphenToUpperCamelCase(string);

                break;
        }

        return result;
    },

    
    isObject: function (obj) {
        if (obj === null) {
            return false;
        }

        return typeof obj === 'object';
    },

    
    clone: function (obj) {
        if (!Espo.Utils.isObject(obj)) {
            return obj;
        }

        return _.isArray(obj) ? obj.slice() : _.extend({}, obj);
    },

    
    cloneDeep: function (data) {
        data = Espo.Utils.clone(data);

        if (Espo.Utils.isObject(data) || _.isArray(data)) {
            for (const i in data) {
                data[i] = this.cloneDeep(data[i]);
            }
        }

        return data;
    },

    
    composeClassName: function (module, name, location) {
        if (module) {
            module = this.camelCaseToHyphen(module);
            name = this.camelCaseToHyphen(name).split('.').join('/');
            location = this.camelCaseToHyphen(location || '');

            return module + ':' + location + '/' + name;
        }
        else {
            name = this.camelCaseToHyphen(name).split('.').join('/');

            return location + '/' + name;
        }
    },

    
    composeViewClassName: function (name) {
        if (name && name[0] === name[0].toLowerCase()) {
            return name;
        }

        if (name.indexOf(':') !== -1) {
            const arr = name.split(':');
            let modPart = arr[0];
            let namePart = arr[1];

            modPart = this.camelCaseToHyphen(modPart);
            namePart = this.camelCaseToHyphen(namePart).split('.').join('/');

            return modPart + ':' + 'views' + '/' + namePart;
        }
        else {
            name = this.camelCaseToHyphen(name).split('.').join('/');

            return 'views' + '/' + name;
        }
    },

    
    toDom: function (string) {
        return Espo.Utils.convert(string, 'c-h')
            .split('.')
            .join('-');
    },

    
    lowerCaseFirst: function (string) {
        if (string === null) {
            return string;
        }

        return string.charAt(0).toLowerCase() + string.slice(1);
    },

    
    upperCaseFirst: function (string) {
        if (string === null) {
            return string;
        }

        return string.charAt(0).toUpperCase() + string.slice(1);
    },

    
    hyphenToUpperCamelCase: function (string) {
        if (string === null) {
            return string;
        }

        return this.upperCaseFirst(
            string.replace(
                /-([a-z])/g,
                function (g) {
                    return g[1].toUpperCase();
                }
            )
        );
    },

    
    hyphenToCamelCase: function (string) {
        if (string === null) {
            return string;
        }

        return string.replace(
            /-([a-z])/g,
            function (g) {
                return g[1].toUpperCase();
            }
        );
    },

    
    camelCaseToHyphen: function (string) {
        if (string === null) {
            return string;
        }

        return string.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    },

    
    trimSlash: function (str) {
        if (str.slice(-1) === '/') {
            return str.slice(0, -1);
        }

        return str;
    },

    
    parseUrlOptionsParam: function (string) {
        if (!string) {
            return {};
        }

        if (string.indexOf('&') === -1 && string.indexOf('=') === -1) {
            return {};
        }

        const options = {};

        if (typeof string !== 'undefined') {
            string.split('&').forEach(item => {
                const p = item.split('=');

                options[p[0]] = true;

                if (p.length > 1) {
                    options[p[0]] = p[1];
                }
            });
        }

        return options;
    },

    
    getKeyFromKeyEvent: function (e) {
        let key = e.code;

        key = keyMap[key] || key;

        if (e.shiftKey) {
            key = 'Shift+' + key;
        }

        if (e.altKey) {
            key = 'Alt+' + key;
        }

        if (IS_MAC ? e.metaKey : e.ctrlKey) {
            key = 'Control+' + key;
        }

        return key;
    },

    
    generateId: function () {
        return (Math.floor(Math.random() * 10000001)).toString()
    },

    
    obtainBaseUrl: function () {
        let baseUrl = window.location.origin + window.location.pathname;

        if (baseUrl.slice(-1) !== '/') {
            baseUrl = window.location.pathname.includes('.') ?
                baseUrl.slice(0, baseUrl.lastIndexOf('/')) + '/' :
                baseUrl + '/';
        }

        return baseUrl;
    }
};

const keyMap = {
    'NumpadEnter': 'Enter',
};


Espo.utils = Espo.Utils;

export default Espo.Utils;
