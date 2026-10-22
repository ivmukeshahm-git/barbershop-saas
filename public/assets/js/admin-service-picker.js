(function ($) {
    'use strict';

    var BC_SVC_MAX_QTY = 10;

    function initPicker($picker, options) {
        var opts = options || {};
        var $list = $picker.find('.bc-svc-picker-list');
        var $label = $picker.find('.bc-svc-picker-label');
        var $count = $picker.find('.bc-svc-picker-count');
        var state = {}; // keyed by service id string
        var hiddenWrap = null; // <i class="bc-svc-hidden-fields"> appended to form

        function readItems() {
            var items = {};
            $list.find('.bc-svc-item').each(function () {
                var $item = $(this);
                var id = String($item.data('id'));
                items[id] = {
                    id: id,
                    name: $item.data('name'),
                    duration: parseInt($item.data('duration'), 10) || 0
                };
            });
            return items;
        }

        var items = readItems();

        function refreshStateFromDom() {
            $list.find('.bc-svc-item').each(function () {
                var $item = $(this);
                var id = String($item.data('id'));
                var checked = $item.find('.bc-svc-check').is(':checked');
                var val = parseInt($item.find('.bc-svc-qty-val').text(), 10) || 1;
                if (checked && val < 1) val = 1;
                if (checked) {
                    state[id] = { qty: val };
                } else {
                    delete state[id];
                }
            });
        }

        function renderHidden() {
            var $host = opts.host ? $(opts.host) : $picker.closest('form');
            if (!$host.length) return;

            if (hiddenWrap && hiddenWrap.parent().length) {
                hiddenWrap.remove();
            }
            hiddenWrap = null;

            var $wrap = $('<i class="bc-svc-hidden-fields"></i>');
            var ids = Object.keys(state);
            ids.forEach(function (id) {
                var entry = state[id];
                var repeat = Math.max(1, entry.qty);
                for (var i = 0; i < repeat; i += 1) {
                    $wrap.append($('<input>').attr({
                        type: 'hidden',
                        name: 'service_id[]',
                        value: id
                    }));
                }
            });

            if (ids.length) {
                hiddenWrap = $wrap;
                $host.prepend($wrap);
            }
        }

        function currentServiceIds() {
            return Object.keys(state);
        }

        function updateLabel() {
            var ids = Object.keys(state);
            if (!ids.length) {
                $label.text('Select Service');
                $count.text('0 selected');
                return;
            }
            var parts = [];
            ids.forEach(function (id) {
                var entry = items[id];
                var svc = entry ? entry.name : id;
                var dur = entry ? entry.duration : 0;
                var qty = state[id].qty;
                parts.push(svc + ' (' + dur + ' min)' + (qty > 1 ? ' x' + qty : ''));
            });
            $label.text(parts.join(', '));
            var totalUnits = ids.reduce(function (sum, id) { return sum + state[id].qty; }, 0);
            $count.text(totalUnits + ' selected');
        }

        function emit() {
            refreshStateFromDom();
            renderHidden();
            updateLabel();
            if (typeof opts.onChange === 'function') {
                opts.onChange(currentServiceIds(), $.extend({}, state));
            }
        }

        function setItem(id, qty) {
            var $item = $list.find('.bc-svc-item[data-id="' + id + '"]');
            if (!$item.length) return;
            var val = Math.max(1, Math.min(BC_SVC_MAX_QTY, parseInt(qty, 10) || 1));
            $item.find('.bc-svc-check').prop('checked', true);
            $item.find('.bc-svc-qty-val').text(val);
            emit();
        }

        function removeItem(id) {
            var $item = $list.find('.bc-svc-item[data-id="' + id + '"]');
            if (!$item.length) return;
            $item.find('.bc-svc-check').prop('checked', false);
            $item.find('.bc-svc-qty-val').text(1);
            emit();
        }

        function selectAll() {
            $list.find('.bc-svc-check').prop('checked', true).each(function () {
                var $item = $(this).closest('.bc-svc-item');
                var val = parseInt($item.find('.bc-svc-qty-val').text(), 10) || 1;
                if (val < 1) $item.find('.bc-svc-qty-val').text(1);
            });
            emit();
        }

        function clearAll() {
            $list.find('.bc-svc-check').prop('checked', false).each(function () {
                $(this).closest('.bc-svc-item').find('.bc-svc-qty-val').text(1);
            });
            emit();
        }

        // API
        $picker.data('picker', {
            setItems: function (selections, silent) {
                // selections: array of ids, or map id->qty, or array of {id, qty}
                clearAllInnerState(true);
                if ($.isArray(selections)) {
                    var map = {};
                    selections.forEach(function (s) {
                        if (typeof s === 'object') {
                            map[String(s.id)] = s.qty;
                        } else {
                            map[String(s)] = 1;
                        }
                    });
                    Object.keys(map).forEach(function (id) {
                        var $item = $list.find('.bc-svc-item[data-id="' + id + '"]');
                        if (!$item.length) return;
                        var val = Math.max(1, Math.min(BC_SVC_MAX_QTY, parseInt(map[id], 10) || 1));
                        $item.find('.bc-svc-check').prop('checked', true);
                        $item.find('.bc-svc-qty-val').text(val);
                        state[id] = { qty: val };
                    });
                } else if (typeof selections === 'object') {
                    Object.keys(selections).forEach(function (id) {
                        var $item = $list.find('.bc-svc-item[data-id="' + id + '"]');
                        if (!$item.length) return;
                        var val = Math.max(1, Math.min(BC_SVC_MAX_QTY, parseInt(selections[id], 10) || 1));
                        $item.find('.bc-svc-check').prop('checked', true);
                        $item.find('.bc-svc-qty-val').text(val);
                        state[id] = { qty: val };
                    });
                }
                refreshStateFromDom();
                renderHidden();
                updateLabel();
                if (!silent && typeof opts.onChange === 'function') {
                    opts.onChange(currentServiceIds(), $.extend({}, state));
                }
            },
            getItems: function () {
                return currentServiceIds();
            },
            getState: function () {
                return $.extend({}, state);
            },
            clear: function (silent) {
                clearAllInnerState(false);
                renderHidden();
                updateLabel();
                if (!silent && typeof opts.onChange === 'function') {
                    opts.onChange(currentServiceIds(), $.extend({}, state));
                }
            }
        });

        function clearAllInnerState(silent) {
            $list.find('.bc-svc-check').prop('checked', false).each(function () {
                $(this).closest('.bc-svc-item').find('.bc-svc-qty-val').text(1);
            });
            state = {};
            if (!silent) {
                renderHidden();
                updateLabel();
            }
        }

        // Wire toggle
        $picker.find('.bc-svc-picker-toggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $picker.toggleClass('open');
        });

        $picker.on('click', function (e) {
            e.stopPropagation();
        });

        // Checkbox change
        $list.on('change', '.bc-svc-check', function () {
            var $item = $(this).closest('.bc-svc-item');
            var id = String($item.data('id'));
            var val = parseInt($item.find('.bc-svc-qty-val').text(), 10) || 1;
            if ($(this).is(':checked')) {
                state[id] = { qty: Math.max(1, val) };
            } else {
                $item.find('.bc-svc-qty-val').text(1);
                delete state[id];
            }
            emit();
        });

        // Qty steppers
        $list.on('click', '.bc-svc-plus', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $item = $(this).closest('.bc-svc-item');
            var id = String($item.data('id'));
            var val = parseInt($item.find('.bc-svc-qty-val').text(), 10) || 1;
            $item.find('.bc-svc-check').prop('checked', true);
            val = Math.min(BC_SVC_MAX_QTY, val + 1);
            $item.find('.bc-svc-qty-val').text(val);
            if (state[id]) {
                state[id].qty = val;
            } else {
                state[id] = { qty: val };
            }
            emit();
        });

        $list.on('click', '.bc-svc-minus', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $item = $(this).closest('.bc-svc-item');
            var id = String($item.data('id'));
            var val = parseInt($item.find('.bc-svc-qty-val').text(), 10) || 1;
            val = Math.max(1, val - 1);
            $item.find('.bc-svc-qty-val').text(val);
            if (state[id]) state[id].qty = val;
            emit();
        });

        $picker.find('[data-action="all"]').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            selectAll();
        });

        $picker.find('[data-action="clear"]').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            clearAll();
        });
    }

    function closeOnOutside(document) {
        $(document).on('click', function () {
            $('.bc-svc-picker.open').removeClass('open');
        });
    }

    $.fn.bcServicePicker = function (options) {
        this.each(function () {
            var $this = $(this);
            if (!$this.data('picker')) {
                initPicker($this, options);
            }
        });
        return this;
    };

    $(function () {
        closeOnOutside(document);
    });
})(jQuery);