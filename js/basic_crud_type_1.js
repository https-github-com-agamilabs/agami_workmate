/**
*  BasicCRUD => Perform basic operation such as create, read, update & delete
*
* example => studentoffice/student_scholarship.php
*/
class BasicCRUD {
    constructor(settings) {
        this.readURL = settings.readURL;
        this.createURL = settings.createURL;
        this.updateURL = settings.updateURL;
        this.deleteURL = settings.deleteURL;

        this.targetCard = $(settings.targetCard);
        this.addButton = this.targetCard.find(`.add_button`);
        this.filterForm = this.targetCard.find(`.filter_form`);

        this.targetContainer = $(settings.targetContainer);

        this.setupModal = $(settings.setupModal);
        this.setupForm = this.setupModal.find(`.setup_form`);

        this.topic = settings.topic;
        this.tablePK = settings.tablePK;

        if (this.addButton.length) {
            this.addButtonClickTrigger();
        }
        if (this.filterForm.length) {
            this.filterFormSubmitTrigger();
        }
        if (this.setupForm) {
            this.setupFormSubmitTrigger();
        }

        this.pagination = settings.hasOwnProperty(`pagination`) ? settings.pagination : false;

        if (this.pagination) {
            this.pageno = 1;
            this.previousPage = this.targetCard.find(`.previous_page`);
            this.nextPage = this.targetCard.find(`.next_page`);
            this.pagenoDiv = this.targetCard.find(`.pageno_div`);
            this.pagenoInput = this.targetCard.find(`.pageno_input`);
            this.paginationEventTrigger();
        }
    }

    get(json = {}) {
        this.targetContainer.empty();

        if (this.filterForm.length) {
            let obj = Object.fromEntries((new FormData(this.filterForm[0])).entries());
            json = { ...json, ...obj };
        }

        if (this.pagination) {
            json.pageno = this.pageno;
        }

        $.post(this.readURL, json, (resp) => {
            if (resp.error) {
                toastr.error(resp.message);

                $(this.targetContainer.is(`tbody`)
                    ? `<th colspan="${this.targetContainer.closest(`table`).find(`thead th`).length}"></th>`
                    : `<div class="w-100"></div>`)
                    .append(`<div class="text-center text-secondary w-100">
                            <div class="py-4">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                                <h5 class="text-500 font-weight-normal mb-0">${resp.message || `You haven't added any ${this.topic} yet.`}</h5>
                            </div>
                        </div>`)
                    .appendTo(this.targetContainer);
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

    successCallback(resp) {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            this.get();
            if (this.setupModal.is(`:visible`)) {
                this.setupModal.modal(`hide`);
            }
        }
    }

    addButtonClickTrigger() {
        this.addButton.click(() => {
            this.setupForm.trigger("reset").data(this.tablePK, -1).data(`action`, this.createURL);
            this.setupModal.modal(`show`).find(`.modal-title`).html(`Create ${this.topic}`);
            let select2Elements = $(`.select2-hidden-accessible`, this.setupForm);
            if (select2Elements.length) {
                select2Elements.val(null).trigger("change");
            }
        });
    }

    filterFormSubmitTrigger() {
        this.filterForm.submit((e) => {
            e.preventDefault();

            if (this.pagination) {
                this.pageno = 1;
                this.pagenoDiv.text(`Page: ${this.pageno}`);
                this.pagenoInput.val(this.pageno);
            }

            this.get();
        });
    }

    setupFormSubmitTrigger() {
        this.setupForm.submit((e) => {
            e.preventDefault();
            let json = Object.fromEntries((new FormData(this.setupForm[0])).entries());

            let tablePK = Number(this.setupForm.data(this.tablePK)) || 0;
            let url = this.setupForm.data(`action`);

            if (tablePK > 0) {
                json[this.tablePK] = tablePK;
            }

            $.post(url, json, resp => this.successCallback(resp), "json");
        });
    }

    editButtonTrigger(template, value) {
        $(`.edit_button`, template).click((e) => {
            this.setupModal.modal(`show`).find(`.modal-title`).html(`Update ${this.topic}`);
            this.setupForm.trigger("reset").data(this.tablePK, value[this.tablePK]).data(`action`, this.updateURL);

            $(`[name]`, this.setupForm).each((i, elem) => {
                let elementName = $(elem).attr("name");
                let elementValue = value[elementName];

                if (elementValue != null) {
                    $(elem).val(isNaN(elementValue) ? elementValue : Number(elementValue));

                    if ($(elem).hasClass(`select2-hidden-accessible`)) {
                        $(elem).trigger(`change`);
                    }
                }
            });
        });
    }

    deleteButtonTrigger(template, value) {
        $(`.delete_button`, template).click((e) => {
            if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;

            $.post(this.deleteURL, {
                [this.tablePK]: value[this.tablePK]
            }, resp => this.successCallback(resp), "json");
        });
    }

    paginationEventTrigger() {
        this.pagenoInput.on("keyup", (e) => {
            if (e.keyCode == 13) {
                this.pageno = Number(this.pagenoInput.val()) || 1;
                this.pagenoInput.hide();
                this.pagenoDiv.text(`Page: ${this.pageno}`).show();
                this.get();
            }
        });

        this.pagenoDiv.click((e) => {
            this.pagenoDiv.hide();
            this.pagenoInput.show().select();
        });

        this.pagenoInput.focusout((e) => {
            this.pagenoInput.hide();
            this.pagenoDiv.show();
        });

        this.previousPage.click((e) => {
            if (this.pageno == 1) {
                toastr.warning("Current page is the first page");
            } else {
                --this.pageno;
                this.pagenoDiv.text(`Page: ${this.pageno}`);
                this.pagenoInput.val(this.pageno);
                this.get();
            }
        });

        this.nextPage.click((e) => {
            ++this.pageno;
            this.pagenoDiv.text(`Page: ${this.pageno}`);
            this.pagenoInput.val(this.pageno);
            this.get();
        });
    }
}