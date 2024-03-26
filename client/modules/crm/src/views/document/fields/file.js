

define('crm:views/document/fields/file', ['views/fields/file'], function (Dep) {

    return Dep.extend({

        getValueForDisplay: function () {
            if (this.isListMode()) {
                let name = this.model.get(this.nameName);
                let id = this.model.get(this.idName);

                if (!id) {
                    return '';
                }

                return $('<a>')
                    .attr('title', name)
                    .attr('href', this.getBasePath() + '?entryPoint=download&id=' + id)
                    .attr('target', '_BLANK')
                    .append(
                        $('<span>').addClass('fas fa-paperclip small')
                    )
                    .get(0).outerHTML;
            }

            return Dep.prototype.getValueForDisplay.call(this);
        },
    });
});
