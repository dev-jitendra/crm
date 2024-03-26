

define('crm:views/campaign-log-record/fields/data', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        listTemplate: 'crm:campaign-log-record/fields/data/detail',

    	getValueForDisplay: function () {
    		let action = this.model.get('action');

    		switch (action) {
    			case 'Sent':
                case 'Opened':
                    if (
                        this.model.get('objectId') &&
                        this.model.get('objectType') &&
                        this.model.get('objectName')
                    ) {
                        return $('<a>')
                            .attr('href', '#' + this.model.get('objectType') + '/view/' + this.model.get('objectId'))
                            .text(this.model.get('objectName'))
                            .get(0).outerHTML;
                    }

                    return $('<span>')
                        .text(this.model.get('stringData') || '')
                        .get(0).outerHTML;

    			case 'Clicked':
                    if (
                        this.model.get('objectId') &&
                        this.model.get('objectType') &&
                        this.model.get('objectName')
                    ) {
                        return $('<a>')
                            .attr('href', '#' + this.model.get('objectType') + '/view/' + this.model.get('objectId'))
                            .text(this.model.get('objectName'))
                            .get(0).outerHTML;
                    }

                    return $('<span>')
                        .text(this.model.get('stringData') || '')
                        .get(0).outerHTML;

                case 'Opted Out':
                    return $('<span>')
                        .text(this.model.get('stringData') || '')
                        .addClass('text-danger')
                        .get(0).outerHTML;

                case 'Bounced':
                    let emailAddress = this.model.get('stringData');
                    let type = this.model.get('stringAdditionalData');

                    let typeLabel = type === 'Hard' ?
                        this.translate('hard', 'labels', 'Campaign') :
                        this.translate('soft', 'labels', 'Campaign')

                    return $('<span>')
                        .append(
                            $('<span>')
                                .addClass('label label-default')
                                .text(typeLabel),
                            ' ',
                            $('<s>')
                                .text(emailAddress)
                                .addClass(type === 'Hard' ? 'text-danger' : '')
                        )
                        .get(0).outerHTML;
    		}

    		return '';
    	},
    });
});
