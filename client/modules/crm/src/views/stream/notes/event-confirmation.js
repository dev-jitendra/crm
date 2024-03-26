

define('crm:views/stream/notes/event-confirmation', ['views/stream/note'], function (Dep) {

    return Dep.extend({

        
        templateContent: `
            {{#unless noEdit}}
            <div class="pull-right right-container cell-buttons">
            {{{right}}}
            </div>
            {{/unless}}

            <div class="stream-head-container">
                <div class="pull-left">
                    {{{avatar}}}
                </div>
                <div class="stream-head-text-container">
                    <span class="{{iconClass}} text-{{style}}"></span>
                    <span class="text-muted message">{{{message}}}</span>
                </div>
            </div>
            <div class="stream-date-container">
                <a class="text-muted small" href="#Note/view/{{model.id}}">{{{createdAt}}}</a>
            </div>
        `,

        data: function () {
            let iconClass = ({
                'success': 'fas fa-check fa-sm',
                'danger': 'fas fa-times fa-sm',
                'warning': 'fas fa-question fa-sm',
            })[this.style] || '';

            return _.extend({
                statusText: this.statusText,
                style: this.style,
                iconClass: iconClass,
            }, Dep.prototype.data.call(this));
        },

        init: function () {
            if (this.getUser().isAdmin()) {
                this.isRemovable = true;
            }

            Dep.prototype.init.call(this);
        },

        setup: function () {
            this.inviteeType = this.model.get('relatedType');
            this.inviteeId = this.model.get('relatedId');
            this.inviteeName = this.model.get('relatedName');

            let data = this.model.get('data') || {};

            let status = data.status || 'Tentative';
            this.style = data.style || 'default';
            this.statusText = this.getLanguage().translateOption(status, 'acceptanceStatus', 'Meeting');

            this.messageName = 'eventConfirmation' + status;

            if (this.isThis) {
                this.messageName += 'This';
            }

            this.messageData['invitee'] =
                $('<a>')
                    .attr('href', '#' + this.inviteeType + '/view/' + this.inviteeId)
                    .attr('data-id', this.inviteeId)
                    .attr('data-scope', this.inviteeType)
                    .text(this.inviteeName);

            this.createMessage();
        },
    });
});
