/**
 * Blue Crown Barbers — Admin SaaS UI helpers
 * Frontend only. Preserves existing LoaderShow/LoaderHide and AJAX contracts.
 */
(function (window, document, $) {
  "use strict";

  /* ---------- Shop currency helpers ---------- */
  window.SHOP_CURRENCY_SYMBOL = window.SHOP_CURRENCY_SYMBOL || "$";
  window.SHOP_CURRENCY = window.SHOP_CURRENCY || "USD";
  window.shopMoney = window.shopMoney || function (amount, decimals) {
    decimals = typeof decimals === "number" ? decimals : 2;
    var n = Number(amount);
    if (isNaN(n)) n = 0;
    return (window.SHOP_CURRENCY_SYMBOL || "") + n.toFixed(decimals);
  };

  /* ---------- Toast system ---------- */
  function ensureToastRoot() {
    var root = document.getElementById("bc-toast-root");
    if (!root) {
      root = document.createElement("div");
      root.id = "bc-toast-root";
      document.body.appendChild(root);
    }
    return root;
  }

  var toastIcons = {
    success: "bi-check-circle-fill",
    error: "bi-x-circle-fill",
    warning: "bi-exclamation-triangle-fill",
    info: "bi-info-circle-fill",
  };

  window.bcToast = function (type, title, message, timeout) {
    type = type || "info";
    timeout = typeof timeout === "number" ? timeout : 3200;
    var root = ensureToastRoot();
    var el = document.createElement("div");
    el.className = "bc-toast " + type;
    el.innerHTML =
      '<div class="bc-toast-icon"><i class="bi ' +
      (toastIcons[type] || toastIcons.info) +
      '"></i></div>' +
      '<div class="bc-toast-body"><strong>' +
      (title || "") +
      "</strong><span>" +
      (message || "") +
      "</span></div>";
    root.appendChild(el);
    setTimeout(function () {
      el.classList.add("hide");
      setTimeout(function () {
        if (el.parentNode) el.parentNode.removeChild(el);
      }, 260);
    }, timeout);
  };

  /* ---------- Enhanced confirm (SweetAlert wrapper) ---------- */
  window.bcConfirmDelete = function (opts) {
    opts = opts || {};
    if (typeof Swal === "undefined") {
      return Promise.resolve({ isConfirmed: window.confirm(opts.text || "Delete Record?") });
    }
    return Swal.fire({
      title: opts.title || "Delete Record?",
      text: opts.text || "This action cannot be undone.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: opts.confirmText || "Delete",
      cancelButtonText: opts.cancelText || "Cancel",
      reverseButtons: true,
      customClass: {
        confirmButton: "btn btn-danger me-2",
        cancelButton: "btn btn-label-secondary",
      },
      buttonsStyling: false,
    });
  };

  /* ---------- Button ripple ---------- */
  document.addEventListener(
    "click",
    function (e) {
      var btn = e.target.closest(".btn");
      if (!btn || btn.classList.contains("btn-link")) return;
      var rect = btn.getBoundingClientRect();
      var ripple = document.createElement("span");
      var size = Math.max(rect.width, rect.height);
      ripple.style.cssText =
        "position:absolute;width:" +
        size +
        "px;height:" +
        size +
        "px;left:" +
        (e.clientX - rect.left - size / 2) +
        "px;top:" +
        (e.clientY - rect.top - size / 2) +
        "px;border-radius:50%;background:rgba(255,255,255,.35);transform:scale(0);animation:bc-ripple .5s linear;pointer-events:none;";
      btn.style.position = btn.style.position || "relative";
      btn.style.overflow = "hidden";
      btn.appendChild(ripple);
      setTimeout(function () {
        if (ripple.parentNode) ripple.parentNode.removeChild(ripple);
      }, 500);
    },
    true
  );

  /* ---------- Password show/hide ---------- */
  $(document).on("click", "[data-bc-toggle-pass]", function () {
    var target = document.querySelector(this.getAttribute("data-bc-toggle-pass"));
    if (!target) return;
    var show = target.type === "password";
    target.type = show ? "text" : "password";
    var icon = this.querySelector("i");
    if (icon) {
      icon.className = show ? "bi bi-eye-slash" : "bi bi-eye";
    }
    this.setAttribute("aria-label", show ? "Hide password" : "Show password");
  });

  /* ---------- Wrap action buttons in tables ---------- */
  function wrapActionButtons($table) {
    if (!$table || !$table.length) return;
    $table.find("tbody tr").each(function () {
      var $tds = $(this).children("td");
      if (!$tds.length) return;
      var $td = $tds.last();
      if ($td.find(".bc-actions").length) return;
      var $btns = $td.find("a.btn, button.btn");
      if (!$btns.length) return;

      $btns.each(function () {
        var $btn = $(this);
        var text = $.trim($btn.clone().children().remove().end().text());
        // Only square-size pure icon buttons (keep labeled buttons full width)
        if ((!text || text.length <= 2) && $btn.find("i, .tf-icons, .bx, .fa, .bi").length) {
          $btn.addClass("bc-btn-icon");
        }
        if ($btn.hasClass("p-2") && (!text || text.length <= 2)) {
          $btn.addClass("bc-btn-icon");
        }
      });

      $td.contents().filter(function () {
        return this.nodeType === 3 && !$.trim(this.nodeValue);
      }).remove();

      $btns.wrapAll('<div class="bc-actions"></div>');
    });
  }

  /* ---------- DataTables defaults (empty state + polish) ---------- */
  function enhanceDataTables() {
    if (!$.fn || !$.fn.dataTable) return;

    $.extend(true, $.fn.dataTable.defaults, {
      language: {
        emptyTable:
          '<div class="bc-empty-state"><strong>No records found</strong><span>There is nothing to show here yet.</span></div>',
        zeroRecords:
          '<div class="bc-empty-state"><strong>No records found</strong><span>There is nothing to show here yet.</span></div>',
        processing: "Loading...",
        search: "Search:",
        searchPlaceholder: "Search...",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)",
        paginate: {
          previous: "Previous",
          next: "Next",
        },
      },
      drawCallback: function () {
        var api = this.api();
        var $table = $(api.table().node());
        var $wrap = $table.closest(".dataTables_wrapper");
        $wrap.addClass("bc-dt-ready");
        wrapActionButtons($table);

        // Re-init tooltips for newly drawn action buttons
        if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
          $table.find("[title]").each(function () {
            try {
              var existing = bootstrap.Tooltip.getInstance(this);
              if (existing) existing.dispose();
              new bootstrap.Tooltip(this, { trigger: "hover", container: "body" });
            } catch (e) { /* ignore */ }
          });
        }
      },
    });

    // Also polish tables already initialized after defaults applied
    $(document).on("draw.dt", function (e, settings) {
      var api = new $.fn.dataTable.Api(settings);
      wrapActionButtons($(api.table().node()));
    });
  }

  /* ---------- Tooltips ---------- */
  function initTooltips() {
    if (typeof bootstrap === "undefined" || !bootstrap.Tooltip) return;
    document.querySelectorAll('[title]:not([data-bs-toggle="dropdown"])').forEach(function (el) {
      if (el.closest(".tox") || el.tagName === "OPTION") return;
      try {
        var existing = bootstrap.Tooltip.getInstance(el);
        if (existing) existing.dispose();
        new bootstrap.Tooltip(el, { trigger: "hover", container: "body" });
      } catch (e) { /* ignore */ }
    });
  }

  /* ---------- Force sidebar always expanded ---------- */
  function lockSidebarOpen() {
    document.documentElement.classList.remove("layout-menu-collapsed");
    document.documentElement.classList.remove("layout-menu-hover");
    document.body.classList.remove("layout-menu-expanded");
    // Hide any leftover toggles/overlays
    document.querySelectorAll(".layout-menu-toggle, .layout-overlay, .drag-target").forEach(function (el) {
      el.style.display = "none";
    });
  }

  /* ---------- Navbar search focus shortcut ---------- */
  document.addEventListener("keydown", function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === "/") {
      var input = document.getElementById("bcNavbarSearch");
      if (input) {
        e.preventDefault();
        input.focus();
      }
    }
  });

  /* ---------- AJAX global loading helpers (non-breaking) ---------- */
  if ($) {
    $(document).ajaxSend(function (event, jqXHR, settings) {
      var url = settings.url || "";
      if (url.indexOf("heartbeat") !== -1) return;
      document.body.classList.add("bc-ajax-busy");
    });
    $(document).ajaxComplete(function () {
      document.body.classList.remove("bc-ajax-busy");
    });
  }

  /* ---------- Page enter animation ---------- */
  function animateContent() {
    var content = document.querySelector(".container-xxl.container-p-y, .content-wrapper .container-xxl");
    if (content) content.classList.add("bc-animate-in");
  }

  /* ---------- Wrap actions on non-DT tables too ---------- */
  function polishStaticTables() {
    $("table").each(function () {
      wrapActionButtons($(this));
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    lockSidebarOpen();
    animateContent();
    polishStaticTables();
    setTimeout(initTooltips, 400);

    // Keep forcing open if Vuexy menu.js toggles class later
    var obs = new MutationObserver(function () {
      if (document.documentElement.classList.contains("layout-menu-collapsed")) {
        document.documentElement.classList.remove("layout-menu-collapsed");
      }
    });
    obs.observe(document.documentElement, { attributes: true, attributeFilter: ["class"] });

    if (window.location.hash === "#change-password") {
      var tab = document.getElementById("tabChangePassword");
      if (tab) tab.click();
    }
  });

  // Apply DataTables defaults immediately (before page @stack scripts init tables)
  enhanceDataTables();

  window.showToast = window.bcToast;
})(window, document, window.jQuery);
