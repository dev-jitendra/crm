

define('views/settings/fields/oidc-redirect-uri', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        detailTemplateContent: `
            {{#if isNotEmpty}}
                <a
                    role="button"
                    data-action="copyToClipboard"
                    class="pull-right text-soft"
                    title="{{translate 'Copy to Clipboard'}}"
                ><span class="far fa-copy"></span></a>
                {{value}}
            {{else}}
                <span class="none-value">{{translate 'None'}}</span>
            {{/if}}
        `,

        portalCollection: null,

        data: function () {
            const isNotEmpty = this.model.entityType !== 'AuthenticationProvider' ||
                this.portalCollection;

            return {
                value: this.getValueForDisplay(),
                isNotEmpty: isNotEmpty,
            };
        },

        
        copyToClipboard: function () {
            const value = this.getValueForDisplay();

            navigator.clipboard.writeText(value).then(() => {
                Espo.Ui.success(this.translate('Copied to clipboard'));
            });
        },

        getValueForDisplay: function () {
            if (this.model.entityType === 'AuthenticationProvider') {
                if (!this.portalCollection) {
                    return null;
                }

                return this.portalCollection.models
                    .map(model => {
                        const file = 'oauth-callback.php'
                        const url = (model.get('url') || '').replace(/\/+$/, '') + `/${file}`;

                        const checkPart = `/portal/${model.id}/${file}`;

                        if (!url.endsWith(checkPart)) {
                            return url;
                        }

                        return url.slice(0, - checkPart.length) + `/portal/${file}`;
                    })
                    .join('\n');
            }

            const siteUrl = (this.getConfig().get('siteUrl') || '').replace(/\/+$/, '');

            return siteUrl + '/oauth-callback.php';
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.model.entityType === 'AuthenticationProvider') {
                this.getCollectionFactory()
                    .create('Portal')
                    .then(collection => {
                        collection.data.select = ['url', 'isDefault'];

                        collection.fetch().then(() => {
                            this.portalCollection = collection;

                            this.reRender();
                        })
                    });
            }
        },
    });
});
