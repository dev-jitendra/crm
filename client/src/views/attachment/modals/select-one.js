

define('views/attachment/modals/select-one', ['views/modal'], function (Dep) {

    return Dep.extend({

        backdrop: true,

        
        templateContent:
            '<ul class="list-group no-side-margin">{{#each viewObject.options.dataList}}'+
            '<li class="list-group-item">'+
            '<a role="button" class="action" data-action="select" data-id="{{id}}">{{name}}</a>'+
            '</li>'+
            '{{/each}}</ul>',

        setup: function () {
            this.headerText = this.translate('Select');

            if (this.options.fieldLabel) {
                this.headerText += ': ' + this.options.fieldLabel;
            }
        },

        actionSelect: function (data) {
            this.trigger('select', data.id);
            this.remove();
        },
    });
});
