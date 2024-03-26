

define('views/stream/fields/post', ['views/fields/text'], function (Dep) {

    return Dep.extend({

        getValueForDisplay: function () {
            let text = Dep.prototype.getValueForDisplay.call(this);

            if (this.isDetailMode() || this.isListMode()) {
                let mentionData = (this.model.get('data') || {}).mentions || {};

                Object
                    .keys(mentionData)
                    .sort((a, b) => {
                        return a.length < b.length;
                    })
                    .forEach(item => {
                        var part = '[' + mentionData[item].name + '](#User/view/'+mentionData[item].id + ')';

                        text = text.replace(new RegExp(item, 'g'), part);
                    });
            }

            return text;
        },
    });
});
