

define('views/template/fields/font-face', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            var engine = this.getConfig().get('pdfEngine') || 'Dompdf';

            var fontFaceList = this.getMetadata().get([
                'app', 'pdfEngines', engine, 'fontFaceList',
            ]) || [];

            fontFaceList = Espo.Utils.clone(fontFaceList);

            fontFaceList.unshift('');

            this.params.options = fontFaceList;
        },
    });
});
