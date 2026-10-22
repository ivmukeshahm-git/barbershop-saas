/**
 * Staff Day Calendar — multi-column resource day view
 * Compatible shim: refetchEvents(), changeView(), gotoDate(), render()
 */
(function (window, $) {
  'use strict';

  function pad(n) {
    return n < 10 ? '0' + n : '' + n;
  }

  function formatHourLabel(hour24) {
    var h = hour24 % 12;
    if (h === 0) h = 12;
    var period = hour24 >= 12 ? 'pm' : 'am';
    return { h: String(h) + ':00', p: period };
  }

  function fmtMinutes(m) {
    var h24 = Math.floor(m / 60) % 24;
    var mm = m % 60;
    var hr = h24 % 12;
    if (hr === 0) hr = 12;
    return hr + ':' + pad(mm);
  }

  function formatClock(date) {
    var h = date.getHours();
    var m = date.getMinutes();
    var hr = h % 12;
    if (hr === 0) hr = 12;
    var period = h >= 12 ? 'pm' : 'am';
    return hr + ':' + pad(m) + ' ' + period;
  }

  function formatRange(start, end) {
    function short(d) {
      var h = d.getHours();
      var m = d.getMinutes();
      var hr = h % 12;
      if (hr === 0) hr = 12;
      return hr + ':' + pad(m);
    }
    return short(start) + ' - ' + short(end);
  }

  function parseDate(value) {
    if (!value) return null;
    if (value instanceof Date) return value;
    var s = String(value).replace('T', ' ').replace(/-/g, '/');
    var d = new Date(s);
    if (isNaN(d.getTime())) d = new Date(value);
    return isNaN(d.getTime()) ? null : d;
  }

  function startOfDay(d) {
    var x = new Date(d);
    x.setHours(0, 0, 0, 0);
    return x;
  }

  function sameDay(a, b) {
    return (
      a.getFullYear() === b.getFullYear() &&
      a.getMonth() === b.getMonth() &&
      a.getDate() === b.getDate()
    );
  }

  function formatTitleDate(d) {
    try {
      return d.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
      });
    } catch (e) {
      return pad(d.getMonth() + 1) + '/' + pad(d.getDate()) + '/' + d.getFullYear();
    }
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  /**
   * Wall-clock "now" in a given IANA timezone (e.g. America/Edmonton).
   * Returns a Date whose getHours/getDate match that timezone's local clock.
   */
  function nowInTimezone(timeZone) {
    var tz = timeZone || 'America/Edmonton';
    try {
      var parts = new Intl.DateTimeFormat('en-US', {
        timeZone: tz,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hourCycle: 'h23',
      }).formatToParts(new Date());
      var map = {};
      parts.forEach(function (p) {
        if (p.type !== 'literal') map[p.type] = p.value;
      });
      var hour = parseInt(map.hour, 10);
      if (hour === 24) hour = 0;
      return new Date(
        parseInt(map.year, 10),
        parseInt(map.month, 10) - 1,
        parseInt(map.day, 10),
        hour,
        parseInt(map.minute, 10),
        parseInt(map.second, 10)
      );
    } catch (e) {
      return new Date();
    }
  }

  var STAFF_ORDER_KEY = 'bc_staff_calendar_order';

  function loadStaffOrder() {
    try {
      var raw = localStorage.getItem(STAFF_ORDER_KEY);
      var parsed = raw ? JSON.parse(raw) : null;
      return Array.isArray(parsed) ? parsed.map(String) : null;
    } catch (e) {
      return null;
    }
  }

  function saveStaffOrder(ids) {
    try {
      localStorage.setItem(STAFF_ORDER_KEY, JSON.stringify(ids.map(String)));
    } catch (e) {}
  }

  function applyOrderToStaff(staffList, orderIds) {
    if (!orderIds || !orderIds.length || !staffList || !staffList.length) {
      return staffList.slice();
    }
    var map = {};
    staffList.forEach(function (s) {
      map[String(s.id)] = s;
    });
    var ordered = [];
    orderIds.forEach(function (id) {
      var key = String(id);
      if (map[key]) {
        ordered.push(map[key]);
        delete map[key];
      }
    });
    Object.keys(map).forEach(function (key) {
      ordered.push(map[key]);
    });
    return ordered;
  }

  function StaffDayCalendar(el, options) {
    this.el = typeof el === 'string' ? document.querySelector(el) : el;
    this.options = options || {};
    this.timezone =
      this.options.timezone ||
      (typeof window.BC_APP_TIMEZONE !== 'undefined' ? window.BC_APP_TIMEZONE : 'America/Edmonton');
    this.staffAll = applyOrderToStaff(this.options.staff || [], loadStaffOrder());
    this.slotMin = this.options.slotMinHour != null ? this.options.slotMinHour : 8;
    this.slotMax = this.options.slotMaxHour != null ? this.options.slotMaxHour : 20;
    this.hourHeight = this.options.hourHeight || 72;
    this.currentDate = startOfDay(this.options.initialDate || nowInTimezone(this.timezone));
    this.events = [];
    this._nowTimer = null;
    this._dragStaffId = null;
    this.view = { type: 'resourceTimeGridDay' };

    this.el.classList.add('bc-staff-cal');
    this.el.innerHTML =
      '<div class="bc-staff-cal-toolbar">' +
      '  <div class="bc-sc-nav">' +
      '    <button type="button" class="btn btn-sm btn-outline-secondary bc-sc-prev" aria-label="Previous day"><i class="bx bx-chevron-left"></i></button>' +
      '    <button type="button" class="btn btn-sm btn-outline-secondary bc-sc-today">Today</button>' +
      '    <button type="button" class="btn btn-sm btn-outline-secondary bc-sc-next" aria-label="Next day"><i class="bx bx-chevron-right"></i></button>' +
      '    <div class="bc-sc-title"></div>' +
      '  </div>' +
      '  <div class="bc-sc-nav">' +
      '    <button type="button" class="btn btn-sm btn-primary bc-sc-break">Break</button>' +
      '  </div>' +
      '</div>' +
      '<div class="bc-sc-shell">' +
      '  <div class="bc-sc-header-scroll"><div class="bc-sc-header-row"></div></div>' +
      '  <div class="bc-sc-body-scroll"><div class="bc-sc-body-row"></div></div>' +
      '</div>';

    this.$title = this.el.querySelector('.bc-sc-title');
    this.$headerScroll = this.el.querySelector('.bc-sc-header-scroll');
    this.$bodyScroll = this.el.querySelector('.bc-sc-body-scroll');
    this.$headerRow = this.el.querySelector('.bc-sc-header-row');
    this.$bodyRow = this.el.querySelector('.bc-sc-body-row');

    var self = this;
    this.el.querySelector('.bc-sc-prev').addEventListener('click', function () {
      self.shiftDay(-1);
    });
    this.el.querySelector('.bc-sc-next').addEventListener('click', function () {
      self.shiftDay(1);
    });
    this.el.querySelector('.bc-sc-today').addEventListener('click', function () {
      self.gotoDate(nowInTimezone(self.timezone));
    });
    this.el.querySelector('.bc-sc-break').addEventListener('click', function () {
      if (typeof self.options.onBreakClick === 'function') self.options.onBreakClick();
    });

    // Sync horizontal scroll header <-> body
    var syncing = false;
    this.$bodyScroll.addEventListener('scroll', function () {
      if (syncing) return;
      syncing = true;
      self.$headerScroll.scrollLeft = self.$bodyScroll.scrollLeft;
      syncing = false;
    });
    this.$headerScroll.addEventListener('scroll', function () {
      if (syncing) return;
      syncing = true;
      self.$bodyScroll.scrollLeft = self.$headerScroll.scrollLeft;
      syncing = false;
    });

    this.render();
    this.refetchEvents();
    this._nowTimer = setInterval(function () {
      self.updateNowLine();
    }, 30000);
  }

  StaffDayCalendar.prototype.getVisibleStaff = function () {
    var filterId = this.options.getStaffFilter
      ? String(this.options.getStaffFilter() || '')
      : '';
    if (!filterId) return this.staffAll.slice();
    return this.staffAll.filter(function (s) {
      return String(s.id) === filterId;
    });
  };

  StaffDayCalendar.prototype.getDefaultAvatar = function () {
    return (
      this.options.defaultAvatar ||
      (typeof window.BC_STAFF_DEFAULT_AVATAR !== 'undefined'
        ? window.BC_STAFF_DEFAULT_AVATAR
        : '')
    );
  };

  StaffDayCalendar.prototype.autoFitColumnWidth = function (staffCount) {
    var count = Math.max(staffCount || 1, 1);
    var shell = this.el.querySelector('.bc-sc-shell');
    var avail = (shell ? shell.clientWidth : this.el.clientWidth) || 900;
    avail = Math.max(200, avail - this.slotGutterWidth());
    var minCol = 160;
    var colW = Math.floor(avail / count);
    if (colW < minCol) colW = minCol;
    this.el.style.setProperty('--bc-sc-col-w', colW + 'px');
    this.el.style.setProperty('--bc-sc-cols', String(count));
    return colW;
  };

  StaffDayCalendar.prototype.slotGutterWidth = function () {
    var g = getComputedStyle(this.el).getPropertyValue('--bc-sc-time-w');
    var n = parseFloat(g);
    return isNaN(n) ? 72 : n;
  };

  StaffDayCalendar.prototype.shiftDay = function (delta) {
    var d = new Date(this.currentDate);
    d.setDate(d.getDate() + delta);
    this.gotoDate(d);
  };

  StaffDayCalendar.prototype.gotoDate = function (date) {
    this.currentDate = startOfDay(parseDate(date) || new Date());
    this.events = [];
    this._availability = null;
    this.render();
    this.refetchEvents();
    if (typeof this.options.onDateChange === 'function') {
      this.options.onDateChange(this.currentDate);
    }
  };

  StaffDayCalendar.prototype.changeView = function (type, dateStr) {
    if (dateStr) this.gotoDate(dateStr);
    else this.render();
  };

  StaffDayCalendar.prototype.render = function () {
    var self = this;
    var staff = this.getVisibleStaff();
    this.computeHourHeights();
    var gridH = this.totalGridHeight();
    var defaultAvatar = this.getDefaultAvatar();

    this.el.style.setProperty('--bc-sc-hour-h', this.hourHeight + 'px');
    this.$title.textContent = formatTitleDate(this.currentDate);
    this.autoFitColumnWidth(staff.length);

    if (!staff.length) {
      this.$headerRow.innerHTML = '';
      this.$bodyRow.innerHTML =
        '<div class="bc-sc-empty w-100">No staff members found.</div>';
      return;
    }

    // Header
    var headerHtml =
      '<div class="bc-sc-time-gutter"></div><div class="bc-sc-cols">';
    staff.forEach(function (s) {
      var avatar = s.avatar || defaultAvatar;
      var name = (s.title || s.name || 'Staff').toString().trim() || 'Staff';
      headerHtml +=
        '<div class="bc-sc-staff-head" draggable="true" data-staff-id="' +
        s.id +
        '">' +
        '<span class="bc-sc-drag-handle" aria-hidden="true"><i class="bx bx-menu"></i></span>' +
        '<img class="bc-sc-staff-avatar" src="' +
        avatar +
        '" alt="' +
        escapeHtml(name) +
        '" draggable="false" onerror="this.onerror=null;this.src=\'' +
        defaultAvatar.replace(/'/g, "\\'") +
        '\'">' +
        '<div class="bc-sc-staff-name" title="' +
        escapeHtml(name) +
        '" style="display:block!important;width:100%!important;color:#111!important;-webkit-text-fill-color:#111!important;font-size:13px!important;font-weight:700!important;line-height:1.3!important;text-align:center!important;opacity:1!important;visibility:visible!important;margin:0!important;padding:2px 4px!important;">' +
        escapeHtml(name) +
        '</div>' +
        '</div>';
    });
    headerHtml += '</div>';
    this.$headerRow.innerHTML = headerHtml;
    this.bindStaffColumnDrag();

    // Body: time gutter + columns (hour rows expand only where appointments need space)
    var timeHtml =
      '<div class="bc-sc-time-gutter"><div class="bc-sc-time-labels" style="height:' +
      gridH +
      'px">';
    for (var h = this.slotMin; h < this.slotMax; h++) {
      var lab = formatHourLabel(h);
      var hourH = this.getHourHeight(h);
      timeHtml +=
        '<div class="bc-sc-time-label" style="height:' +
        hourH +
        'px"><span><span class="h">' +
        lab.h +
        '</span><span class="p">' +
        lab.p +
        '</span></span></div>';
    }
    var endLab = formatHourLabel(this.slotMax % 24);
    timeHtml +=
      '<div class="bc-sc-time-label bc-sc-time-label-end"><span><span class="h">' +
      endLab.h +
      '</span><span class="p">' +
      endLab.p +
      '</span></span></div>';
    timeHtml += '</div></div>';

    var colsHtml =
      '<div class="bc-sc-cols bc-sc-cols-var" style="height:' +
      gridH +
      'px;position:relative">' +
      this.buildHourLinesHtml(gridH);
    staff.forEach(function (s) {
      colsHtml +=
        '<div class="bc-sc-col" data-staff-id="' +
        s.id +
        '" style="height:' +
        gridH +
        'px"></div>';
    });
    colsHtml +=
      '<div class="bc-sc-now" hidden><div class="bc-sc-now-badge"></div><div class="bc-sc-now-line"></div></div>';
    colsHtml += '</div>';

    this.$bodyRow.innerHTML = timeHtml + colsHtml;

    // Slot click
    this.$bodyRow.querySelectorAll('.bc-sc-col').forEach(function (col) {
      col.addEventListener('click', function (e) {
        if (e.target.closest('.bc-sc-event')) return;
        var rect = col.getBoundingClientRect();
        var localY = e.clientY - rect.top;
        var clickDate = self.dateFromLocalY(localY);
        var staffId = col.getAttribute('data-staff-id');
        if (self.isStaffUnavailableAt(staffId, clickDate)) {
          if (typeof self.options.onUnavailableClick === 'function') {
            self.options.onUnavailableClick({
              date: clickDate,
              staffId: staffId,
            });
          }
          return;
        }
        if (typeof self.options.onSlotClick === 'function') {
          self.options.onSlotClick({
            date: clickDate,
            staffId: staffId,
          });
        }
      });
    });

    this.placeEvents();
    this.updateNowLine();
    this.scrollToNowOrBusiness();

    if (this._availability) {
      this.paintAvailability(this._availability);
    }

    requestAnimationFrame(function () {
      self.autoFitColumnWidth(staff.length);
    });
  };

  StaffDayCalendar.prototype.bindStaffColumnDrag = function () {
    var self = this;
    var heads = this.$headerRow.querySelectorAll('.bc-sc-staff-head');
    if (!heads.length) return;

    // When filtered to one staff, skip drag
    if (heads.length < 2) {
      heads.forEach(function (h) {
        h.removeAttribute('draggable');
        h.classList.add('is-drag-disabled');
      });
      return;
    }

    heads.forEach(function (head) {
      head.addEventListener('dragstart', function (e) {
        self._dragStaffId = head.getAttribute('data-staff-id');
        head.classList.add('is-dragging');
        self.el.classList.add('bc-sc-is-reordering');
        try {
          e.dataTransfer.effectAllowed = 'move';
          e.dataTransfer.setData('text/plain', self._dragStaffId || '');
        } catch (err) {}
      });

      head.addEventListener('dragend', function () {
        self._dragStaffId = null;
        self.el.classList.remove('bc-sc-is-reordering');
        self.$headerRow.querySelectorAll('.bc-sc-staff-head').forEach(function (h) {
          h.classList.remove('is-dragging', 'is-drop-target');
        });
      });

      head.addEventListener('dragover', function (e) {
        e.preventDefault();
        try {
          e.dataTransfer.dropEffect = 'move';
        } catch (err) {}
        self.$headerRow.querySelectorAll('.bc-sc-staff-head').forEach(function (h) {
          h.classList.toggle('is-drop-target', h === head);
        });
      });

      head.addEventListener('dragleave', function () {
        head.classList.remove('is-drop-target');
      });

      head.addEventListener('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var fromId = self._dragStaffId;
        try {
          fromId = fromId || e.dataTransfer.getData('text/plain');
        } catch (err) {}
        var toId = head.getAttribute('data-staff-id');
        head.classList.remove('is-drop-target');
        if (!fromId || !toId || String(fromId) === String(toId)) return;
        self.reorderStaffColumns(fromId, toId);
      });
    });
  };

  StaffDayCalendar.prototype.reorderStaffColumns = function (fromId, toId) {
    var list = this.staffAll.slice();
    var fromIdx = -1;
    var toIdx = -1;
    list.forEach(function (s, i) {
      if (String(s.id) === String(fromId)) fromIdx = i;
      if (String(s.id) === String(toId)) toIdx = i;
    });
    if (fromIdx < 0 || toIdx < 0 || fromIdx === toIdx) return;

    var moved = list.splice(fromIdx, 1)[0];
    list.splice(toIdx, 0, moved);
    this.staffAll = list;
    saveStaffOrder(
      list.map(function (s) {
        return String(s.id);
      })
    );

    if (typeof this.options.onStaffOrderChange === 'function') {
      this.options.onStaffOrderChange(
        list.map(function (s) {
          return String(s.id);
        })
      );
    }

    this.render();
  };

  StaffDayCalendar.prototype.minutesFromDayStart = function (date) {
    return date.getHours() * 60 + date.getMinutes() + date.getSeconds() / 60;
  };

  StaffDayCalendar.prototype.getHourHeight = function (hour) {
    if (this.hourHeights && this.hourHeights[hour] != null) {
      return this.hourHeights[hour];
    }
    return this.hourHeight;
  };

  StaffDayCalendar.prototype.normalizeServiceNames = function (props) {
    var serviceNames = (props && props.service_names) || '';
    if (Array.isArray(serviceNames)) {
      serviceNames = serviceNames.filter(Boolean).join(', ');
    }
    serviceNames = String(serviceNames || '').trim();
    if (serviceNames.toLowerCase() === 'break') {
      serviceNames = '';
    }
    return serviceNames;
  };

  StaffDayCalendar.prototype.estimateEventContentHeight = function (props, isBreak) {
    props = props || {};
    var serviceNames = this.normalizeServiceNames(props);
    var lines = 2; // time + customer/break title
    if (!isBreak && serviceNames) lines += 1;
    if (
      !isBreak &&
      props.staffName &&
      String(props.staffName).toLowerCase().indexOf('first available') === 0 &&
      props.assigned_staff_name
    ) {
      lines += 1;
    }
    return Math.max(44, 10 + lines * 17 + 8);
  };

  StaffDayCalendar.prototype.computeHourHeights = function () {
    var self = this;
    var base = this.hourHeight;
    var basePxPerMin = base / 60;
    var heights = {};
    for (var h = this.slotMin; h < this.slotMax; h++) {
      heights[h] = base;
    }

    var day = this.currentDate;
    var rangeStart = this.slotMin * 60;
    var rangeEnd = this.slotMax * 60;

    (this.events || []).forEach(function (ev) {
      var start = parseDate(ev.start);
      var end = parseDate(ev.end) || start;
      if (!start || !sameDay(start, day)) return;

      var startMin = self.minutesFromDayStart(start);
      var endMin = self.minutesFromDayStart(end);
      if (endMin <= startMin) endMin = startMin + 15;

      var visStart = Math.max(startMin, rangeStart);
      var visEnd = Math.min(endMin, rangeEnd);
      if (visEnd <= visStart) return;

      var durationMin = visEnd - visStart;
      var props = ev.extendedProps || {};
      var isBreak = props.calendar === 'Personal';
      var contentH = self.estimateEventContentHeight(props, isBreak);

      // Keep duration proportions visible (15 vs 35 vs 50),
      // while ensuring text still fits inside the shortest boxes.
      var reqPxPerMin = Math.max(basePxPerMin, contentH / durationMin);

      for (var hour = Math.floor(visStart / 60); hour < Math.ceil(visEnd / 60); hour++) {
        if (hour < self.slotMin || hour >= self.slotMax) continue;
        var hourStart = hour * 60;
        var hourEnd = (hour + 1) * 60;
        if (visEnd <= hourStart || visStart >= hourEnd) continue;
        heights[hour] = Math.max(heights[hour], reqPxPerMin * 60);
      }
    });

    this.hourHeights = heights;
    return heights;
  };

  StaffDayCalendar.prototype.totalGridHeight = function () {
    var total = 0;
    for (var h = this.slotMin; h < this.slotMax; h++) {
      total += this.getHourHeight(h);
    }
    return total;
  };

  StaffDayCalendar.prototype.topForDate = function (date) {
    var mins = this.minutesFromDayStart(date);
    var y = 0;
    for (var h = this.slotMin; h < this.slotMax; h++) {
      var hourH = this.getHourHeight(h);
      var hourStart = h * 60;
      var hourEnd = (h + 1) * 60;
      if (mins >= hourEnd) {
        y += hourH;
        continue;
      }
      if (mins <= hourStart) {
        break;
      }
      y += ((mins - hourStart) / 60) * hourH;
      break;
    }
    return y;
  };

  StaffDayCalendar.prototype.dateFromLocalY = function (localY) {
    var y = Math.max(0, localY);
    var acc = 0;
    for (var h = this.slotMin; h < this.slotMax; h++) {
      var hourH = this.getHourHeight(h);
      if (y <= acc + hourH || h === this.slotMax - 1) {
        var frac = hourH > 0 ? (y - acc) / hourH : 0;
        frac = Math.max(0, Math.min(1, frac));
        var totalMin = h * 60 + frac * 60;
        totalMin = Math.round(totalMin / 15) * 15;
        var clickDate = new Date(this.currentDate);
        clickDate.setHours(Math.floor(totalMin / 60), totalMin % 60, 0, 0);
        return clickDate;
      }
      acc += hourH;
    }
    var fallback = new Date(this.currentDate);
    fallback.setHours(this.slotMin, 0, 0, 0);
    return fallback;
  };

  StaffDayCalendar.prototype.buildHourLinesHtml = function (gridH) {
    var html = '<div class="bc-sc-hour-lines" style="height:' + gridH + 'px">';
    var y = 0;
    for (var h = this.slotMin; h < this.slotMax; h++) {
      var hourH = this.getHourHeight(h);
      html +=
        '<div class="bc-sc-hour-line is-half" style="top:' +
        (y + hourH / 2) +
        'px"></div>';
      y += hourH;
      html += '<div class="bc-sc-hour-line" style="top:' + y + 'px"></div>';
    }
    html += '</div>';
    return html;
  };

  StaffDayCalendar.prototype.placeEvents = function () {
    var self = this;
    var day = this.currentDate;
    var cols = {};
    this.$bodyRow.querySelectorAll('.bc-sc-col').forEach(function (col) {
      cols[col.getAttribute('data-staff-id')] = col;
      col.querySelectorAll('.bc-sc-event').forEach(function (n) {
        n.remove();
      });
    });

    this.events.forEach(function (ev) {
      var start = parseDate(ev.start);
      var end = parseDate(ev.end) || start;
      if (!start || !sameDay(start, day)) return;

      var staffId = String(
        ev.resourceId ||
          (ev.extendedProps && ev.extendedProps.staffId) ||
          ''
      );
      var col = cols[staffId];
      if (!col) return;

      var top = self.topForDate(start);
      var bottom = self.topForDate(end);
      var isBreak =
        ev.extendedProps && ev.extendedProps.calendar === 'Personal';
      var props = ev.extendedProps || {};
      var name = props.appointment_name || ev.title || 'Appointment';
      var assignedStaff = props.assigned_staff_name || '';
      var durationMin = props.duration || Math.max(1, Math.round((end - start) / 60000));
      var serviceNames = self.normalizeServiceNames(props);
      var contentH = self.estimateEventContentHeight(props, isBreak);
      // Height follows appointment duration (15/35/50 look different),
      // with a content minimum so text never clips.
      var durationH = Math.max(18, bottom - top);
      var height = Math.max(durationH, contentH);

      var staffMeta =
        props.staffName &&
        String(props.staffName).toLowerCase().indexOf('first available') === 0 &&
        assignedStaff
          ? 'Assigned: ' + assignedStaff
          : '';
      var timeLabel = formatRange(start, end);
      if (durationMin > 0) {
        timeLabel += ' (' + durationMin + ' min)';
      }

      var node = document.createElement('div');
      node.className = 'bc-sc-event ' + (isBreak ? 'is-break' : 'is-appt');
      node.style.top = top + 'px';
      node.style.height = height + 'px';
      node.style.minHeight = height + 'px';
      node.innerHTML =
        '<div class="t">' +
        escapeHtml(timeLabel) +
        '</div>' +
        (serviceNames
          ? '<div class="sv" title="' +
            escapeHtml(serviceNames) +
            '">' +
            escapeHtml(serviceNames) +
            '</div>'
          : '') +
        '<div class="n" title="' +
        escapeHtml(name) +
        '">' +
        escapeHtml(name) +
        '</div>' +
        (staffMeta ? '<div class="s">' + escapeHtml(staffMeta) + '</div>' : '');
      node.addEventListener('click', function (e) {
        e.stopPropagation();
        if (typeof self.options.onEventClick === 'function') {
          self.options.onEventClick({
            event: {
              id: ev.id,
              title: ev.title,
              start: start,
              end: end,
              extendedProps: props,
              url: ev.url || null,
            },
            jsEvent: e,
          });
        }
      });
      col.appendChild(node);
    });
  };

  StaffDayCalendar.prototype.getNow = function () {
    return nowInTimezone(this.timezone);
  };

  StaffDayCalendar.prototype.updateNowLine = function () {
    var nowEl = this.$bodyRow.querySelector('.bc-sc-now');
    if (!nowEl) return;
    var now = this.getNow();
    if (!sameDay(now, this.currentDate)) {
      nowEl.hidden = true;
      return;
    }
    var mins = this.minutesFromDayStart(now);
    if (mins < this.slotMin * 60 || mins > this.slotMax * 60) {
      nowEl.hidden = true;
      return;
    }
    var top = this.topForDate(now);
    nowEl.hidden = false;
    nowEl.style.top = top + 'px';
    var badge = nowEl.querySelector('.bc-sc-now-badge');
    if (badge) badge.textContent = formatClock(now);
  };

  StaffDayCalendar.prototype.scrollToNowOrBusiness = function () {
    var self = this;
    setTimeout(function () {
      var now = self.getNow();
      var top = 0;
      if (sameDay(now, self.currentDate)) {
        top = Math.max(0, self.topForDate(now) - 80);
      } else {
        top = Math.max(0, self.topForDate(new Date(self.currentDate.getFullYear(), self.currentDate.getMonth(), self.currentDate.getDate(), self.slotMin, 0, 0)) - 40);
      }
      self.$bodyScroll.scrollTop = top;
    }, 50);
  };

  StaffDayCalendar.prototype.refetchEvents = function () {
    var self = this;
    var day = this.currentDate;
    var start = new Date(day);
    start.setHours(0, 0, 0, 0);
    var end = new Date(day);
    end.setHours(23, 59, 59, 999);

    var info = {
      start: start,
      end: end,
      startStr: start.toISOString(),
      endStr: end.toISOString(),
    };

    function success(events) {
      self.events = Array.isArray(events) ? events : [];
      // Rebuild grid so hour rows expand only where appointments need space.
      self.render();
      self.refetchAvailability();
    }

    function failure() {
      self.events = [];
      self.render();
      self.refetchAvailability();
    }

    if (typeof this.options.fetchEvents === 'function') {
      this.options.fetchEvents(info, success, failure);
    }
  };

  StaffDayCalendar.prototype.topForTime = function (minutesFromMidnight) {
    var y = 0;
    for (var h = this.slotMin; h < this.slotMax; h++) {
      var hourH = this.getHourHeight(h);
      var hourStart = h * 60;
      var hourEnd = (h + 1) * 60;
      if (minutesFromMidnight >= hourEnd) {
        y += hourH;
        continue;
      }
      if (minutesFromMidnight <= hourStart) {
        break;
      }
      y += ((minutesFromMidnight - hourStart) / 60) * hourH;
      break;
    }
    return y;
  };

  StaffDayCalendar.prototype.setTimeRange = function (openStr, closeStr) {
    if (!openStr || !closeStr) return;
    var op = openStr.split(':');
    var cp = closeStr.split(':');
    var newMin = parseInt(op[0], 10);
    var newMax = parseInt(cp[0], 10);
    if (cp[1] && parseInt(cp[1], 10) > 0) newMax += 1;
    if (newMin < 0 || newMax > 24 || newMin >= newMax) return;
    if (newMin !== this.slotMin || newMax !== this.slotMax) {
      this.slotMin = newMin;
      this.slotMax = newMax;
      this.render();
    }
  };

  StaffDayCalendar.prototype.isStaffUnavailableAt = function (staffId, date) {
    if (!this._availability || typeof this._availability !== 'object') return false;
    var staffData = this._availability.staff || this._availability;
    var data = staffData[String(staffId)];
    if (!data) return false;
    if (data.is_day_off) return true;

    var mins = date.getHours() * 60 + date.getMinutes();

    function toMin(hm) {
      var p = hm.split(':');
      return parseInt(p[0], 10) * 60 + parseInt(p[1], 10);
    }

    var inWorking = (data.working_hours || []).some(function (wh) {
      return mins >= toMin(wh.start) && mins < toMin(wh.end);
    });
    if (!inWorking) return true;

    var inBreak = (data.breaks || []).some(function (brk) {
      return mins >= toMin(brk.start) && mins < toMin(brk.end);
    });
    return inBreak;
  };

  StaffDayCalendar.prototype.getBusyRangesForStaff = function (staffId, breaks) {
    var day = this.currentDate;
    var busy = [];
    (this.events || []).forEach(function (ev) {
      var start = parseDate(ev.start);
      var end = parseDate(ev.end) || start;
      if (!start || !sameDay(start, day)) return;
      var evStaff = String(
        ev.resourceId ||
          (ev.extendedProps && ev.extendedProps.staffId) ||
          ''
      );
      if (!evStaff || evStaff !== String(staffId)) return;
      var s = start.getHours() * 60 + start.getMinutes();
      var e = end.getHours() * 60 + end.getMinutes();
      if (e <= s) e = s + 15;
      busy.push([s, e]);
    });
    (breaks || []).forEach(function (brk) {
      var bs = brk.start.split(':');
      var be = brk.end.split(':');
      busy.push([
        parseInt(bs[0], 10) * 60 + parseInt(bs[1], 10),
        parseInt(be[0], 10) * 60 + parseInt(be[1], 10),
      ]);
    });
    busy.sort(function (a, b) {
      return a[0] - b[0];
    });
    var merged = [];
    busy.forEach(function (r) {
      if (r[1] <= r[0]) return;
      var last = merged[merged.length - 1];
      if (last && r[0] <= last[1]) {
        if (r[1] > last[1]) last[1] = r[1];
      } else {
        merged.push([r[0], r[1]]);
      }
    });
    return merged;
  };

  StaffDayCalendar.prototype.appendUnavailable = function (col, startMin, endMin) {
    var top = this.topForTime(startMin);
    var bot = this.topForTime(endMin);
    if (bot - top < 1) return;
    var el = document.createElement('div');
    el.className = 'bc-sc-avail-overlay bc-sc-avail-unavailable';
    el.style.top = top + 'px';
    el.style.height = bot - top + 'px';
    el.innerHTML = '<span>Not Available</span>';
    col.appendChild(el);
  };

  StaffDayCalendar.prototype.paintAvailability = function (availability) {
    var self = this;
    this.$bodyRow.querySelectorAll('.bc-sc-avail-overlay').forEach(function (el) {
      el.remove();
    });
    if (!availability || typeof availability !== 'object') return;

    var staffData = availability.staff || availability;
    var cols = this.$bodyRow.querySelectorAll('.bc-sc-col');
    cols.forEach(function (col) {
      var staffId = col.getAttribute('data-staff-id');
      var data = staffData[staffId];
      if (!data) return;

      var slotStart = self.slotMin * 60;
      var slotEnd = self.slotMax * 60;

      var workingHours = (data.working_hours || []).map(function (wh) {
        var ws = wh.start.split(':');
        var we = wh.end.split(':');
        return {
          start: parseInt(ws[0], 10) * 60 + parseInt(ws[1], 10),
          end: parseInt(we[0], 10) * 60 + parseInt(we[1], 10),
        };
      }).sort(function (a, b) {
        return a.start - b.start;
      });

      // Day off or no working hours: whole column unavailable
      if (data.is_day_off || !workingHours.length) {
        self.appendUnavailable(col, slotStart, slotEnd);
        return;
      }

      // Booked time (appointments + breaks) to subtract from availability
      var busy = self.getBusyRangesForStaff(staffId, data.breaks);

      // Free intervals = working hours minus booked time
      var free = [];
      workingHours.forEach(function (wh) {
        var ws = Math.max(wh.start, slotStart);
        var we = Math.min(wh.end, slotEnd);
        if (we <= ws) return;
        var cursor = ws;
        busy.forEach(function (b) {
          var bs = Math.max(b[0], ws);
          var be = Math.min(b[1], we);
          if (be <= bs) return;
          if (bs > cursor) free.push([cursor, bs]);
          if (be > cursor) cursor = be;
        });
        if (cursor < we) free.push([cursor, we]);
      });

      // Split free time into 30-minute boxes aligned to clock :00/:30 boundaries
      free.forEach(function (fr) {
        var t = fr[0];
        while (t < fr[1]) {
          var nextBoundary = Math.ceil((t + 1) / 30) * 30;
          var boxEnd = Math.min(nextBoundary, fr[1]);
          if (boxEnd <= t) boxEnd = fr[1];
          var sTop = self.topForTime(t);
          var sBot = self.topForTime(boxEnd);
          var sEl = document.createElement('div');
          sEl.className = 'bc-sc-avail-overlay bc-sc-avail-slot';
          sEl.style.top = sTop + 'px';
          sEl.style.height = Math.max(1, sBot - sTop) + 'px';
          if (sBot - sTop >= 30) {
            sEl.innerHTML =
              '<span class="bc-sc-avail-slot-time">' +
              fmtMinutes(t) +
              ' - ' +
              fmtMinutes(boxEnd) +
              '</span>' +
              '<span class="bc-sc-avail-slot-text">Available</span>';
          }
          col.appendChild(sEl);
          t = boxEnd;
        }
      });

      (data.breaks || []).forEach(function (brk) {
        var bs = brk.start.split(':');
        var be = brk.end.split(':');
        var brkStart = parseInt(bs[0], 10) * 60 + parseInt(bs[1], 10);
        var brkEnd = parseInt(be[0], 10) * 60 + parseInt(be[1], 10);
        brkStart = Math.max(brkStart, slotStart);
        brkEnd = Math.min(brkEnd, slotEnd);
        if (brkEnd <= brkStart) return;
        var rTop = self.topForTime(brkStart);
        var rBot = self.topForTime(brkEnd);
        var rEl = document.createElement('div');
        rEl.className = 'bc-sc-avail-overlay bc-sc-avail-break';
        rEl.style.top = rTop + 'px';
        rEl.style.height = Math.max(1, rBot - rTop) + 'px';
        col.appendChild(rEl);
      });

      // Outside working hours: white blocks (no borders) with "Not Available"
      var cursor = slotStart;
      workingHours.forEach(function (wh) {
        var ws = Math.max(wh.start, slotStart);
        var we = Math.min(wh.end, slotEnd);
        if (we <= ws) return;
        if (ws > cursor) self.appendUnavailable(col, cursor, ws);
        cursor = Math.max(cursor, we);
      });
      if (cursor < slotEnd) self.appendUnavailable(col, cursor, slotEnd);
    });
  };

  StaffDayCalendar.prototype.refetchAvailability = function () {
    var self = this;
    if (typeof this.options.fetchAvailability !== 'function') return;
    var y = this.currentDate.getFullYear();
    var m = pad(this.currentDate.getMonth() + 1);
    var d = pad(this.currentDate.getDate());
    var dateStr = y + '-' + m + '-' + d;

    this.options.fetchAvailability(
      { date: dateStr },
      function (data) {
        self._availability = data || {};
        if (data.shop_open && data.shop_close) {
          self.setTimeRange(data.shop_open, data.shop_close);
        }
        self.paintAvailability(self._availability);
      },
      function () {
        self._availability = {};
        self.paintAvailability({});
      }
    );
  };

  window.StaffDayCalendar = StaffDayCalendar;
})(window, window.jQuery);
