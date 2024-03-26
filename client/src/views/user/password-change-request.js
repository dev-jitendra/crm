

define('views/user/password-change-request', ['view', 'model'], function (Dep, Model) {

    return Dep.extend({

        template: 'user/password-change-request',

        data: function () {
            return {
                requestId: this.options.requestId,
                notFound: this.options.notFound,
                notFoundMessage: this.notFoundMessage,
            };
        },

        events: {
            'click #btn-submit': function () {
                this.submit();
            },
        },

        setup: function () {
            let model = this.model = new Model();
            model.entityType = model.name = 'User';

            this.createView('password', 'views/user/fields/password', {
                model: model,
                mode: 'edit',
                selector: '.field[data-name="password"]',
                defs: {
                    name: 'password',
                    params: {
                        required: true,
                        maxLength: 255,
                    },
                },
                strengthParams: this.options.strengthParams,
            });

            this.createView('passwordConfirm', 'views/fields/password', {
                model: model,
                mode: 'edit',
                selector: '.field[data-name="passwordConfirm"]',
                defs: {
                    name: 'passwordConfirm',
                    params: {
                        required: true,
                        maxLength: 255,
                    },
                },
            });

            this.createView('generatePassword', 'views/user/fields/generate-password', {
                model: model,
                mode: 'detail',
                readOnly: true,
                selector: '.field[data-name="generatePassword"]',
                defs: {
                    name: 'generatePassword',
                },
                strengthParams: this.options.strengthParams,
            });

            this.createView('passwordPreview', 'views/fields/base', {
                model: model,
                mode: 'detail',
                readOnly: true,
                selector: '.field[data-name="passwordPreview"]',
                defs: {
                    name: 'passwordPreview',
                },
            });

            this.model.on('change:passwordPreview', () => this.reRender());

            let url = this.baseUrl = window.location.href.split('?')[0];

            this.notFoundMessage = this.translate('passwordChangeRequestNotFound', 'messages', 'User')
                .replace('{url}', url);
        },

        submit: function () {
            this.getView('password').fetchToModel();
            this.getView('passwordConfirm').fetchToModel();

            var notValid = this.getView('password').validate() ||
                this.getView('passwordConfirm').validate();

            var password = this.model.get('password');

            if (notValid) {
                return;
            }

            let $submit = this.$el.find('.btn-submit');

            $submit.addClass('disabled');

            Espo.Ajax
                .postRequest('User/changePasswordByRequest', {
                    requestId: this.options.requestId,
                    password: password,
                })
                .then(data => {
                    this.$el.find('.password-change').remove();

                    var url = data.url || this.baseUrl;

                    var msg = this.translate('passwordChangedByRequest', 'messages', 'User') +
                        ' <a href="' + url + '">' + this.translate('Login', 'labels', 'User') + '</a>.';

                    this.$el.find('.msg-box')
                        .removeClass('hidden')
                        .html('<span class="text-success">' + msg + '</span>');
                })
                .catch(() =>
                    $submit.removeClass('disabled')
                );
        },

    });
});
