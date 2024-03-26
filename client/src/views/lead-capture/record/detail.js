

define('views/lead-capture/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        setupActionItems: function () {
            Dep.prototype.setupActionItems.call(this);

            this.dropdownItemList.push({
                'label': 'Generate New API Key',
                'name': 'generateNewApiKey',
            });
        },

        actionGenerateNewApiKey: function () {
            this.confirm(this.translate('confirmation', 'messages'), () => {
                Espo.Ajax.postRequest('LeadCapture/action/generateNewApiKey', {id: this.model.id})
                    .then(data => {
                        this.model.set(data);
                    });
            });
        },
    });
});
