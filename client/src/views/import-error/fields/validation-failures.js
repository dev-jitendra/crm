

define('views/import-error/fields/validation-failures', ['views/fields/base'], (Dep) => {

    
    return Dep.extend({

        detailTemplateContent: `
            {{#if itemList.length}}
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50%;">{{translate 'Field'}}</th>
                        <th>{{translateOption 'Validation' scope='ImportError' field='type'}}</th>
                    </tr>
                </thead>
                <tbody>
                    {{#each itemList}}
                    <tr>
                        <td>{{translate field category='fields' scope=entityType}}</td>
                        <td>
                            {{translate type category='fieldValidations'}}
                            {{#if popoverText}}
                            <a
                                role="button"
                                tabindex="-1"
                                class="text-muted popover-anchor"
                                data-text="{{popoverText}}"
                            ><span class="fas fa-info-circle"></span></a>
                            {{/if}}
                        </td>
                    </tr>
                    {{/each}}
                </tbody>
            </table>
            {{else}}
            <span class="none-value">{{translate 'None'}}</span>
            {{/if}}
        `,

        data: function () {
            let data = Dep.prototype.data.call(this);

            data.itemList = this.getDataList();

            return data;
        },

        afterRenderDetail: function () {
            this.$el.find('.popover-anchor').each((i, el) => {
                let text = this.getHelper().transformMarkdownText(el.dataset.text).toString();

                Espo.Ui.popover($(el), {content: text}, this);
            });
        },

        
        getDataList: function () {
            let itemList = Espo.Utils.cloneDeep(this.model.get(this.name)) || [];

            let entityType = this.model.get('entityType');

            if (Array.isArray(itemList)) {
                itemList.forEach(item => {
                    
                    let fieldManager = this.getFieldManager();
                    
                    let language = this.getLanguage();

                    let fieldType = fieldManager.getEntityTypeFieldParam(entityType, item.field, 'type');

                    if (!fieldType) {
                        return;
                    }

                    let key = fieldType + '_' + item.type;

                    if (!language.has(key, 'fieldValidationExplanations', 'Global')) {
                        return;
                    }

                    item.popoverText = language.translate(key, 'fieldValidationExplanations');
                });
            }

            return itemList;
        },
    });
});
