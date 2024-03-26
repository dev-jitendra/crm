



import View from 'view';
import $ from 'jquery';

class MasterSiteView extends View {

    template = 'site/master'

    views = {
        header: {
            id: 'header',
            view: 'views/site/header',
        },
        main: {
            id: 'main',
            view: false,
        },
        footer: {
            fullSelector: 'body > footer',
            view: 'views/site/footer',
        },
    }

    showLoadingNotification() {
        Espo.Ui.notify(' ... ');
    }

    hideLoadingNotification() {
        Espo.Ui.notify(false);
    }

    setup() {
        $(window).on('resize.' + this.cid, () => {
            this.adjustContent();
        });
    }

    onRemove() {
        $(window).off('resize.' + this.cid);
    }

    afterRender() {
        let params = this.getThemeManager().getParam('params');

        let $body = $('body');

        for (let param in params) {
            let value = this.getThemeManager().getParam(param);

            $body.attr('data-' + Espo.Utils.camelCaseToHyphen(param), value);
        }

        let footerView = this.getView('footer');

        if (footerView) {
            let html = footerView.$el.html() || '';

            if ((html.match(/espocrm/gi) || []).length < 2) {
                let text = 'PHAgY2xhc3M9ImNyZWRpdCBzbWFsbCI+JmNvcHk7IDxhIGhyZWY9Imh0dHA6Ly93d3cuZXNwb2Nyb' +
                    'S5jb20iPkVzcG9DUk08L2E+PC9wPg==';

                let decText;

                if (typeof window.atob === "function") {
                    decText = window.atob(text);
                } else if (typeof atob === "function") {
                    decText = atob(text);
                }

                if (decText) {
                    footerView.$el.html(decText);
                }
            }
        }

        this.$content = this.$el.find('> #content');

        this.adjustContent();

        let extensions = this.getHelper().getAppParam('extensions') || [];

        if (this.getConfig().get('maintenanceMode')) {
            this.createView('dialog', 'views/modal', {
                templateContent: '<div class="text-danger">{{complexText viewObject.options.message}}</div>',
                headerText: this.translate('maintenanceMode', 'fields', 'Settings'),
                backdrop: true,
                message: this.translate('maintenanceMode', 'messages'),
                buttonList: [
                    {
                        name: 'close',
                        label: this.translate('Close'),
                    }
                ],
            }, view => {
                view.render();
            });
        }
        else if (this.getHelper().getAppParam('auth2FARequired')) {
            this.createView('dialog', 'views/modals/auth2fa-required', {}, (view) => {
                view.render();
            });
        }
        else if (extensions.length !== 0) {
            this.processExtensions(extensions);
        }
    }

    adjustContent() {
        if (!this.isRendered()) {
            return;
        }

        if (window.innerWidth < this.getThemeManager().getParam('screenWidthXs')) {
            this.isSmallScreen = true;

            let height = window.innerHeight - this.$content.get(0).getBoundingClientRect().top;

            let $navbarCollapse = $('#navbar .navbar-body');

            if ($navbarCollapse.hasClass('in') || $navbarCollapse.hasClass('collapsing')) {
                height += $navbarCollapse.height();
            }

            let footerHeight = $('#footer').height() || 26;

            height -= footerHeight;

            if (height <= 0) {
                this.$content.css('minHeight', '');

                return;
            }

            this.$content.css('minHeight', height + 'px');

            return;
        }

        if (this.isSmallScreen) {
            this.$content.css('minHeight', '');
        }

        this.isSmallScreen = false;
    }

    
    processExtensions(list) {
        let messageList = [];

        list.forEach(item => {
            if (!item.notify) {
                return;
            }

            let message = item.licenseStatusMessage ??
                'extensionLicense' +
                Espo.Utils.upperCaseFirst(
                    Espo.Utils.hyphenToCamelCase(item.licenseStatus.toLowerCase())
                );

            messageList.push(
                this.translate(message, 'messages')
                    .replace('{name}', item.name)
            );
        });

        if (!messageList.length) {
            return;
        }

        let message = messageList.join('\n\n');

        message = this.getHelper().transformMarkdownText(message);

        let dialog = new Espo.Ui.Dialog({
            backdrop: 'static',
            buttonList: [
                {
                    name: 'close',
                    text: this.translate('Close'),
                    className: 'btn-s-wide',
                    onClick: () => dialog.close(),
                }
            ],
            className: 'dialog-confirm text-danger',
            body: message.toString(),
        });

        dialog.show();
    }
}

export default MasterSiteView;
