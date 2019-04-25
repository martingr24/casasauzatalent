function actualizarPantalla(direction, element, syncHistory, callback) {
    if (typeof syncHistory === 'undefined'){
        syncHistory = true;
    }
    if (syncHistory) {
        history.pushState({url: direction, element: element}, "", "./");
        sessionStorage.url = direction;
        sessionStorage.element = element;
        sessionStorage.data = "{}";
    }
    $.ajax({
        url: direction,
        dataType: "html",
        beforeSend: function () {
$(element).html("<div class='loader' style='position: fixed;'><div class='ball-spin-fade-loader'><div></div><div></div><div></div>");
        }
    })
    .done(function (data) {
        $(element).html(data);
        $(".loader").fadeOut(300);

        if (typeof callback !== 'undefined') {
            callback();
        }
    })
    .fail(function (conn, status, error) {
        $(element).text("Error:" + error);
        $(".loader").fadeOut(300);
    });
}

window.onpopstate = function (event) {
    if(typeof event.state.url !== 'undefined'){
       if(typeof event.state.data !== 'undefined') {
            sendData(event.state.url,event.state.data,event.state.element,false);
       } else {
            actualizarPantalla(event.state.url,event.state.element,false);
       }
    }

};

function enviarFormulario (direccion, form, element, callback) {
    var progress = $(".progress");
    var bar = $(".progress-bar");
    var mensaje = $("#mensaje");
    if (form.get(0).checkValidity() === false){
        form.addClass("was-validated");
        form.find(".invalid-feedback").remove();
        form.find(":invalid").each(function() {
            var error = this.validationMessage;

            $(this).after("<div class='invalid-feedback'>" + error + "</div>");
        });
    } else {
        form.ajaxSubmit({
            type: "POST",
            target: element,
            url: direccion,
            beforeSubmit: function () {
                var percentVal = '0%';

                progress.prop("hidden",false);
                mensaje.prop("hidden",false);
                mensaje.html("Subiendo archivo...");
                bar.width(percentVal);
                bar.html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';

                bar.html(percentVal);
                bar.width(percentVal);
                bar.attr("aria-valuenow",percentComplete);
            },
            success: function (data) {
                progress.prop("hidden", true);
                mensaje.prop("hidden", true);

                if (typeof callback !== 'undefined') {
                    callback();
                }
            }
        });
    }
}