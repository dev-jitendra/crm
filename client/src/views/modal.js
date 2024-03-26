



import View from 'view';


class ModalView extends View {

    

    

    
    constructor(options) {
        super(options);
    }

    
    cssName = 'modal-dialog'

    
    className = 'dialog'

    
    header

    
    headerHtml

    
    $header

    
    headerElement

    
    headerText

    
    dialog

    
    containerSelector = ''

    
    scope = null

    
    backdrop = 'static'

    
    buttonList = []

    
    dropdownItemList = []

    
    buttons = []

    
    width = null

    
    fitHeight = false

    
    noFullHeight = false

    
    escapeDisabled = false

    
    isDraggable = false

    
    isCollapsable = false

    
    isCollapsed = false

    
    events = {
        
        'click .action': function (e) {
            Espo.Utils.handleAction(this, e.originalEvent, e.currentTarget);
        },
        
        'click [data-action="collapseModal"]': function () {
            this.collapse();
        },
    }

    
    footerAtTheTop = null

    
    shortcutKeys = null

    
    init() {
        const id = this.cssName + '-container-' + Math.floor((Math.random() * 10000) + 1).toString();

        this.containerSelector = '#' + id;

        this.header = this.options.header || this.header;
        this.headerHtml = this.options.headerHtml || this.headerHtml;
        this.$header = this.options.$header || this.$header;
        this.headerElement = this.options.headerElement || this.headerElement;
        this.headerText = this.options.headerText || this.headerText;

        this.backdrop = this.options.backdrop || this.backdrop;

        this.setSelector(this.containerSelector);

        this.buttonList = this.options.buttonList || this.buttonList;
        this.dropdownItemList = this.options.dropdownItemList || this.dropdownItemList;

        this.buttonList = Espo.Utils.cloneDeep(this.buttonList);
        this.dropdownItemList = Espo.Utils.cloneDeep(this.dropdownItemList);

        
        this.buttons = Espo.Utils.cloneDeep(this.buttons);

        if (this.shortcutKeys) {
            this.shortcutKeys = Espo.Utils.cloneDeep(this.shortcutKeys);
        }

        this.on('render', () => {
            if (this.dialog) {
                this.dialog.close();
            }

            this.isCollapsed = false;

            $(this.containerSelector).remove();

            $('<div />').css('display', 'none')
                .attr('id', id)
                .addClass('modal-container')
                .appendTo('body');

            let modalBodyDiffHeight = 92;

            if (this.getThemeManager().getParam('modalBodyDiffHeight') !== null) {
                modalBodyDiffHeight = this.getThemeManager().getParam('modalBodyDiffHeight');
            }

            let headerHtml = this.headerHtml || this.header;

            if (this.$header && this.$header.length) {
                headerHtml = this.$header.get(0).outerHTML;
            }

            if (this.headerElement) {
                headerHtml = this.headerElement.outerHTML;
            }

            if (this.headerText) {
                headerHtml = Handlebars.Utils.escapeExpression(this.headerText);
            }

            const footerAtTheTop = (this.footerAtTheTop !== null) ? this.footerAtTheTop :
                this.getThemeManager().getParam('modalFooterAtTheTop');

            this.dialog = new Espo.Ui.Dialog({
                backdrop: this.backdrop,
                header: headerHtml,
                container: this.containerSelector,
                body: '',
                buttonList: this.getDialogButtonList(),
                dropdownItemList: this.getDialogDropdownItemList(),
                width: this.width,
                keyboard: !this.escapeDisabled,
                fitHeight: this.fitHeight,
                draggable: this.isDraggable,
                className: this.className,
                bodyDiffHeight: modalBodyDiffHeight,
                footerAtTheTop: footerAtTheTop,
                fullHeight: !this.noFullHeight && this.getThemeManager().getParam('modalFullHeight'),
                screenWidthXs: this.getThemeManager().getParam('screenWidthXs'),
                fixedHeaderHeight: this.fixedHeaderHeight,
                closeButton: !this.noCloseButton,
                collapseButton: this.isCollapsable,
                onRemove: () => this.onDialogClose(),
                onBackdropClick: () => this.onBackdropClick(),
            });

            this.setElement(this.containerSelector + ' .body');
        });

        this.on('after:render', () => {
            $(this.containerSelector).show();

            this.dialog.show();

            if (this.fixedHeaderHeight && this.flexibleHeaderFontSize) {
                this.adjustHeaderFontSize();
            }

            this.adjustButtons();

            if (!this.noFullHeight) {
                this.initBodyScrollListener();
            }
        });

        this.once('remove', () => {
            if (this.dialog) {
                this.dialog.close();
            }

            $(this.containerSelector).remove();
        });
    }

    setupFinal() {
        if (this.shortcutKeys) {
            this.events['keydown.modal-base'] = e => {
                const key = Espo.Utils.getKeyFromKeyEvent(e);

                if (typeof this.shortcutKeys[key] === 'function') {
                    this.shortcutKeys[key].call(this, e.originalEvent);

                    return;
                }

                const actionName = this.shortcutKeys[key];

                if (!actionName) {
                    return;
                }

                if (this.hasActionItem(actionName) && !this.hasAvailableActionItem(actionName)) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const methodName = 'action' + Espo.Utils.upperCaseFirst(actionName);

                if (typeof this[methodName] === 'function') {
                    this[methodName]();

                    return;
                }

                this[actionName]();
            };
        }
    }

    
    getDialogButtonList() {
        const buttonListExt = [];

        
        this.buttons.forEach(item => {
            const o = Espo.Utils.clone(item);

            if (!('text' in o) && ('label' in o)) {
                o.text = this.getLanguage().translate(o.label);
            }

            buttonListExt.push(o);
        });

        this.buttonList.forEach(item => {
            let o = {};

            if (typeof item === 'string') {
                o.name = item;
            } else if (typeof item === 'object') {
                o = item;
            } else {
                return;
            }

            if (!o.text) {
                if (o.labelTranslation) {
                    o.text = this.getLanguage().translatePath(o.labelTranslation);
                }
                else if ('label' in o) {
                    o.text = this.translate(o.label, 'labels', this.scope);
                }
                else {
                    o.text = this.translate(o.name, 'modalActions', this.scope);
                }
            }

            if (o.iconHtml && !o.html) {
                o.html = o.iconHtml + ' <span>' + this.getHelper().escapeString(o.text) + '</span>';
            }
            else if (o.iconClass && !o.html) {
                o.html = `<span class="${o.iconClass}"></span> ` +
                    '<span>' + this.getHelper().escapeString(o.text) + '</span>';
            }

            o.onClick = o.onClick || ((d, e) => {
                const handler = o.handler || (o.data || {}).handler;

                Espo.Utils.handleAction(this, e.originalEvent, e.currentTarget, {
                    action: o.name,
                    handler: handler,
                    actionFunction: o.actionFunction,
                });
            });

            buttonListExt.push(o);
        });

        return buttonListExt;
    }

    
    getDialogDropdownItemList() {
        const dropdownItemListExt = [];

        this.dropdownItemList.forEach(item => {
            let o = {};

            if (typeof item === 'string') {
                o.name = item;
            } else if (typeof item === 'object') {
                o = item;
            } else {
                return;
            }

            if (!o.text) {
                if (o.labelTranslation) {
                    o.text = this.getLanguage().translatePath(o.labelTranslation);
                }
                else if ('label' in o) {
                    o.text = this.translate(o.label, 'labels', this.scope)
                }
                else {
                    o.text = this.translate(o.name, 'modalActions', this.scope);
                }
            }

            o.onClick = o.onClick || ((d, e) => {
                
                let handler = o.handler || (o.data || {}).handler;

                Espo.Utils.handleAction(this, e.originalEvent, e.currentTarget, {
                    action: o.name,
                    handler: handler,
                    actionFunction: o.actionFunction,
                });
            });

            dropdownItemListExt.push(o);
        });

        return dropdownItemListExt;
    }

    
    updateDialog() {
        if (!this.dialog) {
            return;
        }

        this.dialog.setActionItems(
            this.getDialogButtonList(),
            this.getDialogDropdownItemList()
        );
    }

    
    onDialogClose() {
        if (!this.isBeingRendered() && !this.isCollapsed) {
            this.trigger('close');
            this.remove();
        }
    }

    
    onBackdropClick() {}

    
    actionCancel() {
        this.trigger('cancel');
        this.dialog.close();
    }

    
    actionClose() {
        this.trigger('cancel');
        this.dialog.close();
    }

    
    close() {
        this.dialog.close();
    }

    
    disableButton(name) {
        this.buttonList.forEach((d) => {
            if (d.name !== name) {
                return;
            }

            d.disabled = true;
        });

        if (!this.isRendered()) {
            return;
        }

        this.$el.find('footer button[data-name="'+name+'"]')
            .addClass('disabled')
            .attr('disabled', 'disabled');
    }

    
    enableButton(name) {
        this.buttonList.forEach((d) => {
            if (d.name !== name) {
                return;
            }

            d.disabled = false;
        });

        if (!this.isRendered()) {
            return;
        }

        this.$el.find('footer button[data-name="'+name+'"]')
            .removeClass('disabled')
            .removeAttr('disabled');
    }

    
    addButton(o, position, doNotReRender) {
        let index = -1;

        this.buttonList.forEach((item, i) => {
            if (item.name === o.name) {
                index = i;
            }
        });

        if (~index) {
            return;
        }

        if (position === true) {
            this.buttonList.unshift(o);
        }
        else if (typeof position === 'string') {
            index = -1;

            this.buttonList.forEach((item, i) => {
                if (item.name === position) {
                    index = i;
                }
            });

            if (~index) {
                this.buttonList.splice(index, 0, o);
            } else {
                this.buttonList.push(o);
            }
        }
        else {
            this.buttonList.push(o);
        }

        if (!doNotReRender && this.isRendered()) {
            this.reRenderFooter();
        }
    }

    
    addDropdownItem(o, toBeginning, doNotReRender) {
        if (!o) {
            toBeginning ?
                this.dropdownItemList.unshift(false) :
                this.dropdownItemList.push(false);

            return;
        }

        const name = o.name;

        if (!name) {
            return;
        }

        for (const item of this.dropdownItemList) {
            if (item.name === name) {
                return;
            }
        }

        toBeginning ?
            this.dropdownItemList.unshift(o) :
            this.dropdownItemList.push(o);

        if (!doNotReRender && this.isRendered()) {
            this.reRenderFooter();
        }
    }

    
    reRenderFooter() {
        if (!this.dialog) {
            return;
        }

        this.updateDialog();

        const $footer = this.dialog.getFooter();

        this.$el.find('footer.modal-footer')
            .empty()
            .append($footer);

        this.dialog.initButtonEvents();
    }

    
    removeButton(name, doNotReRender) {
        let index = -1;

        for (const [i, item] of this.buttonList.entries()) {
            if (item.name === name) {
                index = i;

                break;
            }
        }

        if (~index) {
            this.buttonList.splice(index, 1);
        }

        for (const [i, item] of this.dropdownItemList.entries()) {
            if (item.name === name) {
                this.dropdownItemList.splice(i, 1);

                break;
            }
        }

        if (this.isRendered()) {
            this.$el.find('.modal-footer [data-name="'+name+'"]').remove();
        }

        if (!doNotReRender && this.isRendered()) {
            this.reRender();
        }
    }

    
    showButton(name) {
        for (const item of this.buttonList) {
            if (item.name === name) {
                item.hidden = false;

                break;
            }
        }

        if (!this.isRendered()) {
            return;
        }

        this.$el.find('footer button[data-name="' + name + '"]').removeClass('hidden');

        this.adjustButtons();
    }

    
    hideButton(name) {
        for (const item of this.buttonList) {
            if (item.name === name) {
                item.hidden = true;

                break;
            }
        }

        if (!this.isRendered()) {
            return;
        }

        this.$el.find('footer button[data-name="'+name+'"]').addClass('hidden');

        this.adjustButtons();
    }

    
    showActionItem(name) {
        for (const item of this.buttonList) {
            if (item.name === name) {
                item.hidden = false;

                break;
            }
        }

        for (const item of this.dropdownItemList) {
            if (item.name === name) {
                item.hidden = false;

                break;
            }
        }

        if (!this.isRendered()) {
            return;
        }

        this.$el.find('footer button[data-name="'+name+'"]').removeClass('hidden');
        this.$el.find('footer li > a[data-name="'+name+'"]').parent().removeClass('hidden');

        if (!this.isDropdownItemListEmpty()) {
            const $dropdownGroup = this.$el.find('footer .main-btn-group > .btn-group');

            $dropdownGroup.removeClass('hidden');
            $dropdownGroup.find('> button').removeClass('hidden');
        }

        this.adjustButtons();
    }

    
    hideActionItem(name) {
        for (const item of this.buttonList) {
            if (item.name === name) {
                item.hidden = true;

                break;
            }
        }

        for (const item of this.dropdownItemList) {
            if (item.name === name) {
                item.hidden = true;

                break;
            }
        }

        if (!this.isRendered()) {
            return;
        }

        this.$el.find('footer button[data-name="'+name+'"]').addClass('hidden');
        this.$el.find('footer li > a[data-name="'+name+'"]').parent().addClass('hidden');

        if (this.isDropdownItemListEmpty()) {
            const $dropdownGroup = this.$el.find('footer .main-btn-group > .btn-group');

            $dropdownGroup.addClass('hidden');
            $dropdownGroup.find('> button').addClass('hidden');
        }

        this.adjustButtons();
    }

    
    hasActionItem(name) {
        const hasButton = this.buttonList
            .findIndex(item => item.name === name) !== -1;

        if (hasButton) {
            return true;
        }

        return this.dropdownItemList
            .findIndex(item => item.name === name) !== -1;
    }

    
    hasAvailableActionItem(name) {
        const hasButton = this.buttonList
            .findIndex(item => item.name === name && !item.disabled && !item.hidden) !== -1;

        if (hasButton) {
            return true;
        }

        return this.dropdownItemList
            .findIndex(item => item.name === name && !item.disabled && !item.hidden) !== -1;
    }

    
    isDropdownItemListEmpty() {
        if (this.dropdownItemList.length === 0) {
            return true;
        }

        let isEmpty = true;

        this.dropdownItemList.forEach((item) => {
            if (!item.hidden) {
                isEmpty = false;
            }
        });

        return isEmpty;
    }

    
    adjustHeaderFontSize(step) {
        step = step || 0;

        if (!step) {
            this.fontSizePercentage = 100;
        }

        const $titleText = this.$el.find('.modal-title > .modal-title-text');

        const containerWidth = $titleText.parent().width();
        let textWidth = 0;

        $titleText.children().each((i, el) => {
            textWidth += $(el).outerWidth(true);
        });

        if (containerWidth < textWidth) {
            if (step > 5) {
                const $title = this.$el.find('.modal-title');

                $title.attr('title', $titleText.text());
                $title.addClass('overlapped');

                $titleText.children().each((i, el) => {
                   $(el).removeAttr('title');
                });

                return;
            }

            this.fontSizePercentage -= 4;

            this.$el.find('.modal-title .font-size-flexible')
                .css('font-size', this.fontSizePercentage + '%');

            this.adjustHeaderFontSize(step + 1);
        }
    }

    
    collapse() {
        this.beforeCollapse().then(data => {
            if (!this.getParentView()) {
                throw new Error("Can't collapse w/o parent view.");
            }

            this.isCollapsed = true;

            data = data || {};

            let title;

            if (data.title) {
                title = data.title;
            }
            else {
                const $title = this.$el.find('.modal-header .modal-title .modal-title-text');

                title = $title.text();
            }

            this.dialog.close();

            let masterView = this;

            while (masterView.getParentView()) {
                masterView = masterView.getParentView();
            }

            this.unchainFromParent();

            (new Promise(resolve => {
                if (masterView.hasView('collapsedModalBar')) {
                    resolve(masterView.getView('collapsedModalBar'));

                    return;
                }

                masterView
                    .createView('collapsedModalBar', 'views/collapsed-modal-bar', {
                        fullSelector: 'body > .collapsed-modal-bar',
                    })
                    .then(view => resolve(view));
            }))
            .then(barView => {
                barView.addModalView(this, {title: title});
            });
        });
    }

    unchainFromParent() {
        const key = this.getParentView().getViewKey(this);

        this.getParentView().unchainView(key);
    }

    
    beforeCollapse() {
        return new Promise(resolve => resolve());
    }

    
    adjustButtons() {
        this.adjustLeftButtons();
        this.adjustRightButtons();
    }

    
    adjustLeftButtons() {
        const $buttons = this.$el.find('footer.modal-footer > .main-btn-group button.btn');

        $buttons
            .removeClass('radius-left')
            .removeClass('radius-right');

        const $buttonsVisible = $buttons.filter('button:not(.hidden)');

        $buttonsVisible.first().addClass('radius-left');
        $buttonsVisible.last().addClass('radius-right');
    }

    
    adjustRightButtons() {
        const $buttons = this.$el.find('footer.modal-footer > .additional-btn-group button.btn:not(.btn-text)');

        $buttons
            .removeClass('radius-left')
            .removeClass('radius-right')
            .removeClass('margin-right');

        const $buttonsVisible = $buttons.filter('button:not(.hidden)');

        $buttonsVisible.first().addClass('radius-left');
        $buttonsVisible.last().addClass('radius-right');

        if ($buttonsVisible.last().next().hasClass('btn-text')) {
            $buttonsVisible.last().addClass('margin-right');
        }
    }

    
    initBodyScrollListener() {
        const $body = this.$el.find('> .dialog > .modal-dialog > .modal-content > .modal-body');
        const $footer = $body.parent().find('> .modal-footer');

        if (!$footer.length) {
            return;
        }

        $body.off('scroll.footer-shadow');

        $body.on('scroll.footer-shadow', () => {
            if ($body.scrollTop()) {
                $footer.addClass('shadowed');

                return;
            }

            $footer.removeClass('shadowed');
        });
    }
}

export default ModalView;
