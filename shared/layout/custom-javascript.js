toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

$(document).on("click", `.ripple`, function (e) {
    e = e.touches ? e.touches[0] : e;
    const r = this.getBoundingClientRect(),
        d = Math.sqrt(Math.pow(r.width, 2) + Math.pow(r.height, 2)) * 2;
    this.style.cssText = `--s: 0; --o: 1;`;
    this.offsetTop;
    this.style.cssText = `--t: 1; --o: 0; --d: ${d}; --x:${e.clientX - r.left}; --y:${e.clientY - r.top};`;
});

$(document).on("shown.bs.modal", ".modal", (e) => $(e.target).find("input:not(:disabled,input[type=button],input[type=submit]),select,textarea").filter(":visible:first").trigger("focus"));

$(document).on("keydown", "input:not(:disabled,input[type=button],input[type=submit]),select,textarea", function (e) {
    let form = $(this).parents("form:eq(0)");
    if (form && e.shiftKey && e.key === "Enter") {
        e.preventDefault();
        form.find(":submit").trigger("click");
    } else if (form && e.key === "Enter" && ($(this).prop("tagName").toLowerCase() != "textarea")) {
        let focusable = form.find("input:not(:disabled,input[type=button],input[type=submit]),select,textarea").filter(":visible");
        let next = focusable.eq(focusable.index(this) + 1);
        if (next.length) {
            next.focus();
        } else {
            e.preventDefault();
            form.find(":submit").trigger("click");
        }
    }
});