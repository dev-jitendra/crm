



import {marked} from 'marked';
import DOMPurify from 'dompurify';
import $ from 'jquery';






class Dialog {

    height
    fitHeight
    onRemove
    onClose
    onBackdropClick
    buttons
    screenWidthXs

    
    constructor(options) {
        options = options || {};

        
        this.className = 'dialog';
        
        this.backdrop = 'static';
        
        this.closeButton = true;
        
        this.collapseButton = false;
        
        this.header = null;
        
        this.body = '';
        
        this.width = null;
        
        this.buttonList = [];
        
        this.dropdownItemList = [];
        
        this.removeOnClose = true;
        
        this.draggable = false;
        
        this.container = 'body';
        
        this.options = options;
        
        this.keyboard = true;

        this.activeElement = document.activeElement;

        const params = [
            'className',
            'backdrop',
            'keyboard',
            'closeButton',
            'collapseButton',
            'header',
            'body',
            'width',
            'height',
            'fitHeight',
            'buttons',
            'buttonList',
            'dropdownItemList',
            'removeOnClose',
            'draggable',
            'container',
            'onRemove',
            'onClose',
            'onBackdropClick',
        ];

        params.forEach(param => {
            if (param in options) {
                this[param] = options[param];
            }
        });

        
        this.onCloseIsCalled = false;

        if (this.buttons && this.buttons.length) {
            
            this.buttonList = this.buttons;
        }

        this.id = 'dialog-' + Math.floor((Math.random() * 100000));

        if (typeof this.backdrop === 'undefined') {
            
            this.backdrop = 'static';
        }

        const $header = this.getHeader();
        const $footer = this.getFooter();

        const $body = $('<div>')
            .addClass('modal-body body')
            .html(this.body);

        const $content = $('<div>').addClass('modal-content');

        if ($header) {
            $content.append($header);
        }

        if ($footer && this.options.footerAtTheTop) {
            $content.append($footer);
        }

        $content.append($body);

        if ($footer && !this.options.footerAtTheTop) {
            $content.append($footer);
        }

        const $dialog = $('<div>')
            .addClass('modal-dialog')
            .append($content);

        const $container = $(this.container);

        $('<div>')
            .attr('id', this.id)
            .attr('class', this.className + ' modal')
            .attr('role', 'dialog')
            .attr('tabindex', '-1')
            .append($dialog)
            .appendTo($container);

        
        this.$el = $('#' + this.id);

        
        this.el = this.$el.get(0);

        this.$el.find('header a.close').on('click', () => {
            
        });

        this.initButtonEvents();

        if (this.draggable) {
            this.$el.find('header').css('cursor', 'pointer');

            
            this.$el.draggable({
                handle: 'header',
            });
        }

        const modalContentEl = this.$el.find('.modal-content');

        if (this.width) {
            modalContentEl.css('width', this.width);
            modalContentEl.css('margin-left', '-' + (parseInt(this.width.replace('px', '')) / 5) + 'px');
        }

        if (this.removeOnClose) {
            this.$el.on('hidden.bs.modal', e => {
                if (this.$el.get(0) === e.target) {
                    if (!this.onCloseIsCalled) {
                        this.close();
                    }

                    if (this.skipRemove) {
                        return;
                    }

                    this.remove();
                }
            });
        }

        const $window = $(window);

        this.$el.on('shown.bs.modal', () => {
            $('.modal-backdrop').not('.stacked').addClass('stacked');

            const headerHeight = this.$el.find('.modal-header').outerHeight() || 0;
            const footerHeight = this.$el.find('.modal-footer').outerHeight() || 0;

            let diffHeight = headerHeight + footerHeight;

            if (!options.fullHeight) {
                diffHeight = diffHeight + options.bodyDiffHeight;
            }

            if (this.fitHeight || options.fullHeight) {
                const processResize = () => {
                    const windowHeight = window.innerHeight;
                    const windowWidth = $window.width();

                    if (!options.fullHeight && windowHeight < 512) {
                        this.$el.find('div.modal-body').css({
                            maxHeight: 'none',
                            overflow: 'auto',
                            height: 'none',
                        });

                        return;
                    }

                    const cssParams = {
                        overflow: 'auto',
                    };

                    if (options.fullHeight) {
                        cssParams.height = (windowHeight - diffHeight) + 'px';

                        this.$el.css('paddingRight', 0);
                    }
                    else {
                        if (windowWidth <= options.screenWidthXs) {
                            cssParams.maxHeight = 'none';
                        } else {
                            cssParams.maxHeight = (windowHeight - diffHeight) + 'px';
                        }
                    }

                    this.$el.find('div.modal-body').css(cssParams);
                };

                $window.off('resize.modal-height');
                $window.on('resize.modal-height', processResize);

                processResize();
            }
        });

        const $documentBody = $(document.body);

        this.$el.on('hidden.bs.modal', () => {
            if ($('.modal:visible').length > 0) {
                $documentBody.addClass('modal-open');
            }
        });
    }

    
    callOnClose() {
        if (this.onClose) {
            this.onClose()
        }
    }

    
    callOnBackdropClick() {
        if (this.onBackdropClick) {
            this.onBackdropClick()
        }
    }

    
    callOnRemove() {
        if (this.onRemove) {
            this.onRemove()
        }
    }

    
    setActionItems(buttonList, dropdownItemList) {
        this.buttonList = buttonList;
        this.dropdownItemList = dropdownItemList;
    }

    
    initButtonEvents() {
        this.buttonList.forEach(o => {
            if (typeof o.onClick === 'function') {
                const $button = $('#' + this.id + ' .modal-footer button[data-name="' + o.name + '"]');

                $button.on('click', e => o.onClick(this, e));
            }
        });

        this.dropdownItemList.forEach(o => {
            if (typeof o.onClick === 'function') {
                const $button = $('#' + this.id + ' .modal-footer a[data-name="' + o.name + '"]');

                $button.on('click', e => o.onClick(this, e));
            }
        });
    }

    
    getHeader() {
        if (!this.header) {
            return null;
        }

        const $header = $('<header />')
            .addClass('modal-header')
            .addClass(this.options.fixedHeaderHeight ? 'fixed-height' : '')
            .append(
                $('<h4 />')
                    .addClass('modal-title')
                    .append(
                        $('<span />')
                            .addClass('modal-title-text')
                            .html(this.header)
                    )
            );


        if (this.collapseButton) {
            $header.prepend(
                $('<a>')
                    .addClass('collapse-button')
                    .attr('role', 'button')
                    .attr('tabindex', '-1')
                    .attr('data-action', 'collapseModal')
                    .append(
                        $('<span />')
                            .addClass('fas fa-minus')
                    )
            );
        }

        if (this.closeButton) {
            $header.prepend(
                $('<a>')
                    .addClass('close')
                    .attr('data-dismiss', 'modal')
                    .attr('role', 'button')
                    .attr('tabindex', '-1')
                    .append(
                        $('<span />')
                            .attr('aria-hidden', 'true')
                            .html('&times;')
                    )
            );
        }

        return $header;
    }

    
    getFooter() {
        if (!this.buttonList.length && !this.dropdownItemList.length) {
            return null;
        }

        const $footer = $('<footer>').addClass('modal-footer');

        const $main = $('<div>')
            .addClass('btn-group')
            .addClass('main-btn-group');

        const $additional = $('<div>')
            .addClass('btn-group')
            .addClass('additional-btn-group');

        this.buttonList.forEach(o => {
            const style = o.style || 'default';

            const $button =
                $('<button>')
                    .attr('type', 'button')
                    .attr('data-name', o.name)
                    .addClass('btn')
                    .addClass('btn-' + style)
                    .addClass(o.className || 'btn-xs-wide');

            if (o.disabled) {
                $button.attr('disabled', 'disabled');
                $button.addClass('disabled');
            }

            if (o.hidden) {
                $button.addClass('hidden');
            }

            if (o.title) {
                $button.attr('title', o.title);
            }

            if (o.text) {
                $button.text(o.text);
            }

            if (o.html) {
                $button.html(o.html);
            }

            if (o.pullLeft || o.position === 'right') {
                $additional.append($button);

                return;
            }

            $main.append($button);
        });

        const allDdItemsHidden = this.dropdownItemList.filter(o => !o.hidden).length === 0;

        const $dropdown = $('<div>')
            .addClass('btn-group')
            .addClass(allDdItemsHidden ? 'hidden' : '')
            .append(
                $('<button>')
                    .attr('type', 'button')
                    .addClass('btn btn-default dropdown-toggle')
                    .addClass(allDdItemsHidden ? 'hidden' : '')
                    .attr('data-toggle', 'dropdown')
                    .append(
                        $('<span>').addClass('fas fa-ellipsis-h')
                    )
            );

        const $ul = $('<ul>').addClass('dropdown-menu pull-right');

        $dropdown.append($ul);

        this.dropdownItemList.forEach(o => {
            const $a = $('<a>')
                .attr('role', 'button')
                .attr('tabindex', '0')
                .attr('data-name', o.name);

            if (o.text) {
                $a.text(o.text);
            }

            if (o.title) {
                $a.attr('title', o.title);
            }

            if (o.html) {
                $a.html(o.html);
            }

            const $li = $('<li>')
                .addClass(o.hidden ? ' hidden' : '')
                .append($a);

            $ul.append($li);
        });

        if ($ul.children().length) {
            $main.append($dropdown);
        }

        if ($additional.children().length) {
            $footer.append($additional);
        }

        $footer.append($main);

        return $footer;
    }

    
    show() {
        
        this.$el.modal({
             backdrop: this.backdrop,
             keyboard: this.keyboard,
        });

        this.$el.find('.modal-content').removeClass('hidden');

        const $modalBackdrop = $('.modal-backdrop');

        $modalBackdrop.each((i, el) => {
            if (i < $modalBackdrop.length - 1) {
                $(el).addClass('hidden');
            }
        });

        const $modalContainer = $('.modal-container');

        $modalContainer.each((i, el) => {
            if (i < $modalContainer.length - 1) {
                $(el).addClass('overlaid');
            }
        });

        this.$el.off('click.dismiss.bs.modal');

        this.$el.on(
            'click.dismiss.bs.modal',
            '> div.modal-dialog > div.modal-content > header [data-dismiss="modal"]',
            () => this.close()
        );

        this.$el.on('mousedown', e => {
            this.$mouseDownTarget = $(e.target);
        });

        this.$el.on('click.dismiss.bs.modal', (e) => {
            if (e.target !== e.currentTarget) {
                return;
            }

            if (
                this.$mouseDownTarget &&
                this.$mouseDownTarget.closest('.modal-content').length
            ) {
                return;
            }

            this.callOnBackdropClick();

            if (this.backdrop === 'static') {
                return;
            }

            this.close();
        });

        $('body > .popover').addClass('hidden');
    }

    
    hide() {
        this.$el.find('.modal-content').addClass('hidden');
    }

    
    hideWithBackdrop() {
        const $modalBackdrop = $('.modal-backdrop');

        $modalBackdrop.last().addClass('hidden');
        $($modalBackdrop.get($modalBackdrop.length - 2)).removeClass('hidden');

        const $modalContainer = $('.modal-container');

        $($modalContainer.get($modalContainer.length - 2)).removeClass('overlaid');

        this.skipRemove = true;

        setTimeout(() => {
            this.skipRemove = false;
        }, 50);

        
        this.$el.modal('hide');
        this.$el.find('.modal-content').addClass('hidden');
    }

    
    _close() {
        const $modalBackdrop = $('.modal-backdrop');

        $modalBackdrop.last().removeClass('hidden');

        const $modalContainer = $('.modal-container');

        $($modalContainer.get($modalContainer.length - 2)).removeClass('overlaid');
    }

    
    _findClosestFocusableElement(element) {
        
        const isVisible = !!(
            element.offsetWidth ||
            element.offsetHeight ||
            element.getClientRects().length
        );

        if (isVisible) {
            
            element.focus({preventScroll: true});

            return element;
        }

        const $element = $(element);

        if ($element.closest('.dropdown-menu').length) {
            const $button = $element.closest('.btn-group').find(`[data-toggle="dropdown"]`);

            if ($button.length) {
                
                $button.get(0).focus({preventScroll: true});

                return $button.get(0);
            }
        }

        return null;
    }

    
    close() {
        if (!this.onCloseIsCalled) {
            this.callOnClose();
            this.onCloseIsCalled = true;

            if (this.activeElement) {
                setTimeout(() => {
                    const element = this._findClosestFocusableElement(this.activeElement);

                    if (element) {
                        
                        element.focus({preventScroll: true});
                    }
                }, 50);
            }
        }

        this._close();
        
        this.$el.modal('hide');
        $(this).trigger('dialog:close');
    }

    
    remove() {
        this.callOnRemove();

        
        
        this._close();
        this.$el.remove();

        $(this).off();
        $(window).off('resize.modal-height');
    }
}



Espo.Ui = {

    Dialog: Dialog,

    

    
    confirm: function (message, o, callback, context) {
        o = o || {};

        const confirmText = o.confirmText;
        const cancelText = o.cancelText;
        const confirmStyle = o.confirmStyle || 'danger';
        let backdrop = o.backdrop;

        if (typeof backdrop === 'undefined') {
            backdrop = false;
        }

        let isResolved = false;

        const processCancel = () => {
            if (!o.cancelCallback) {
                return;
            }

            if (context) {
                o.cancelCallback.call(context);

                return;
            }

            o.cancelCallback();
        };

        if (!o.isHtml) {
            message = Handlebars.Utils.escapeExpression(message);
        }

        return new Promise(resolve => {
            const dialog = new Dialog({
                backdrop: backdrop,
                header: null,
                className: 'dialog-confirm',
                body: '<span class="confirm-message">' + message + '</a>',
                buttonList: [
                    {
                        text: ' ' + confirmText + ' ',
                        name: 'confirm',
                        className: 'btn-s-wide',
                        onClick: () => {
                            isResolved = true;

                            if (callback) {
                                if (context) {
                                    callback.call(context);
                                } else {
                                    callback();
                                }
                            }

                            resolve();

                            dialog.close();
                        },
                        style: confirmStyle,
                        position: 'right',
                    },
                    {
                        text: cancelText,
                        name: 'cancel',
                        className: 'btn-s-wide',
                        onClick: () => {
                            isResolved = true;

                            dialog.close();
                            processCancel();
                        },
                        position: 'left',
                    }
                ],
                onClose: () => {
                    if (isResolved) {
                        return;
                    }

                    processCancel();
                },
            });

            dialog.show();
            dialog.$el.find('button[data-name="confirm"]').focus();
        });
    },

    
    dialog: function (options) {
        return new Dialog(options);
    },


    

    
    popover: function (element, o, view) {
        const $el = $(element);
        const $body = $('body');
        const content = o.content || Handlebars.Utils.escapeExpression(o.text || '');
        let isShown = false;

        let container = o.container;

        if (!container) {
            const $modalBody = $el.closest('.modal-body');

            container = $modalBody.length ? $modalBody : 'body';
        }

        
        $el
            .popover({
                placement: o.placement || 'bottom',
                container: container,
                viewport: container,
                html: true,
                content: content,
                trigger: o.trigger || 'manual',
            })
            .on('shown.bs.popover', () => {
                isShown = true;

                if (!view) {
                    return;
                }

                if (view && !o.noHideOnOutsideClick) {
                    $body.off('click.popover-' + view.cid);

                    $body.on('click.popover-' + view.cid, e => {
                        if ($(e.target).closest('.popover-content').get(0)) {
                            return;
                        }

                        if ($.contains($el.get(0), e.target)) {
                            return;
                        }

                        if ($el.get(0) === e.target) {
                            return;
                        }

                        $body.off('click.popover-' + view.cid);
                        
                        $el.popover('hide');
                    });
                }

                if (o.onShow) {
                    o.onShow();
                }
            })
            .on('hidden.bs.popover', () => {
                isShown = false;

                if (o.onHide) {
                    o.onHide();
                }
            });

        if (!o.noToggleInit) {
            $el.on('click', () => {
                
                $el.popover('toggle');
            });
        }

        let isDetached = false;

        const detach = () => {
            if (view) {
                $body.off('click.popover-' + view.cid);

                view.off('remove', destroy);
                view.off('render', destroy);
                view.off('render', hide);
            }

            isDetached = true;
        };

        const destroy = () => {
            if (isDetached) {
                return;
            }

            
            $el.popover('destroy');

            detach();
        };

        const hide = () => {
            if (!isShown) {
                return;
            }

            
            $el.popover('hide');
        };

        const show = () => {
            
            $el.popover('show');
        };

        if (view) {
            view.once('remove', destroy);

            if (!o.preventDestroyOnRender) {
                view.once('render', destroy);
            }

            if (o.preventDestroyOnRender) {
                view.on('render', hide);
            }
        }

        return {
            hide: () => hide(),
            destroy: () => destroy(),
            show: () => show(),
            detach: () => detach(),
        };
    },

    

    
    notify: function (message, type, timeout, options) {
        type = type || 'warning';
        options = {...options};

        if (type === 'warning' && notifySuppressed) {
            return;
        }

        $('#notification').remove();

        if (!message) {
            return;
        }

        if (options.suppress && timeout) {
            notifySuppressed = true;

            setTimeout(() => notifySuppressed = false, timeout)
        }

        const parsedMessage = message.indexOf('\n') !== -1 ?
            marked.parse(message) :
            marked.parseInline(message);

        let sanitizedMessage = DOMPurify.sanitize(parsedMessage, {}).toString();

        const closeButton = options.closeButton || false;

        if (type === 'error') {
            
            type = 'danger';
        }

        if (sanitizedMessage === ' ... ') {
            sanitizedMessage = ' <span class="fas fa-spinner fa-spin"> ';
        }

        const additionalClassName = closeButton ? ' alert-closable' : '';

        const $el = $('<div>')
            .addClass('alert alert-' + type + additionalClassName + ' fade in')
            .attr('id', 'notification')
            .css({
                'position': 'fixed',
                'top': '0',
                'left': '50vw',
                'transform': 'translate(-50%, 0)',
                'z-index': 2000,
            })
            .append(
                $('<div>')
                    .addClass('message')
                    .html(sanitizedMessage)
            );

        if (closeButton) {
            const $close = $('<button>')
                .attr('type', 'button')
                .attr('data-dismiss', 'modal')
                .attr('aria-hidden', 'true')
                .addClass('close')
                .html('&times;');

            $el.append(
                $('<div>')
                    .addClass('close-container')
                    .append($close)
            );

            $close.on('click', () => $el.alert('close'));
        }

        if (timeout) {
            setTimeout(() => $el.alert('close'), timeout);
        }

        $el.appendTo('body')
    },

    
    warning: function (message, options) {
        Espo.Ui.notify(message, 'warning', 2000, options);
    },

    
    success: function (message, options) {
        Espo.Ui.notify(message, 'success', 2000, options);
    },

    
    error: function (message, options) {
        options = typeof options === 'boolean' ?
            {closeButton: options} :
            {...options};

        const timeout = options.closeButton ? 0 : 4000;

        Espo.Ui.notify(message, 'danger', timeout, options);
    },

    
    info: function (message, options) {
        Espo.Ui.notify(message, 'info', 2000, options);
    },
};

let notifySuppressed = false;


Espo.ui = Espo.Ui;

export default Espo.Ui;
