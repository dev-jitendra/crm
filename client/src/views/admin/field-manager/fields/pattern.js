

define('views/admin/field-manager/fields/pattern', ['views/fields/varchar'], function (Dep) {

    
    return Dep.extend({

        noSpellCheck: true,

        setupOptions: function () {
            let patterns = this.getMetadata().get(['app', 'regExpPatterns']) || {};

            let patternList = Object.keys(patterns)
                .filter(item => !patterns[item].isSystem)
                .map(item => '$' + item);

            this.setOptionList(patternList);
        },
    })
});
