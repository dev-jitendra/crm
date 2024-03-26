

define('views/admin/complex-expression/modals/add-function', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        template: 'admin/formula/modals/add-function',

        fitHeight: true,

        backdrop: true,

        events: {
            'click [data-action="add"]': function (e) {
                this.trigger('add', $(e.currentTarget).data('value'));
            }
        },

        data: function () {
            var text = this.translate('formulaFunctions', 'messages', 'Admin')
                .replace('{documentationUrl}', this.documentationUrl);
            text = this.getHelper().transformMarkdownText(text, {linksInNewTab: true}).toString();

            return {
                functionDataList: this.functionDataList,
                text: text,
            };
        },

        setup: function () {
            this.header = this.translate('Function');

            this.documentationUrl = 'https:

            this.functionDataList = this.options.functionDataList ||
                this.getMetadata().get('app.complexExpression.functionList') || [];
        },

    });
});
