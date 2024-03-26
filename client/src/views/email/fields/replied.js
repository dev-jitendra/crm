

define('views/email/fields/replied', ['views/fields/link'], function (Dep) {

    return Dep.extend({

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.mode === 'detail') {
                var $a = this.$el.find('a');
                if ($a.get(0)) {
                    $(
                        '<span class="fas fa-arrow-left fa-sm link-field-icon text-soft"></span>'
                    ).insertBefore($a);
                }
            }
        },
    });
});
