

define('views/user/modals/login-as', ['views/modal'], function (Dep) {

    return Dep.extend({

        backdrop: true,

        templateContent: `
            <div class="well">
                {{translate 'loginAs' category='messages' scope='User'}}
            </div>
            <a href="{{viewObject.url}}" class="text-large">{{translate 'Login Link' scope='User'}}</a>
        `,

        setup: function () {
            this.$header = $('<span>')
                .append(
                    $('<span>').text(this.model.get('name')),
                    ' ',
                    $('<span>').addClass('chevron-right'),
                    ' ',
                    $('<span>').text(this.translate('Login')),
                );

            this.url = `?entryPoint=loginAs` +
                `&anotherUser=${this.options.anotherUser}&username=${this.options.username}`;
        },
    });
});
