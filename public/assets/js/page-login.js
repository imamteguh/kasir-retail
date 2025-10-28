"use strict";

document.addEventListener("DOMContentLoaded", function () {
    // Ambil elemen form
    let form = document.querySelector("#formLogin");
    if (form && typeof FormValidation !== "undefined") {
        // Inisialisasi FormValidation
        FormValidation.formValidation(form, {
            fields: {
                email: {
                    validators: {
                        notEmpty: { message: "Please enter your email" },
                        emailAddress: {
                            message: "Please enter a valid email address",
                        },
                    },
                },
                password: {
                    validators: {
                        notEmpty: { message: "Please enter your password" },
                        stringLength: {
                            min: 8,
                            message: "Password must be more than 8 characters",
                        },
                    },
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: "",
                    rowSelector: ".form-control-validation",
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus(),
            },
            init: (validator) => {
                // Penempatan pesan error jika dalam input-group
                validator.on("plugins.message.placed", (e) => {
                    if (
                        e.element.parentElement.classList.contains(
                            "input-group"
                        )
                    ) {
                        e.element.parentElement.insertAdjacentElement(
                            "afterend",
                            e.messageElement
                        );
                    }
                });
            },
        });
    }
});
