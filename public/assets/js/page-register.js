"use strict";

document.addEventListener("DOMContentLoaded", function () {
    // Ambil elemen form
    let form = document.querySelector("#formRegister");
    if (form && typeof FormValidation !== "undefined") {
        // Inisialisasi FormValidation
        FormValidation.formValidation(form, {
            fields: {
                name: {
                    validators: {
                        notEmpty: { message: "Please enter your full name" },
                    },
                },
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
                        regexp: {
                            regexp: /[A-Za-z0-9]/,
                            message: "Password must contain at least one lowercase, uppercase letter, and number",
                        },
                        stringLength: {
                            min: 8,
                            message: "Password must be more than 8 characters",
                        },
                    },
                },
                password_confirmation: {
                    validators: {
                        notEmpty: {
                            message: "Please confirm password",
                        },
                        identical: {
                            compare: () => form.querySelector('[name="password"]').value,
                            message: "The password and its confirmation do not match",
                        },
                        stringLength: {
                            min: 8,
                            message: "Password must be more than 8 characters",
                        },
                    },
                },
                terms: {
                    validators: {
                        notEmpty: {
                            message: "Please select terms and condition",
                        },
                    },
                },
                store_name: {
                    validators: {
                        notEmpty: { message: "Please enter store name" },
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
