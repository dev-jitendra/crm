

define('views/settings/fields/theme', ['views/fields/enum', 'theme-manager', 'ui/select'],
function (Dep, ThemeManager, Select) {

    return Dep.extend({

        editTemplateContent: `
            <div class="grid-auto-fit-xxs">
                <div>
                    <select data-name="{{name}}" class="form-control main-element">
                        {{options
                            params.options value
                            scope=scope
                            field=name
                            translatedOptions=translatedOptions
                            includeMissingOption=true
                            styleMap=params.style
                        }}
                    </select>
                </div>
                {{#if navbarOptionList.length}}
                <div>
                    <select data-name="themeNavbar" class="form-control">
                        {{options navbarOptionList navbar translatedOptions=navbarTranslatedOptions}}
                    </select>
                </div>
                {{/if}}
            </div>
        `,

        data: function () {
            let data = Dep.prototype.data.call(this);

            data.navbarOptionList = this.getNavbarOptionList();
            data.navbar = this.getNavbarValue() || this.getDefaultNavbar();

            data.navbarTranslatedOptions = {};
            data.navbarOptionList.forEach(item => {
                data.navbarTranslatedOptions[item] = this.translate(item, 'themeNavbars');
            });

            return data;
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.initThemeManager();

            this.model.on('change:theme', (m, v, o) => {
                this.initThemeManager()

                if (o.ui) {
                    this.reRender()
                        .then(() => Select.focus(this.$element, {noTrigger: true}));
                }
            })
        },

        afterRenderEdit: function () {
            this.$navbar = this.$el.find('[data-name="themeNavbar"]');

            this.$navbar.on('change', () => this.trigger('change'));

            Select.init(this.$navbar);
        },

        getNavbarValue: function () {
            let params = this.model.get('themeParams') || {};

            return params.navbar;
        },

        getNavbarDefs: function () {
            if (!this.themeManager) {
                return null;
            }

            let params = this.themeManager.getParam('params');

            if (!params || !params.navbar) {
                return null;
            }

            return Espo.Utils.cloneDeep(params.navbar);
        },

        getNavbarOptionList: function () {
            let defs = this.getNavbarDefs();

            if (!defs) {
                return [];
            }

            let optionList = defs.options || [];

            if (!optionList.length || optionList.length === 1) {
                return [];
            }

            return optionList;
        },

        getDefaultNavbar: function () {
            let defs = this.getNavbarDefs() || {};

            return defs.default || null;
        },

        initThemeManager: function () {
            let theme = this.model.get('theme');

            if (!theme) {
                this.themeManager = null;

                return;
            }

            this.themeManager = new ThemeManager(
                this.getConfig(),
                this.getPreferences(),
                this.getMetadata(),
                theme
            );
        },

        getAttributeList: function () {
            return [this.name, 'themeParams'];
        },

        setupOptions: function () {
            this.params.options = Object.keys(this.getMetadata().get('themes') || {})
                .sort((v1, v2) => {
                    if (v2 === 'EspoRtl') {
                        return -1;
                    }

                    return this.translate(v1, 'theme')
                        .localeCompare(this.translate(v2, 'theme'));
                });
        },

        fetch: function () {
            let data = Dep.prototype.fetch.call(this);

            let params = {};

            if (this.$navbar.length) {
                params.navbar = this.$navbar.val();
            }

            data.themeParams = params;

            return data;
        },
    });
});
