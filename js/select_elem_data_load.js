/**
*  @SelectElemDataLoad Get data from url and then append option in select element
*
* example => studentoffice/student_scholarship.php
*/
class SelectElemDataLoad {
    pattern = /{{(.*?)}}/igm;

    constructor(settings) {
        this.readURL = settings.readURL || ``;
        this.targets = settings.targets || [];
        this.dependents = settings.dependents || [];

        this.optionText = settings.optionText || `text`;
        this.optionValue = settings.optionValue || `id`;
        this.templateString = settings.templateString || ``;

        if (settings.hasOwnProperty(`readURL`)) {
            this.get();
        } else {
            this.show();
        }
    }

    get() {
        $.each(this.targets, (_index, target) => {
            $(target.selectElem).empty();
        });

        let json = {};

        $.each(this.dependents, (_index, dependent) => {
            json[$(dependent).attr(`name`)] = $(dependent).val();
        });

        $.post(this.readURL, json, (resp) => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                if (resp.hasOwnProperty(`data`)) {
                    this.data = resp.data;
                } else if (resp.hasOwnProperty(`results`)) {
                    this.data = resp.results;
                }
                this.show(this.data);
            }
        }, "json");
    }

    show(data) {
        $.each(this.targets, (_index, target) => {
            $(target.selectElem).empty();

            if (target.hasOwnProperty(`defaultOptionText`)) {
                $(target.selectElem).append(new Option(target.defaultOptionText, target.defaultOptionValue));
            }

            $.each(data, (_indexInData, value) => {
                let templateString = `${this.templateString}`;

                if (this.templateString.length) {
                    var match;

                    while ((match = this.pattern.exec(templateString)) != null) {
                        templateString = templateString.replace(match[0], value[match[1]]);
                    }
                } else {
                    templateString = value[this.optionText];
                }

                $(target.selectElem).append(new Option($(`<div>${templateString}</div>`).text(), value[this.optionValue]));
            });

            if (target.hasOwnProperty(`select2Settings`)) {
                $(target.selectElem).select2(target.select2Settings).val(null).trigger("change");
            }
        });
    }
}