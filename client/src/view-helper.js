



import {marked} from 'marked';
import DOMPurify from 'dompurify';
import Handlebars from 'handlebars';


class ViewHelper {

    constructor() {
        this._registerHandlebarsHelpers();

        
        this.mdBeforeList = [
            
            {
                
                regex: /`([\s\S]*?)`/g,
                value: (s, string) => {
                    
                    return '`' + string.replace(/\\\</g, '<') + '`';
                },
            },
        ];

        marked.setOptions({
            breaks: true,
            tables: false,
        });

        DOMPurify.addHook('beforeSanitizeAttributes', function (node) {
            if (node instanceof HTMLAnchorElement) {
                if (node.getAttribute('target')) {
                    node.targetBlank = true;
                }
                else {
                    node.targetBlank = false;
                }
            }
        });

        DOMPurify.addHook('afterSanitizeAttributes', function (node) {
            if (node instanceof HTMLAnchorElement) {
                const href = node.getAttribute('href');

                if (href && !href.startsWith('#')) {
                    node.setAttribute('rel', 'noopener noreferrer');
                }

                if (node.targetBlank) {
                    node.setAttribute('target', '_blank');
                    node.setAttribute('rel', 'noopener noreferrer');
                }
            }
        });
    }

    
    layoutManager = null

    
    settings = null

    
    config = null

    
    user = null

    
    preferences = null

    
    acl = null

    
    modelFactory = null

    
    collectionFactory = null

    
    router = null

    
    storage = null

    
    sessionStorage = null

    
    dateTime = null

    
    language = null

    
    metadata = null

    
    fieldManager = null

    
    cache = null

    
    themeManager = null

    
    webSocketManager = null

    
    numberUtil = null

    
    pageTitle = null

    
    broadcastChannel = null

    
    basePath = ''

    
    appParams = null

    
    _registerHandlebarsHelpers() {
        Handlebars.registerHelper('img', img => {
            return new Handlebars.SafeString(`<img src="img/${img}" alt="img">`);
        });

        Handlebars.registerHelper('prop', (object, name) => {
            if (name in object) {
                return object[name];
            }
        });

        Handlebars.registerHelper('var', (name, context, options) => {
            if (typeof context === 'undefined') {
                return null;
            }

            let contents = context[name];

            if (options.hash.trim) {
                contents = contents.trim();
            }

            return new Handlebars.SafeString(contents);
        });

        Handlebars.registerHelper('concat', function (left, right) {
            return left + right;
        });

        Handlebars.registerHelper('ifEqual', function (left, right, options) {
            
            if (left == right) {
                return options.fn(this);
            }

            return options.inverse(this);
        });

        Handlebars.registerHelper('ifNotEqual', function (left, right, options) {
            
            if (left != right) {
                return options.fn(this);
            }

            return options.inverse(this);
        });

        Handlebars.registerHelper('ifPropEquals', function (object, property, value, options) {
            
            if (object[property] == value) {
                return options.fn(this);
            }

            return options.inverse(this);
        });

        Handlebars.registerHelper('ifAttrEquals', function (model, attr, value, options) {
            
            if (model.get(attr) == value) {
                return options.fn(this);
            }

            return options.inverse(this);
        });

        Handlebars.registerHelper('ifAttrNotEmpty', function (model, attr, options) {
            const value = model.get(attr);

            if (value !== null && typeof value !== 'undefined') {
                return options.fn(this);
            }

            return options.inverse(this);
        });

        Handlebars.registerHelper('ifNotEmptyHtml', function (value, options) {
            value = value.replace(/\s/g, '');

            if (value) {
                return options.fn(this);
            }

            return options.inverse(this);
        });

        Handlebars.registerHelper('get', (model, name) => model.get(name));

        Handlebars.registerHelper('length', arr => arr.length);

        Handlebars.registerHelper('translate', (name, options) => {
            const scope = options.hash.scope || null;
            const category = options.hash.category || null;

            if (name === 'null') {
                return '';
            }

            return this.language.translate(name, category, scope);
        });

        Handlebars.registerHelper('dropdownItem', (name, options) => {
            const scope = options.hash.scope || null;
            const label = options.hash.label;
            const labelTranslation = options.hash.labelTranslation;
            const data = options.hash.data;
            const hidden = options.hash.hidden;
            const disabled = options.hash.disabled;
            const title = options.hash.title;
            const link = options.hash.link;
            const action = options.hash.action || name;
            const iconHtml = options.hash.iconHtml;
            const iconClass = options.hash.iconClass;

            let html =
                options.hash.html ||
                options.hash.text ||
                (
                    labelTranslation ?
                        this.language.translatePath(labelTranslation) :
                        this.language.translate(label, 'labels', scope)
                );

            if (!options.hash.html) {
                html = this.escapeString(html);
            }

            if (iconHtml) {
                html = iconHtml + ' ' + html;
            }
            else if (iconClass) {
                const iconHtml = $('<span>').addClass(iconClass).get(0).outerHTML;

                html = iconHtml + ' ' + html;
            }

            const $li = $('<li>')
                .addClass(hidden ? 'hidden' : '')
                .addClass(disabled ? 'disabled' : '');

            const $a = $('<a>')
                .attr('role', 'button')
                .attr('tabindex', '0')
                .attr('data-name', name)
                .addClass(options.hash.className || '')
                .addClass('action')
                .html(html);

            if (action) {
                $a.attr('data-action', action);
            }

            $li.append($a);

            link ?
                $a.attr('href', link) :
                $a.attr('role', 'button');

            if (data) {
                for (const key in data) {
                    $a.attr('data-' + Espo.Utils.camelCaseToHyphen(key), data[key]);
                }
            }

            if (disabled) {
                $li.attr('disabled', 'disabled');
            }

            if (title) {
                $a.attr('title', title);
            }

            return new Handlebars.SafeString($li.get(0).outerHTML);
        });

        Handlebars.registerHelper('button', (name, options) => {
            const style = options.hash.style || 'default';
            const scope = options.hash.scope || null;
            const label = options.hash.label || name;
            const labelTranslation = options.hash.labelTranslation;
            const link = options.hash.link;
            const iconHtml = options.hash.iconHtml;
            const iconClass = options.hash.iconClass;

            let html =
                options.hash.html ||
                options.hash.text ||
                (
                    labelTranslation ?
                        this.language.translatePath(labelTranslation) :
                        this.language.translate(label, 'labels', scope)
                );

            if (!options.hash.html) {
                html = this.escapeString(html);
            }

            if (iconHtml) {
                html = iconHtml + ' ' + '<span>' + html + '</span>';
            }
            else if (iconClass) {
                const iconHtml = $('<span>').addClass(iconClass).get(0).outerHTML;

                html = iconHtml + ' ' + '<span>' + html + '</span>';
            }

            const tag = link ? '<a>' : '<button>';

            const $button = $(tag)
                .addClass('btn action')
                .addClass(options.hash.className || '')
                .addClass(options.hash.hidden ? 'hidden' : '')
                .addClass(options.hash.disabled ? 'disabled' : '')
                .attr('data-action', name)
                .attr('data-name', name)
                .addClass('btn-' + style)
                .html(html);

            link ?
                $button.href(link) :
                $button.attr('type', 'button')

            if (options.hash.disabled) {
                $button.attr('disabled', 'disabled');
            }

            if (options.hash.title) {
                $button.attr('title', options.hash.title);
            }

            return new Handlebars.SafeString($button.get(0).outerHTML);
        });

        Handlebars.registerHelper('hyphen', (string) => {
            return Espo.Utils.convert(string, 'c-h');
        });

        Handlebars.registerHelper('toDom', (string) => {
            return Espo.Utils.toDom(string);
        });

        
        Handlebars.registerHelper('breaklines', (text) => {
            text = Handlebars.Utils.escapeExpression(text || '');
            text = text.replace(/(\r\n|\n|\r)/gm, '<br>');

            return new Handlebars.SafeString(text);
        });

        Handlebars.registerHelper('complexText', (text, options) => {
            return this.transformMarkdownText(text, options.hash);
        });

        Handlebars.registerHelper('translateOption', (name, options) => {
            const scope = options.hash.scope || null;
            const field = options.hash.field || null;

            if (!field) {
                return '';
            }

            let translationHash = options.hash.translatedOptions || null;

            if (translationHash === null) {
                translationHash = this.language.translate(field, 'options', scope) || {};

                if (typeof translationHash !== 'object') {
                    translationHash = {};
                }
            }

            if (name === null) {
                name = '';
            }

            return translationHash[name] || name;
        });

        Handlebars.registerHelper('options', (list, value, options) => {
            if (typeof value === 'undefined') {
                value = false;
            }

            list = list || [];

            let html = '';

            const multiple = (Object.prototype.toString.call(value) === '[object Array]');

            const checkOption = name => {
                if (multiple) {
                    return value.indexOf(name) !== -1;
                }

                return value === name || !value && !name;
            };

            options.hash =  options.hash || {};

            const scope = options.hash.scope || false;
            const category = options.hash.category || false;
            const field = options.hash.field || false;
            const styleMap = options.hash.styleMap || {};

            if (!multiple && options.hash.includeMissingOption && (value || value === '')) {
                if (!~list.indexOf(value)) {
                    list = Espo.Utils.clone(list);

                    list.push(value);
                }
            }

            let translationHash = options.hash.translationHash ||
                options.hash.translatedOptions ||
                null;

            if (translationHash === null) {
                if (!category && field) {
                    translationHash = this.language
                        .translate(field, 'options', scope) || {};

                    if (typeof translationHash !== 'object') {
                        translationHash = {};
                    }
                }
                else {
                    translationHash = {};
                }
            }

            const translate = name => {
                if (!category) {
                    return translationHash[name] || name;
                }

                return this.language.translate(name, category, scope);
            };

            for (const key in list) {
                const value = list[key];
                const label = translate(value);

                const $option =
                    $('<option>')
                        .attr('value', value)
                        .addClass(styleMap[value] ? 'text-' + styleMap[value] : '')
                        .text(label);

                if (checkOption(list[key])) {
                    $option.attr('selected', 'selected')
                }

                html += $option.get(0).outerHTML;
            }

            return new Handlebars.SafeString(html);
        });

        Handlebars.registerHelper('basePath', () => {
            return this.basePath || '';
        });
    }

    
    getAppParam(name) {
        return (this.appParams || {})[name];
    }

    
    escapeString(text) {
        return Handlebars.Utils.escapeExpression(text);
    }

    
    getAvatarHtml(id, size, width, additionalClassName) {
        if (this.config.get('avatarsDisabled')) {
            return '';
        }

        const t = this.cache ? this.cache.get('app', 'timestamp') : Date.now();

        const basePath = this.basePath || '';
        size = size || 'small';
        width = width || 16;

        let className = 'avatar';

        if (additionalClassName) {
            className += ' ' + additionalClassName;
        }

        
        return $(`<img>`)
            .attr('src', `${basePath}?entryPoint=avatar&size=${size}&id=${id}&t=${t}`)
            .attr('alt', 'avatar')
            .addClass(className)
            .attr('width', width.toString())
            .get(0).outerHTML;
    }

    
    transformMarkdownInlineText(text) {
        return this.transformMarkdownText(text, {inline: true});
    }

    
    transformMarkdownText(text, options) {
        text = text || '';

        
        text = text.replace(/\</g, '\\<');

        this.mdBeforeList.forEach(item => {
            text = text.replace(item.regex, item.value);
        });

        options = options || {};

        text = options.inline ?
            marked.parseInline(text) :
            marked.parse(text);

        text = DOMPurify.sanitize(text, {}).toString();

        if (options.linksInNewTab) {
            text = text.replace(/<a href=/gm, '<a target="_blank" rel="noopener noreferrer" href=');
        }

        text = text.replace(
            /<a href="mailto:(.*)"/gm,
            '<a role="button" class="selectable" data-email-address="$1" data-action="mailTo"'
        );

        return new Handlebars.SafeString(text);
    }

    
    getScopeColorIconHtml(scope, noWhiteSpace, additionalClassName) {
        if (this.config.get('scopeColorsDisabled') || this.preferences.get('scopeColorsDisabled')) {
            return '';
        }

        const color = this.metadata.get(['clientDefs', scope, 'color']);

        let html = '';

        if (color) {
            const $span = $('<span class="color-icon fas fa-square">');

            $span.css('color', color);

            if (additionalClassName) {
                $span.addClass(additionalClassName);
            }

            html = $span.get(0).outerHTML;
        }

        if (!noWhiteSpace) {
            if (html) {
                html += `<span style="user-select: none;">&nbsp;</span>`;
            }
        }

        return html;
    }

    
    sanitizeHtml(text, options) {
        return DOMPurify.sanitize(text, options);
    }

    
    moderateSanitizeHtml(value) {
        value = value || '';
        value = value.replace(/<\/?(base)[^><]*>/gi, '');
        value = value.replace(/<\/?(object)[^><]*>/gi, '');
        value = value.replace(/<\/?(embed)[^><]*>/gi, '');
        value = value.replace(/<\/?(applet)[^><]*>/gi, '');
        value = value.replace(/<\/?(iframe)[^><]*>/gi, '');
        value = value.replace(/<\/?(script)[^><]*>/gi, '');
        value = value.replace(/<[^><]*([^a-z]on[a-z]+)=[^><]*>/gi, function (match) {
            return match.replace(/[^a-z]on[a-z]+=/gi, ' data-handler-stripped=');
        });

        value = this.stripEventHandlersInHtml(value);

        value = value.replace(/href=" *javascript:(.*?)"/gi, () => {
            return 'removed=""';
        });

        value = value.replace(/href=' *javascript:(.*?)'/gi, () => {
            return 'removed=""';
        });

        value = value.replace(/src=" *javascript:(.*?)"/gi, () => {
            return 'removed=""';
        });

        value = value.replace(/src=' *javascript:(.*?)'/gi, () => {
            return 'removed=""';
        });

        return value;
    }

    
    stripEventHandlersInHtml(html) {
        let j; 

        function stripHTML() {
            html = html.slice(0, strip) + html.slice(j);
            j = strip;

            strip = false;
        }

        function isValidTagChar(str) {
            return str.match(/[a-z?\\\/!]/i);
        }

        let strip = false;
        let lastQuote = false;

        for (let i = 0; i < html.length; i++){
            if (html[i] === '<' && html[i + 1] && isValidTagChar(html[i + 1])) {
                i++;

                for (let j = i; j < html.length; j++){
                    if (!lastQuote && html[j] === '>'){
                        if (strip) {
                            stripHTML();
                        }

                        i = j;

                        break;
                    }

                    
                    if (lastQuote === html[j]){
                        lastQuote = false;

                        continue;
                    }

                    if (!lastQuote && html[j - 1] === "=" && (html[j] === "'" || html[j] === '"')) {
                        lastQuote = html[j];
                    }

                    if (!lastQuote && html[j - 2] === " " && html[j - 1] === "o" && html[j] === "n") {
                        strip = j - 2;
                    }

                    if (strip && html[j] === " " && !lastQuote){
                        stripHTML();
                    }
                }
            }
        }

        return html;
    }

    
    calculateContentContainerHeight($el) {
        const smallScreenWidth = this.themeManager.getParam('screenWidthXs');

        const $window = $(window);

        const footerHeight = $('#footer').height() || 26;
        let top = 0;
        const element = $el.get(0);

        if (element) {
            top = element.getBoundingClientRect().top;

            if ($window.width() < smallScreenWidth) {
                const $navbarCollapse = $('#navbar .navbar-body');

                if ($navbarCollapse.hasClass('in') || $navbarCollapse.hasClass('collapsing')) {
                    top -= $navbarCollapse.height();
                }
            }
        }

        const spaceHeight = top + footerHeight;

        return $window.height() - spaceHeight - 20;
    }

    
    processSetupHandlers(view, type, scope) {
        
        scope = scope || view.scope || view.entityType;

        let handlerIdList = this.metadata.get(['clientDefs', 'Global', 'viewSetupHandlers', type]) || [];

        if (scope) {
            handlerIdList = handlerIdList
                .concat(
                    this.metadata.get(['clientDefs', scope, 'viewSetupHandlers', type]) || []
                );
        }

        if (handlerIdList.length === 0) {
            return Promise.resolve();
        }

        

        
        const promiseList = [];

        for (const id of handlerIdList) {
            const promise = new Promise(resolve => {
                Espo.loader.require(id, Handler => {
                    const result = (new Handler(view)).process(view);

                    if (result && Object.prototype.toString.call(result) === '[object Promise]') {
                        result.then(() => resolve());

                        return;
                    }

                    resolve();
                });
            });

            promiseList.push(promise);
        }

        return Promise.all(promiseList);
    }

    
    _isXsScreen

    
    isXsScreen() {
        if (this._isXsScreen == null) {
            this._isXsScreen = window.innerWidth < this.themeManager.getParam('screenWidthXs');
        }

        return this._isXsScreen;
    }
}

export default ViewHelper;
