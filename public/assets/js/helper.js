$(function () {
    // initialization helpers
    if (typeof $.fn.autoNumeric === 'function') {
        $(".autoNumeric, .autonumeric, .autonumber").autoNumeric();
    }

    if (typeof $.fn.DataTable === 'function') {
        $(".responsive-datatable").DataTable();
    }

    if (typeof $.fn.parsley === 'function') {
        $(".form-parsley").parsley();
    }

    if (typeof $.fn.select2 === 'function') {
        $(".select2").select2({
            width: "100%",
        });
    }

    // Custom function helpers
    /* disable enter to submit */
    $('.disableEnterSubmit').keypress(function (e) {
        let keycode = (event.keyCode ? event.keyCode : event.which);

        if (keycode == '13') {
            // Lakukan sesuatu ketika tombol enter ditekan
            e.preventDefault();
        }
    });
});

function currencyFormat(nominal, currency = 'IDR') {
    return nominal.toLocaleString('id-ID', {
        style: 'currency',
        currency
    });
}

function swalConfirmUrl(element, title = 'Are You Sure?', text = 'You won\'t be able to revert this!', refreshPage = true) {
    Swal.fire({
        title: title,
        text: text,
        type: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes"
    }).then(function (t) {
        if (t.value === true) {
            $.ajax({
                type: "get",
                url: $(element).data('url'),
                data: "data",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        toastSuccess(response.message ?? '');
                        
                        if (refreshPage) {
                            setTimeout(() => {
                               location.reload(); 
                            }, 1500);
                        }
                    } else {
                        toastDanger();
                    }
                },
            });
        }
    });

}

const generateRandomColor = () => {
    return '#' + Math.floor(Math.random() * 0xFFFFFF).toString(16).padStart(6, '0').toUpperCase()
}

function removeCurrencyFormat(str) {
    // Remove all non-digit characters
    let cleanedStr = str.replace(/[^\d.-]/g, '');

    // Convert to a number and then to an integer
    return parseInt(cleanedStr);
}

function submitModalRequest(element) {
    const formId = "#" + $(element).attr('id');
    const modalData = $(element).data();
    const modal = modalData.modal;
    const datatable = modalData.datatable;

    setAutoNumericRawValue();

    $.ajax({
        type: $(element).attr('method'),
        url: $(element).attr('action'),
        data: $(element).serialize(),
        dataType: "json",
        success: function (response) {
            if (response.success) {
                $(formId)[0].reset();
                $(`${formId} select`).val(null).trigger('change');
                $(`${formId} span.text-danger`).html('');
                $(`${modal} button.close`).click();
                $(datatable).DataTable().ajax.reload();

                toastSuccess(response.message);
            } else {
                toastDanger(response.message);
            }
        },
        error: function (xhr, status, error) {
            // Handle error response
            let response = xhr.responseJSON;

            $.each(response.errors, function (index, value) {
                $(`#${index}`).siblings('.text-danger').html(
                    `<i class="mdi mdi-close-circle"></i> ${value[0]}`);
            });

            $(` ${formId}.text-danger`).each(function (index, element) {
                $(element).html('');
            });

            toastDanger();
        }
    });
}

function deleteAjax(element) {
    const deleteForm = $(element).parent();

    $.ajax({
        type: deleteForm.attr('method'),
        url: deleteForm.attr('action'),
        data: deleteForm.serialize(),
        dataType: "json",
        success: function (response) {
            if (response.success) {
                const datatable = $(element).data('datatable');
                $(datatable).DataTable().ajax.reload();

                toastSuccess(response.message);
            } else {
                toastDanger(response.message);
            }
        },
        error: function (xhr, status, error) {
            toastDanger();
        }
    });
}

function saDelete(element, serverside = false) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then(function (t) {
        if (t.value === true) {
            if (!serverside) {
                $(element).parent().submit();
            } else {
                deleteAjax(element);
            }
        }
    })
}

if (typeof $.fn.autoNumeric === 'function') {
    function setAutoNumericRawValue() {
        $('.autoNumeric, .autonumeric, .autonumber').each(function (index, element) {
            const rawValue = $(this).autoNumeric('get');
            $(this).val(rawValue);
        });
    }
}


if (typeof $.fn.toast === 'function') {
    function toastDanger(text = null) {
        if (text === null) {
            text = "Change a few things up and try submitting again.";
        }

        $.toast({
            heading: "Something went wrong!",
            text: text,
            position: "top-right",
            loaderBg: "#bf441d",
            icon: "error",
            hideAfter: 3e3,
            stack: 1
        })
    }
}

if (typeof $.fn.toast === 'function') {
    function toastSuccess(text = '') {
        $.toast({
            heading: "Success!",
            text: text,
            position: "top-right",
            loaderBg: "#5ba035",
            icon: "success",
            hideAfter: 3e3,
            stack: 1
        })
    }
}

function unformatNumeric(element) {
    let value = element.val()

    if (isNaN(value)) {
        let unformatedValue = value.slice(0, -3).replace(/[^0-9]/g, '')

        element.val(unformatedValue)
    }
}
