/**
 *  Form Wizard
 */

'use strict';

$(function () {
    const select2 = $('.select2'),
        selectPicker = $('.selectpicker');

    // Bootstrap select
    if (selectPicker.length) {
        selectPicker.selectpicker();
    }

    // select2
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                placeholder: 'Select value',
                dropdownParent: $this.parent()
            });
        });
    }
});
(function () {
    // Numbered Wizard
    // --------------------------------------------------------------------
    const wizardNumbered = document.querySelector('.wizard-numbered');
    if (wizardNumbered) {
        const wizardNumberedBtnNextList = [].slice.call(wizardNumbered.querySelectorAll('.btn-next'));
        const wizardNumberedBtnPrevList = [].slice.call(wizardNumbered.querySelectorAll('.btn-prev'));
        const numberedStepper = new Stepper(wizardNumbered, {
            linear: true
        });
        if (wizardNumberedBtnNextList) {

            wizardNumberedBtnNextList.forEach(wizardNumberedBtnNext => {
                wizardNumberedBtnNext.addEventListener('click', event => {
                    if (wizardNumberedBtnNext.classList.contains('check-staff-assign')){
                        let allSelected = true;

                        // loop through all staff-select dropdowns
                        $(".staff-service-combination .staff-select").each(function() {
                            if ($(this).val() === "") {
                                allSelected = false;
                                $(this).css("border", "1px solid red"); // highlight empty
                            } else {
                                $(this).css("border", "");
                            }
                        });

                        if (!allSelected) {
                            // e.preventDefault(); // prevent stepper from moving
                            alert("Please assign staff for all products before proceeding!");
                            return false;
                        }
                    }
                    numberedStepper.next();

                });
            });
        }
        if (wizardNumberedBtnPrevList) {
            wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
                wizardNumberedBtnPrev.addEventListener('click', event => {
                    numberedStepper.previous();
                });
            });
        }

        if ($('.step').hasClass('edit-clss')){
            // alert('dsds');
            numberedStepper.to(2);
        }
        // if (wizardNumberedBtnSubmit) {
        //   wizardNumberedBtnSubmit.addEventListener('click', event => {
        //     alert('Submitted..!!');
        //   });
        // }
    }

    // // Vertical Wizard
    // // --------------------------------------------------------------------
    // const wizardVertical = document.querySelector('.wizard-vertical'),
    //     wizardVerticalBtnNextList = [].slice.call(wizardVertical.querySelectorAll('.btn-next')),
    //     wizardVerticalBtnPrevList = [].slice.call(wizardVertical.querySelectorAll('.btn-prev')),
    //     wizardVerticalBtnSubmit = wizardVertical.querySelector('.btn-submit');
    //
    // if (typeof wizardVertical !== undefined && wizardVertical !== null) {
    //     const verticalStepper = new Stepper(wizardVertical, {
    //         linear: true
    //     });
    //     if (wizardVerticalBtnNextList) {
    //         wizardVerticalBtnNextList.forEach(wizardVerticalBtnNext => {
    //             wizardVerticalBtnNext.addEventListener('click', event => {
    //                 verticalStepper.next();
    //             });
    //         });
    //     }
    //     if (wizardVerticalBtnPrevList) {
    //         wizardVerticalBtnPrevList.forEach(wizardVerticalBtnPrev => {
    //             wizardVerticalBtnPrev.addEventListener('click', event => {
    //                 verticalStepper.previous();
    //             });
    //         });
    //     }
    //
    //     // if (wizardVerticalBtnSubmit) {
    //     //   wizardVerticalBtnSubmit.addEventListener('click', event => {
    //     //     alert('Submitted..!!');
    //     //   });
    //     // }
    // }

    // Modern Wizard
    // --------------------------------------------------------------------
    // const wizardModern = document.querySelector('.wizard-modern-example'),
    //     wizardModernBtnNextList = [].slice.call(wizardModern.querySelectorAll('.btn-next')),
    //     wizardModernBtnPrevList = [].slice.call(wizardModern.querySelectorAll('.btn-prev')),
    //     wizardModernBtnSubmit = wizardModern.querySelector('.btn-submit');
    // if (typeof wizardModern !== undefined && wizardModern !== null) {
    //     const modernStepper = new Stepper(wizardModern, {
    //         linear: true
    //     });
    //     if (wizardModernBtnNextList) {
    //         wizardModernBtnNextList.forEach(wizardModernBtnNext => {
    //             wizardModernBtnNext.addEventListener('click', event => {
    //                 modernStepper.next();
    //             });
    //         });
    //     }
    //     if (wizardModernBtnPrevList) {
    //         wizardModernBtnPrevList.forEach(wizardModernBtnPrev => {
    //             wizardModernBtnPrev.addEventListener('click', event => {
    //                 modernStepper.previous();
    //             });
    //         });
    //     }
    //     // if (wizardModernBtnSubmit) {
    //     //   wizardModernBtnSubmit.addEventListener('click', event => {
    //     //     alert('Submitted..!!');
    //     //   });
    //     // }
    // }
    //
    // // Modern Vertical Wizard
    // // --------------------------------------------------------------------
    // const wizardModernVertical = document.querySelector('.wizard-modern-vertical'),
    //     wizardModernVerticalBtnNextList = [].slice.call(wizardModernVertical.querySelectorAll('.btn-next')),
    //     wizardModernVerticalBtnPrevList = [].slice.call(wizardModernVertical.querySelectorAll('.btn-prev')),
    //     wizardModernVerticalBtnSubmit = wizardModernVertical.querySelector('.btn-submit');
    // if (typeof wizardModernVertical !== undefined && wizardModernVertical !== null) {
    //     const modernVerticalStepper = new Stepper(wizardModernVertical, {
    //         linear: true
    //     });
    //     if (wizardModernVerticalBtnNextList) {
    //         wizardModernVerticalBtnNextList.forEach(wizardModernVerticalBtnNext => {
    //             wizardModernVerticalBtnNext.addEventListener('click', event => {
    //                 modernVerticalStepper.next();
    //             });
    //         });
    //     }
    //     if (wizardModernVerticalBtnPrevList) {
    //         wizardModernVerticalBtnPrevList.forEach(wizardModernVerticalBtnPrev => {
    //             wizardModernVerticalBtnPrev.addEventListener('click', event => {
    //                 modernVerticalStepper.previous();
    //             });
    //         });
    //     }
    //     // if (wizardModernVerticalBtnSubmit) {
    //     //   wizardModernVerticalBtnSubmit.addEventListener('click', event => {
    //     //     alert('Submitted..!!');
    //     //   });
    //     // }
    // }
})();
