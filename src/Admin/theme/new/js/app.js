!function (e, t) {
    "object" == typeof exports && "object" == typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define([], t) : "object" == typeof exports ? exports.datedreamer = t() : e.datedreamer = t()
}(this, () => {
    return n = {
        484: function (e) {
            e.exports = function () {
                "use strict";

                function r(e, t, n) {
                    var r = String(e);
                    return !r || r.length >= t ? e : "" + Array(t + 1 - r.length).join(n) + e
                }

                function n(e) {
                    return e instanceof x
                }

                function o(e, t, n) {
                    var r;
                    if (!e) return c;
                    if ("string" == typeof e) {
                        var i = e.toLowerCase(), t = (d[i] && (r = i), t && (d[i] = t, r = i), e.split("-"));
                        if (!r && 1 < t.length) return o(t[0])
                    } else {
                        i = e.name;
                        d[i] = e, r = i
                    }
                    return !n && r && (c = r), r || !n && c
                }

                function a(e, t) {
                    return n(e) ? e.clone() : ((t = "object" == typeof t ? t : {}).date = e, t.args = arguments, new x(t))
                }

                var i = "millisecond", u = "second", p = "minute", h = "hour", f = "day", m = "week", v = "month",
                    s = "quarter", g = "year", y = "date", b = "Invalid Date",
                    l = /^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[Tt\s]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/,
                    _ = /\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g, e = {
                        name: "en",
                        weekdays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
                        months: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
                        ordinal: function (e) {
                            var t = ["th", "st", "nd", "rd"], n = e % 100;
                            return "[" + e + (t[(n - 20) % 10] || t[n] || t[0]) + "]"
                        }
                    }, t = {
                        s: r, z: function (e) {
                            var e = -e.utcOffset(), t = Math.abs(e), n = Math.floor(t / 60), t = t % 60;
                            return (e <= 0 ? "+" : "-") + r(n, 2, "0") + ":" + r(t, 2, "0")
                        }, m: function e(t, n) {
                            var r, i, o;
                            return t.date() < n.date() ? -e(n, t) : (r = 12 * (n.year() - t.year()) + (n.month() - t.month()), o = n - (i = t.clone().add(r, v)) < 0, t = t.clone().add(r + (o ? -1 : 1), v), +(-(r + (n - i) / (o ? i - t : t - i)) || 0))
                        }, a: function (e) {
                            return e < 0 ? Math.ceil(e) || 0 : Math.floor(e)
                        }, p: function (e) {
                            return {
                                M: v,
                                y: g,
                                w: m,
                                d: f,
                                D: y,
                                h: h,
                                m: p,
                                s: u,
                                ms: i,
                                Q: s
                            }[e] || String(e || "").toLowerCase().replace(/s$/, "")
                        }, u: function (e) {
                            return void 0 === e
                        }
                    }, c = "en", d = {}, w = (d[c] = e, t), x = (w.l = o, w.i = n, w.w = function (e, t) {
                        return a(e, {locale: t.$L, utc: t.$u, x: t.$x, $offset: t.$offset})
                    }, (e = L.prototype).parse = function (o) {
                        this.$d = function () {
                            var e = o.date, t = o.utc;
                            if (null === e) return new Date(NaN);
                            if (w.u(e)) return new Date;
                            if (!(e instanceof Date || "string" != typeof e || /Z$/i.test(e))) {
                                var n, r, i = e.match(l);
                                if (i) return n = i[2] - 1 || 0, r = (i[7] || "0").substring(0, 3), t ? new Date(Date.UTC(i[1], n, i[3] || 1, i[4] || 0, i[5] || 0, i[6] || 0, r)) : new Date(i[1], n, i[3] || 1, i[4] || 0, i[5] || 0, i[6] || 0, r)
                            }
                            return new Date(e)
                        }(), this.$x = o.x || {}, this.init()
                    }, e.init = function () {
                        var e = this.$d;
                        this.$y = e.getFullYear(), this.$M = e.getMonth(), this.$D = e.getDate(), this.$W = e.getDay(), this.$H = e.getHours(), this.$m = e.getMinutes(), this.$s = e.getSeconds(), this.$ms = e.getMilliseconds()
                    }, e.$utils = function () {
                        return w
                    }, e.isValid = function () {
                        return !(this.$d.toString() === b)
                    }, e.isSame = function (e, t) {
                        e = a(e);
                        return this.startOf(t) <= e && e <= this.endOf(t)
                    }, e.isAfter = function (e, t) {
                        return a(e) < this.startOf(t)
                    }, e.isBefore = function (e, t) {
                        return this.endOf(t) < a(e)
                    }, e.$g = function (e, t, n) {
                        return w.u(e) ? this[t] : this.set(n, e)
                    }, e.unix = function () {
                        return Math.floor(this.valueOf() / 1e3)
                    }, e.valueOf = function () {
                        return this.$d.getTime()
                    }, e.startOf = function (e, t) {
                        function n(e, t) {
                            return t = w.w(i.$u ? Date.UTC(i.$y, t, e) : new Date(i.$y, t, e), i), o ? t : t.endOf(f)
                        }

                        function r(e, t) {
                            return w.w(i.toDate()[e].apply(i.toDate("s"), (o ? [0, 0, 0, 0] : [23, 59, 59, 999]).slice(t)), i)
                        }

                        var i = this, o = !!w.u(t) || t, t = w.p(e), a = this.$W, s = this.$M, l = this.$D,
                            c = "set" + (this.$u ? "UTC" : "");
                        switch (t) {
                            case g:
                                return o ? n(1, 0) : n(31, 11);
                            case v:
                                return o ? n(1, s) : n(0, s + 1);
                            case m:
                                var d = this.$locale().weekStart || 0, d = (a < d ? a + 7 : a) - d;
                                return n(o ? l - d : l + (6 - d), s);
                            case f:
                            case y:
                                return r(c + "Hours", 0);
                            case h:
                                return r(c + "Minutes", 1);
                            case p:
                                return r(c + "Seconds", 2);
                            case u:
                                return r(c + "Milliseconds", 3);
                            default:
                                return this.clone()
                        }
                    }, e.endOf = function (e) {
                        return this.startOf(e, !1)
                    }, e.$set = function (e, t) {
                        var e = w.p(e), n = "set" + (this.$u ? "UTC" : ""),
                            n = ((r = {})[f] = n + "Date", r[y] = n + "Date", r[v] = n + "Month", r[g] = n + "FullYear", r[h] = n + "Hours", r[p] = n + "Minutes", r[u] = n + "Seconds", r[i] = n + "Milliseconds", r[e]),
                            r = e === f ? this.$D + (t - this.$W) : t;
                        return e === v || e === g ? ((t = this.clone().set(y, 1)).$d[n](r), t.init(), this.$d = t.set(y, Math.min(this.$D, t.daysInMonth())).$d) : n && this.$d[n](r), this.init(), this
                    }, e.set = function (e, t) {
                        return this.clone().$set(e, t)
                    }, e.get = function (e) {
                        return this[w.p(e)]()
                    }, e.add = function (n, e) {
                        function t(e) {
                            var t = a(r);
                            return w.w(t.date(t.date() + Math.round(e * n)), r)
                        }

                        var r = this, e = (n = Number(n), w.p(e));
                        return e === v ? this.set(v, this.$M + n) : e === g ? this.set(g, this.$y + n) : e === f ? t(1) : e === m ? t(7) : (e = {
                            minute: 6e4,
                            hour: 36e5,
                            second: 1e3
                        }[e] || 1, e = this.$d.getTime() + n * e, w.w(e, this))
                    }, e.subtract = function (e, t) {
                        return this.add(-1 * e, t)
                    }, e.format = function (e) {
                        var i, n, t, r, o, a, s, l, c, d, u = this, p = this.$locale();
                        return this.isValid() ? (i = e || "YYYY-MM-DDTHH:mm:ssZ", n = w.z(this), t = this.$H, e = this.$m, r = this.$M, o = p.weekdays, a = p.months, s = function (e, t, n, r) {
                            return e && (e[t] || e(u, i)) || n[t].slice(0, r)
                        }, l = function (e) {
                            return w.s(t % 12 || 12, e, "0")
                        }, c = p.meridiem || function (e, t, n) {
                            e = e < 12 ? "AM" : "PM";
                            return n ? e.toLowerCase() : e
                        }, d = {
                            YY: String(this.$y).slice(-2),
                            YYYY: this.$y,
                            M: r + 1,
                            MM: w.s(r + 1, 2, "0"),
                            MMM: s(p.monthsShort, r, a, 3),
                            MMMM: s(a, r),
                            D: this.$D,
                            DD: w.s(this.$D, 2, "0"),
                            d: String(this.$W),
                            dd: s(p.weekdaysMin, this.$W, o, 2),
                            ddd: s(p.weekdaysShort, this.$W, o, 3),
                            dddd: o[this.$W],
                            H: String(t),
                            HH: w.s(t, 2, "0"),
                            h: l(1),
                            hh: l(2),
                            a: c(t, e, !0),
                            A: c(t, e, !1),
                            m: String(e),
                            mm: w.s(e, 2, "0"),
                            s: String(this.$s),
                            ss: w.s(this.$s, 2, "0"),
                            SSS: w.s(this.$ms, 3, "0"),
                            Z: n
                        }, i.replace(_, function (e, t) {
                            return t || d[e] || n.replace(":", "")
                        })) : p.invalidDate || b
                    }, e.utcOffset = function () {
                        return 15 * -Math.round(this.$d.getTimezoneOffset() / 15)
                    }, e.diff = function (e, t, n) {
                        var t = w.p(t), e = a(e), r = 6e4 * (e.utcOffset() - this.utcOffset()), i = this - e,
                            e = w.m(this, e), o = {};
                        return o[g] = e / 12, o[v] = e, o[s] = e / 3, o[m] = (i - r) / 6048e5, o[f] = (i - r) / 864e5, o[h] = i / 36e5, o[p] = i / 6e4, o[u] = i / 1e3, e = o[t] || i, n ? e : w.a(e)
                    }, e.daysInMonth = function () {
                        return this.endOf(v).$D
                    }, e.$locale = function () {
                        return d[this.$L]
                    }, e.locale = function (e, t) {
                        var n;
                        return e ? (n = this.clone(), (e = o(e, t, !0)) && (n.$L = e), n) : this.$L
                    }, e.clone = function () {
                        return w.w(this.$d, this)
                    }, e.toDate = function () {
                        return new Date(this.valueOf())
                    }, e.toJSON = function () {
                        return this.isValid() ? this.toISOString() : null
                    }, e.toISOString = function () {
                        return this.$d.toISOString()
                    }, e.toString = function () {
                        return this.$d.toUTCString()
                    }, L), E = x.prototype;

                function L(e) {
                    this.$L = o(e.locale, null, !0), this.parse(e)
                }

                return a.prototype = E, [["$ms", i], ["$s", u], ["$m", p], ["$H", h], ["$W", f], ["$M", v], ["$y", g], ["$D", y]].forEach(function (t) {
                    E[t[1]] = function (e) {
                        return this.$g(e, t[0], t[1])
                    }
                }), a.extend = function (e, t) {
                    return e.$i || (e(t, x, a), e.$i = !0), a
                }, a.locale = o, a.isDayjs = n, a.unix = function (e) {
                    return a(1e3 * e)
                }, a.en = d[c], a.Ls = d, a.p = {}, a
            }()
        }, 285: function (e) {
            e.exports = function () {
                "use strict";

                function e(t) {
                    return function (e) {
                        this[t] = +e
                    }
                }

                function n(e) {
                    return (e = f[e]) && (e.indexOf ? e : e.s.concat(e.f))
                }

                function t(e, t) {
                    var n, r = f.meridiem;
                    if (r) {
                        for (var i = 1; i <= 24; i += 1) if (-1 < e.indexOf(r(i, 0, t))) {
                            n = 12 < i;
                            break
                        }
                    } else n = e === (t ? "pm" : "PM");
                    return n
                }

                var s = {
                        LTS: "h:mm:ss A",
                        LT: "h:mm A",
                        L: "MM/DD/YYYY",
                        LL: "MMMM D, YYYY",
                        LLL: "MMMM D, YYYY h:mm A",
                        LLLL: "dddd, MMMM D, YYYY h:mm A"
                    }, l = /(\[[^[]*\])|([-_:/.,()\s]+)|(A|a|YYYY|YY?|MM?M?M?|Do|DD?|hh?|HH?|mm?|ss?|S{1,3}|z|ZZ?)/g,
                    r = /\d\d/, i = /\d\d?/, o = /\d*[^-_:/,()\s\d]+/, f = {}, a = function (e) {
                        return (e = +e) + (68 < e ? 1900 : 2e3)
                    }, c = [/[+-]\d\d:?(\d\d)?|Z/, function (e) {
                        var t;
                        (this.zone || (this.zone = {})).offset = !e || "Z" === e || 0 === (t = 60 * (e = e.match(/([+-]|\d\d)/g))[1] + (+e[2] || 0)) ? 0 : "+" === e[0] ? -t : t
                    }], p = {
                        A: [o, function (e) {
                            this.afternoon = t(e, !1)
                        }],
                        a: [o, function (e) {
                            this.afternoon = t(e, !0)
                        }],
                        S: [/\d/, function (e) {
                            this.milliseconds = 100 * +e
                        }],
                        SS: [r, function (e) {
                            this.milliseconds = 10 * +e
                        }],
                        SSS: [/\d{3}/, function (e) {
                            this.milliseconds = +e
                        }],
                        s: [i, e("seconds")],
                        ss: [i, e("seconds")],
                        m: [i, e("minutes")],
                        mm: [i, e("minutes")],
                        H: [i, e("hours")],
                        h: [i, e("hours")],
                        HH: [i, e("hours")],
                        hh: [i, e("hours")],
                        D: [i, e("day")],
                        DD: [r, e("day")],
                        Do: [o, function (e) {
                            var t = f.ordinal, n = e.match(/\d+/);
                            if (this.day = n[0], t) for (var r = 1; r <= 31; r += 1) t(r).replace(/\[|\]/g, "") === e && (this.day = r)
                        }],
                        M: [i, e("month")],
                        MM: [r, e("month")],
                        MMM: [o, function (e) {
                            var t = n("months"), t = (n("monthsShort") || t.map(function (e) {
                                return e.slice(0, 3)
                            })).indexOf(e) + 1;
                            if (t < 1) throw new Error;
                            this.month = t % 12 || t
                        }],
                        MMMM: [o, function (e) {
                            e = n("months").indexOf(e) + 1;
                            if (e < 1) throw new Error;
                            this.month = e % 12 || e
                        }],
                        Y: [/[+-]?\d+/, e("year")],
                        YY: [r, function (e) {
                            this.year = a(e)
                        }],
                        YYYY: [/\d{4}/, e("year")],
                        Z: c,
                        ZZ: c
                    };

                function _(e) {
                    for (var t = e, i = f && f.formats, d = (e = t.replace(/(\[[^\]]+])|(LTS?|l{1,4}|L{1,4})/g, function (e, t, n) {
                        var r = n && n.toUpperCase();
                        return t || i[n] || s[n] || i[r].replace(/(\[[^\]]+])|(MMMM|MM|DD|dddd)/g, function (e, t, n) {
                            return t || n.slice(1)
                        })
                    })).match(l), u = d.length, n = 0; n < u; n += 1) {
                        var r = d[n], o = p[r], a = o && o[0], o = o && o[1];
                        d[n] = o ? {regex: a, parser: o} : r.replace(/^\[|\]$/g, "")
                    }
                    return function (e) {
                        for (var t, n, r, i = {}, o = 0, a = 0; o < u; o += 1) {
                            var s, l, c = d[o];
                            "string" == typeof c ? a += c.length : (l = c.regex, c = c.parser, s = e.slice(a), l = l.exec(s)[0], c.call(i, l), e = e.replace(l, ""))
                        }
                        return void 0 !== (r = (t = i).afternoon) && (n = t.hours, r ? n < 12 && (t.hours += 12) : 12 === n && (t.hours = 0), delete t.afternoon), i
                    }
                }

                return function (e, t, p) {
                    p.p.customParseFormat = !0, e && e.parseTwoDigitYear && (a = e.parseTwoDigitYear);
                    var e = t.prototype, h = e.parse;
                    e.parse = function (e) {
                        var t = e.date, n = e.utc, r = e.args, i = (this.$u = n, r[1]);
                        if ("string" == typeof i) {
                            var o = !0 === r[2], a = !0 === r[3], s = o || a, l = r[2];
                            a && (l = r[2]), f = this.$locale(), !o && l && (f = p.Ls[l]), this.$d = function (e, t, n) {
                                try {
                                    var r, i, o, a, s, l, c, d, u, p, h, f, m, v, g, y, b;
                                    return -1 < ["x", "X"].indexOf(t) ? new Date(("X" === t ? 1e3 : 1) * e) : (i = (r = _(t)(e)).year, o = r.month, a = r.day, s = r.hours, l = r.minutes, c = r.seconds, d = r.milliseconds, u = r.zone, p = new Date, h = a || (i || o ? 1 : p.getDate()), f = i || p.getFullYear(), m = 0, i && !o || (m = 0 < o ? o - 1 : p.getMonth()), v = s || 0, g = l || 0, y = c || 0, b = d || 0, u ? new Date(Date.UTC(f, m, h, v, g, y, b + 60 * u.offset * 1e3)) : n ? new Date(Date.UTC(f, m, h, v, g, y, b)) : new Date(f, m, h, v, g, y, b))
                                } catch (e) {
                                    return new Date("")
                                }
                            }(t, i, n), this.init(), l && !0 !== l && (this.$L = this.locale(l).$L), s && t != this.format(i) && (this.$d = new Date("")), f = {}
                        } else if (i instanceof Array) for (var c = i.length, d = 1; d <= c; d += 1) {
                            r[1] = i[d - 1];
                            var u = p.apply(this, r);
                            if (u.isValid()) {
                                this.$d = u.$d, this.$L = u.$L, this.init();
                                break
                            }
                            d === c && (this.$d = new Date(""))
                        } else h.call(this, e)
                    }
                }
            }()
        }, 933: function (e, t, n) {
            "use strict";
            var r = this && this.__importDefault || function (e) {
                return e && e.__esModule ? e : {default: e}
            };
            Object.defineProperty(t, "__esModule", {value: !0}), t.calendarToggle = void 0;
            const i = n(255), o = n(256), a = r(n(484)), s = r(n(285));
            a.default.extend(s.default);

            class l extends HTMLElement {
                constructor(e) {
                    super(), this.inputPlaceholder = "Enter a date", this.options = e, this.element = e.element, this.attachShadow({mode: "open"}), this.init()
                }

                init() {
                    null != this.element ? (this.generateTemplate(), document.addEventListener("click", e => {
                        this === e.target || this.contains(e.target) || null == (e = this.calendarWrapElement) || e.classList.remove("active")
                    })) : console.error("No element was provided to calendar. Initializing aborted")
                }

                generateTemplate() {
                    let e;
                    e = ("string" == typeof this.options.selectedDate || "object" == typeof this.options.selectedDate ? (0, a.default)(this.options.selectedDate, this.options.format) : (0, a.default)()).format(this.options.format);
                    var t = (0, o.calendarToggleRoot)(this.options.theme, this.options.styles, this.inputPlaceholder, e);
                    let n;
                    if ("string" == typeof this.element ? n = document.querySelector(this.element) : "object" == typeof this.element && (n = this.element), n) {
                        this.shadowRoot && (this.shadowRoot.innerHTML = t), n.append(this);
                        const e = null == (t = this.shadowRoot) ? void 0 : t.querySelector(".datedreamer__calendar-toggle__calendar"),
                            o = null == (t = this.shadowRoot) ? void 0 : t.querySelector("#date-input");
                        e && (this.calendarWrapElement = e), o && (this.inputElement = o, this.inputElement.addEventListener("focus", () => {
                            var e;
                            null != (e = this.calendarWrapElement) && e.classList.add("active")
                        })), this.generateCalendar()
                    } else console.error(`Could not find ${this.element} in DOM.`)
                }

                generateCalendar() {
                    var e = new i.calendar(Object.assign(Object.assign({}, this.options), {
                        element: this.calendarWrapElement || "",
                        hideInputs: !0,
                        onChange: e => this.dateChangedHandler(e)
                    }));
                    this.calendarElement = e
                }

                dateChangedHandler(e) {
                    var t;
                    this.inputElement.value = e.detail, null != (t = this.calendarWrapElement) && t.classList.remove("active"), this.options.onChange && this.options.onChange(e)
                }
            }

            t.calendarToggle = l, customElements.define("datedreamer-calendar-toggle", l)
        }, 255: function (e, t, n) {
            "use strict";
            var r = this && this.__importDefault || function (e) {
                return e && e.__esModule ? e : {default: e}
            };
            Object.defineProperty(t, "__esModule", {value: !0}), t.calendar = void 0;
            const f = n(256), i = r(n(484)), o = r(n(285));
            i.default.extend(o.default);

            class a extends HTMLElement {
                constructor(e) {
                    super(), this.calendarElement = null, this.headerElement = null, this.inputsElement = null, this.errorsElement = null, this.inputLabel = "Set a date", this.inputPlaceholder = "Enter a date", this.hideInputs = !1, this.darkMode = !1, this.hideOtherMonthDays = !1, this.errors = [], this.daysElement = null, this.selectedDate = new Date, this.displayedMonthDate = new Date, this.theme = "unstyled", this.styles = "", this.goToPrevMonth = e => {
                        this.displayedMonthDate.setMonth(this.displayedMonthDate.getMonth() - 1), this.rebuildCalendar(), this.onPrevNav && this.onPrevNav(new CustomEvent("prevNav", {detail: this.displayedMonthDate}))
                    }, this.goToNextMonth = e => {
                        this.displayedMonthDate.setMonth(this.displayedMonthDate.getMonth() + 1), this.rebuildCalendar(), this.onNextNav && this.onNextNav(new CustomEvent("prevNav", {detail: this.displayedMonthDate}))
                    }, this.setSelectedDay = e => {
                        const t = new Date(this.displayedMonthDate);
                        if (t.setDate(e), this.rangeMode) {
                            if (this.connector) {
                                if (null !== this.connector.startDate && null !== this.connector.endDate && (this.connector.startDate = null, this.connector.endDate = null, this.connector.rebuildAllCalendars()), null == this.connector.startDate ? this.connector.startDate = new Date(t) : null == this.connector.endDate && (this.connector.endDate = new Date(t)), null !== this.connector.startDate && null !== this.connector.endDate) {
                                    if (this.connector.startDate > this.connector.endDate) {
                                        console.log("start date is larger than end date");
                                        const e = new Date(this.connector.endDate),
                                            t = new Date(this.connector.startDate);
                                        this.connector.startDate = e, this.connector.endDate = t, console.log(this.connector.startDate, this.connector.endDate)
                                    }
                                    this.connector.dateChangedCallback && this.connector.dateChangedCallback(new CustomEvent("dateChanged"))
                                }
                                this.connector.rebuildAllCalendars()
                            }
                        } else this.selectedDate = new Date(t), this.rebuildCalendar(), this.dateChangedCallback(this.selectedDate)
                    }, this.element = e.element, e.format && (this.format = e.format), e.theme && (this.theme = e.theme), e.styles && (this.styles = e.styles), e.iconNext && (this.iconNext = e.iconNext), e.iconPrev && (this.iconPrev = e.iconPrev), e.inputLabel && (this.inputLabel = e.inputLabel), e.inputPlaceholder && (this.inputPlaceholder = e.inputPlaceholder), e.hidePrevNav && (this.hidePrevNav = e.hidePrevNav), e.hideNextNav && (this.hideNextNav = e.hideNextNav), e.hideInputs && (this.hideInputs = e.hideInputs), e.darkMode && (this.darkMode = e.darkMode), e.rangeMode && (this.rangeMode = e.rangeMode), e.connector && (this.connector = e.connector, this.connector.calendars.push(this)), e.hideOtherMonthDays && (this.hideOtherMonthDays = e.hideOtherMonthDays), "string" == typeof e.selectedDate ? this.selectedDate = (0, i.default)(e.selectedDate, e.format).toDate() : "object" == typeof e.selectedDate && (this.selectedDate = e.selectedDate), this.attachShadow({mode: "open"}), this.onChange = e.onChange, this.onRender = e.onRender, this.onNextNav = e.onNextNav, this.onPrevNav = e.onPrevNav, this.displayedMonthDate = new Date(this.selectedDate), this.init()
                }

                init() {
                    var e;
                    null == this.element ? console.error("No element was provided to calendar. Initializing aborted") : (e = (0, f.calendarRoot)(this.theme, this.styles, this.darkMode), this.insertCalendarIntoSelector(e), this.headerElement = null == (e = this.shadowRoot) ? void 0 : e.querySelector(".datedreamer__calendar_header"), this.daysElement = null == (e = this.shadowRoot) ? void 0 : e.querySelector(".datedreamer__calendar_days"), this.inputsElement = null == (e = this.shadowRoot) ? void 0 : e.querySelector(".datedreamer__calendar_inputs"), this.errorsElement = null == (e = this.shadowRoot) ? void 0 : e.querySelector(".datedreamer__calendar_errors"), this.generateHeader(), this.generateInputs(), this.generateDays(), this.onRenderCallback())
                }

                insertCalendarIntoSelector(e) {
                    let t;
                    "string" == typeof this.element ? t = document.querySelector(this.element) : "object" == typeof this.element && (t = this.element), t ? (this.shadowRoot && (this.shadowRoot.innerHTML = e), t.append(this)) : console.error(`Could not find ${this.element} in DOM.`)
                }

                generateHeader() {
                    var e;
                    if (!this.hidePrevNav) {
                        const e = document.createElement("button");
                        e.classList.add("datedreamer__calendar_prev"), e.innerHTML = this.iconPrev || f.leftChevron, e.setAttribute("aria-label", "Previous"), e.addEventListener("click", this.goToPrevMonth), null != (t = this.headerElement) && t.append(e)
                    }
                    var t = document.createElement("span");
                    if (t.classList.add("datedreamer__calendar_title"), t.innerText = f.monthNames[this.displayedMonthDate.getMonth()] + " " + this.displayedMonthDate.getFullYear(), null != (e = this.headerElement) && e.append(t), !this.hideNextNav) {
                        const t = document.createElement("button");
                        t.classList.add("datedreamer__calendar_next"), t.innerHTML = this.iconNext || f.rightChevron, t.setAttribute("aria-label", "Next"), t.addEventListener("click", this.goToNextMonth), null != (e = this.headerElement) && e.append(t)
                    }
                }

                generateInputs() {
                    var e, t, n, r;
                    this.hideInputs || ((e = document.createElement("label")).setAttribute("for", "date-input"), e.textContent = this.inputLabel, (t = document.createElement("div")).classList.add("datedreamer__calendar__inputs-wrap"), (n = document.createElement("input")).id = "date-input", n.placeholder = this.inputPlaceholder, n.value = (0, i.default)(this.selectedDate).format(this.format), n.addEventListener("keyup", e => this.dateInputChanged(e)), n.setAttribute("title", "Set a date"), (r = document.createElement("button")).innerText = "Today", r.addEventListener("click", () => this.setDateToToday()), t.append(n, r), null != (n = this.inputsElement) && n.append(e, t))
                }

                generateErrors() {
                    var e;
                    const r = null == (e = this.inputsElement) ? void 0 : e.querySelector("input");
                    r && r.classList.remove("error"), this.errorsElement && (this.errorsElement.innerHTML = ""), this.errors.forEach(({
                                                                                                                                          type: e,
                                                                                                                                          message: t
                                                                                                                                      }) => {
                        var n = document.createElement("span");
                        n.innerText = t, "input-error" == e && r && r.classList.add("error"), null != (t = this.errorsElement) && t.append(n)
                    }), this.errors = []
                }

                generateDays() {
                    var t, n, r, i, o, a;
                    const s = this.selectedDate.getDate(), l = this.displayedMonthDate.getMonth(),
                        c = this.displayedMonthDate.getFullYear(), d = new Date(c, l + 1, 0).getDate(),
                        e = new Date(c, l, 1), u = new Date(c, l, d),
                        p = f.weekdays.indexOf(e.toString().split(" ")[0]),
                        h = 6 - f.weekdays.indexOf(u.toString().split(" ")[0]);
                    for (let e = 1; e <= p + d + h; e++) if (e > p && e <= p + d) {
                        const o = document.createElement("div"),
                            a = (o.classList.add("datedreamer__calendar_day"), document.createElement("button"));
                        if (a.addEventListener("click", () => this.setSelectedDay(e - p)), a.innerText = (e - p).toString(), a.setAttribute("type", "button"), this.rangeMode) {
                            this.displayedMonthDate.getMonth() == (null == (t = null == (r = this.connector) ? void 0 : r.startDate) ? void 0 : t.getMonth()) && this.displayedMonthDate.getFullYear() == this.connector.startDate.getFullYear() && e - p == this.connector.startDate.getDate() && o.classList.add("active"), this.displayedMonthDate.getMonth() == (null == (r = null == (n = this.connector) ? void 0 : n.endDate) ? void 0 : r.getMonth()) && this.displayedMonthDate.getFullYear() == this.connector.endDate.getFullYear() && e - p == this.connector.endDate.getDate() && o.classList.add("active");
                            const i = new Date(this.displayedMonthDate);
                            i.setDate(e - p), null != (t = this.connector) && t.startDate && this.connector.endDate && (null == (n = this.connector) ? void 0 : n.startDate) < i && (null == (r = this.connector) ? void 0 : r.endDate) > i && o.classList.add("highlight")
                        } else e == p + s && this.displayedMonthDate.getMonth() == this.selectedDate.getMonth() && this.displayedMonthDate.getFullYear() == this.selectedDate.getFullYear() && o.classList.add("active");
                        o.append(a), null != (i = this.daysElement) && i.append(o)
                    } else if (e <= p) {
                        const r = document.createElement("div");
                        if (r.classList.add("datedreamer__calendar_day", "disabled"), !this.hideOtherMonthDays) {
                            const t = document.createElement("button");
                            t.innerText = new Date(c, l, 0 - (p - e)).getDate().toString(), t.setAttribute("disabled", "true"), t.setAttribute("type", "button"), r.append(t)
                        }
                        null != (o = this.daysElement) && o.append(r)
                    } else if (e > p + d) {
                        const r = e - (p + h + d) + h, t = document.createElement("div");
                        if (t.classList.add("datedreamer__calendar_day", "disabled"), !this.hideOtherMonthDays) {
                            const n = document.createElement("button");
                            n.innerText = new Date(c, l + 1, r).getDate().toString(), n.setAttribute("disabled", "true"), n.setAttribute("type", "button"), t.append(n)
                        }
                        null != (a = this.daysElement) && a.append(t)
                    }
                }

                rebuildCalendar(e = !0) {
                    this.daysElement && (this.daysElement.innerHTML = ""), this.headerElement && (this.headerElement.innerHTML = ""), this.generateErrors(), this.generateDays(), this.generateHeader(), e && (this.inputsElement && (this.inputsElement.innerHTML = ""), this.generateInputs())
                }

                setDate(e) {
                    "string" == typeof e ? this.selectedDate = new Date(e) : "object" == typeof e && (this.selectedDate = e), this.displayedMonthDate = this.selectedDate, this.rebuildCalendar(), this.dateChangedCallback(this.selectedDate)
                }

                setDateToToday() {
                    this.selectedDate = new Date, this.displayedMonthDate = new Date, this.rebuildCalendar(), this.dateChangedCallback(this.selectedDate)
                }

                dateInputChanged(e) {
                    e = (0, i.default)(e.target.value, this.format).toDate();
                    isNaN(e.getUTCMilliseconds()) ? (this.errors.push({
                        type: "input-error",
                        message: "The entered date is invalid"
                    }), this.generateErrors()) : (this.selectedDate = e, this.displayedMonthDate = new Date(e), this.rebuildCalendar(!1), this.dateChangedCallback(this.selectedDate))
                }

                dateChangedCallback(e) {
                    this.onChange && (e = new CustomEvent("onChange", {detail: (0, i.default)(e).format(this.format)}), this.onChange(e))
                }

                onRenderCallback() {
                    var e;
                    this.onRender && (e = new CustomEvent("onRender", {detail: {calendar: this.calendarElement}}), this.onRender(e))
                }

                setDisplayedMonthDate(e) {
                    this.displayedMonthDate = e, this.rebuildCalendar()
                }
            }

            t.calendar = a, customElements.define("datedreamer-calendar", a)
        }, 406: (e, t) => {
            "use strict";
            Object.defineProperty(t, "__esModule", {value: !0}), t.default = class {
                constructor() {
                    this.calendars = new Array, this.startDate = null, this.endDate = null, this.pickingEndDate = null
                }

                rebuildAllCalendars() {
                    this.calendars.forEach(e => {
                        e.rebuildCalendar()
                    })
                }
            }
        }, 98: function (e, t, n) {
            "use strict";
            var r = this && this.__importDefault || function (e) {
                return e && e.__esModule ? e : {default: e}
            };
            Object.defineProperty(t, "__esModule", {value: !0}), t.range = void 0;
            const i = r(n(484)), o = n(109), a = n(255), s = r(n(406));

            class l extends HTMLElement {
                constructor(e) {
                    super(), this.calendar1DisplayedDate = new Date, this.calendar2DisplayedDate = new Date, this.handleDateChange = e => {
                        var t;
                        if (this.onChange) {
                            const e = new CustomEvent("onChange", {
                                detail: {
                                    startDate: (0, i.default)(null == (t = this.connector) ? void 0 : t.startDate).format(this.format),
                                    endDate: (0, i.default)(null == (t = this.connector) ? void 0 : t.endDate).format(this.format)
                                }
                            });
                            this.onChange(e)
                        }
                    }, this.element = e.element, this.connector = new s.default, this.styles = e.styles, this.format = e.format, this.iconPrev = e.iconPrev, this.iconNext = e.iconNext, this.onChange = e.onChange, this.onRender = e.onRender, this.theme = e.theme, this.darkMode = e.darkMode, this.connector && (this.connector.dateChangedCallback = this.handleDateChange), this.init()
                }

                init() {
                    this.addStyles(), this.calendar1DisplayedDate.setDate(1), this.calendar2DisplayedDate.setDate(1), this.calendar2DisplayedDate.setMonth(this.calendar2DisplayedDate.getMonth() + 1);
                    const e = document.createElement("div");
                    e.classList.add("datedreamer-range"), this.darkMode && e.classList.add("dark");
                    var t = document.createElement("div"), n = document.createElement("div");
                    if (e.append(t, n), this.calendar1 = new a.calendar({
                        element: t,
                        theme: this.theme,
                        format: this.format,
                        hideInputs: !0,
                        hideNextNav: !0,
                        styles: o.calendarStyles,
                        iconPrev: this.iconPrev,
                        onPrevNav: e => this.prevHandler(e),
                        rangeMode: !0,
                        hideOtherMonthDays: !0,
                        connector: this.connector,
                        darkMode: this.darkMode
                    }), this.calendar2 = new a.calendar({
                        element: n,
                        theme: this.theme,
                        format: this.format,
                        hideInputs: !0,
                        hidePrevNav: !0,
                        styles: o.calendarStyles,
                        iconNext: this.iconNext,
                        onNextNav: e => this.nextHandler(e),
                        rangeMode: !0,
                        hideOtherMonthDays: !0,
                        connector: this.connector,
                        darkMode: this.darkMode
                    }), this.calendar2.setDisplayedMonthDate(this.calendar2DisplayedDate), this.append(e), "string" == typeof this.element) {
                        const e = document.querySelector(this.element);
                        e && e.append(this)
                    } else "object" == typeof this.element && this.element.append(this)
                }

                addStyles() {
                    var e = `
            .datedreamer-range {
                display: inline-flex;
                box-shadow: 0 10px 15px -3px rgb(0 0 0 / 10%), 0 4px 6px -4px rgb(0 0 0 / 10%);
            }

            .datedreamer-range.dark {
                background: #2c3e50;
            }
            ${this.styles || ""}
        `, t = document.createElement("style");
                    t.innerHTML = e, this.append(t)
                }

                prevHandler(e) {
                    this.calendar1DisplayedDate = e.detail, this.calendar2DisplayedDate.setMonth(this.calendar2DisplayedDate.getMonth() - 1), this.resetViewedDated()
                }

                nextHandler(e) {
                    this.calendar2DisplayedDate = e.detail, this.calendar1DisplayedDate.setMonth(this.calendar1DisplayedDate.getMonth() + 1), this.resetViewedDated()
                }

                resetViewedDated() {
                    var e;
                    null != (e = this.calendar1) && e.setDisplayedMonthDate(this.calendar1DisplayedDate), null != (e = this.calendar2) && e.setDisplayedMonthDate(this.calendar2DisplayedDate)
                }
            }

            t.range = l, customElements.define("datedreamer-range", l)
        }, 256: (e, r) => {
            "use strict";
            Object.defineProperty(r, "__esModule", {value: !0}), r.litePurple = r.unstyledTheme = r.calendarToggleRoot = r.calendarRoot = r.rightChevron = r.leftChevron = r.weekdays = r.monthNames = void 0, r.monthNames = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"], r.weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], r.leftChevron = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/></svg>', r.rightChevron = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>', r.calendarRoot = function (e, t = "", n) {
                return `
  <style>
      ${r.unstyledTheme}
      ${"lite-purple" == e ? r.litePurple : ""}
      
      ${t}
  </style>
  <div class="datedreamer__calendar ${n ? "dark" : ""}">
      <div class="datedreamer__calendar_header"></div>
  
      <div class="datedreamer__calendar_inputs"></div>
      <div class="datedreamer__calendar_errors"></div>
  
      <div class="datedreamer__calendar_days-wrap">
          <div class="datedreamer__calendar_days-header">
              <div class="datedreamer__calendar_day datedreamer__calendar_day-header">Вс</div>    
              <div class="datedreamer__calendar_day datedreamer__calendar_day-header">Пн</div>
              <div class="datedreamer__calendar_day datedreamer__calendar_day-header">Вт</div>
              <div class="datedreamer__calendar_day datedreamer__calendar_day-header">Ср</div>
              <div class="datedreamer__calendar_day datedreamer__calendar_day-header">Чт</div>
              <div class="datedreamer__calendar_day datedreamer__calendar_day-header">Пт</div>
              <div class="datedreamer__calendar_day datedreamer__calendar_day-header">Сб</div>
          </div>
  
          <div class="datedreamer__calendar_days"></div>
      </div>
  </div>
  `
            }, r.calendarToggleRoot = function (e, t = "", n, r) {
                return `
    <style>
        .datedreamer__calendar-toggle {
            position: relative;
        }
        .datedreamer__calendar-toggle__calendar {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
        }

        .datedreamer__calendar-toggle__calendar.active {
            display: block;
        }

        ${"lite-purple" == e ? "\n        .datedreamer__calendar-toggle__input input {\n            font-weight: 500;\n            border-radius: 4px;\n            border: 1px solid #e9e8ec;\n            font-size: 12px;\n            background: white;\n            display: block;\n            padding: 4px 4px 4px 8px;\n            margin-right: 8px;\n        }\n        " : ""}

        ${t}
    </style>
    <div class="datedreamer__calendar-toggle">
        <div class="datedreamer__calendar-toggle__input">
            <input id="date-input" placeholder="${n}" value="${r}" readonly/>
        </div>

        <div class="datedreamer__calendar-toggle__calendar"></div>
    </div>
  `
            }, r.unstyledTheme = "\n@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');\n\n.datedreamer__calendar {\n    -webkit-font-smoothing: antialiased;\n    -moz-osx-font-smoothing: grayscale;\n    font-family: 'Roboto', sans-serif;\n    width: 100%;\n    max-width: 240px;\n    padding: 14px;\n    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);\n    background: #fff;\n    z-index: 0;\n    position: relative;\n    box-sizing: border-box;\n}\n\n.datedreamer__calendar.dark {\n  background: #2c3e50;\n}\n\n.datedreamer__calendar_header {\n    width: 100%;\n    display: flex;\n    align-items: center;\n}\n\n.datedreamer__calendar_prev,.datedreamer__calendar_next {\n    background: none;\n    border: none;\n    width: 16px;\n    height: 16px;\n    text-align: center;\n    cursor: pointer;\n    display: flex;\n    align-items: center;\n    justify-content: center;\n    color:#2d3436;\n}\n\n.dark .datedreamer__calendar_prev, .dark .datedreamer__calendar_next {\n  color: #fff;\n}\n\n.datedreamer__calendar_prev svg, .datedreamer__calendar_next svg {\n    transform: scale(2.875);\n}\n\n.dark .datedreamer__calendar_prev svg, .dark .datedreamer__calendar_next svg {\n  fill: #fff;\n}\n\n.datedreamer__calendar_title {\n    width: 100%;\n    display: block;\n    flex-grow: 1;\n    text-align: center;\n    color: #2d3436;\n    font-weight: 600;\n    font-size: 0.875rem;\n}\n\n.dark .datedreamer__calendar_title {\n  color: #fff;\n}\n\n.datedreamer__calendar_inputs {\n    margin-top: 12px;\n}\n\n.datedreamer__calendar_inputs label {\n  width: 100%;\n}\n\n.dark .datedreamer__calendar_inputs label {\n  color: #fff;\n}\n\n.datedreamer__calendar__inputs-wrap {\n  display: flex;\n}\n\n.datedreamer__calendar_inputs input {\n  width: 100%;\n}\n\n.datedreamer__calendar_inputs input.error {\n   border: 2px solid #d63031;\n}\n\n.datedreamer__calendar_errors {\n  margin: 8px 0;\n  color: #d63031;\n}\n\n.datedreamer__calendar_days, .datedreamer__calendar_days-header {\n    margin-top: 12px;\n    display: grid;\n    grid-template-columns: repeat(7,1fr);\n    text-align: center;\n}\n\n.datedreamer__calendar_days-header {\n  color: #2d3436;\n  font-size: 1rem;\n}\n\n.dark .datedreamer__calendar_days-header {\n  color: #fff;\n}\n\n.datedreamer__calendar_day {\n    width: 100%;\n    height: 100%;\n    display: block;\n}\n\n.datedreamer__calendar_day button {\n    display: block;\n    width: 100%;\n    height: 100%;\n    cursor: pointer;\n}\n\n.datedreamer__calendar_day.active button {\n    background: blue;\n    color: white;\n}\n\n.datedreamer__calendar_day.highlight button {\n  background: #236bb9;\n  color: white;\n}\n", r.litePurple = '\n.datedreamer__calendar {\n  border-radius: 8px;\n}\n\n.datedreamer__calendar_prev svg, .datedreamer__calendar_next svg {\n  transform: scale(2);\n}\n\n.datedreamer__calendar_title {\n  font-size: 12px;\n}\n\n.datedreamer__calendar_inputs input, .datedreamer__calendar_inputs button {\n  font-weight: 500;\n  border-radius: 4px;\n  border: 1px solid #e9e8ec;\n  font-size: 12px;\n  background: white;\n}\n\n.datedreamer__calendar_inputs label {\n  font-size: 12px;\n}\n\n.datedreamer__calendar_inputs input {\n  flex-grow: 1;\n  width: calc(100% - 8px);\n  display: block;\n  padding: 4px 4px 4px 8px;\n  margin-right: 8px;\n}\n\n.dark .datedreamer__calendar_inputs input {\n  background: #4b6584;\n  border: #4b6584;\n  color: #fff;\n}\n\n.datedreamer__calendar_inputs button {\n  padding: 6px 12px;\n  display: inline-block;\n  cursor: pointer;\n  color: black;\n}\n\n.dark .datedreamer__calendar_inputs button {\n  background: #4b6584;\n  border: #4b6584;\n  color: #fff;\n}\n\n.datedreamer__calendar_errors {\n  font-size: 12px;\n  font-weight: bold;\n}\n\n.datedreamer__calendar_day-header.datedreamer__calendar_day {\n  font-size: 12px;\n}\n\n.datedreamer__calendar_days {\n  margin-top: 8px;\n}\n\n.datedreamer__calendar_days .datedreamer__calendar_day {\n  margin: 2px;\n}\n\n.datedreamer__calendar_days .datedreamer__calendar_day.disabled button{\n  color: #767676;\n  cursor: default;\n  font-weight: normal;\n}\n\n.datedreamer__calendar_days .datedreamer__calendar_day.active, .datedreamer__calendar_days .datedreamer__calendar_day.highlight {\n  position: relative;\n}\n\n.datedreamer__calendar_day.highlight:before{\n  content: "";\n  width: 100%;\n  height: 100%;\n  background: #BFA9F3;\n  position: absolute;\n  display: block;\n  z-index: -1;\n  top: 50%;\n  right: 0;\n  left: 0;\n  transform: translateY(-50%);\n}\n\n\n.datedreamer__calendar_days .datedreamer__calendar_day.active:before {\n  content: "";\n  width: 100%;\n  height: 100%;\n  background: #7d56da;\n  border-radius: 2px;\n  position: absolute;\n  display: block;\n  z-index: -1;\n  top: 50%;\n  right: 0;\n  left: 0;\n  transform: translateY(-50%);\n}\n\n.datedreamer__calendar_days .datedreamer__calendar_day button {\n  background: transparent;\n  border: none;\n  padding: 5px;\n  display: flex;\n  justify-content: center;\n  align-items: center;\n  font-size: 12px;\n  font-weight: bold;\n  color: black;\n}\n\n.datedreamer__calendar_days .datedreamer__calendar_day.active button {\n  color: #fff;\n}\n\n.dark .datedreamer__calendar_days .datedreamer__calendar_day button {\n  color: #ecf0f1;\n}\n'
        }, 109: (e, t) => {
            "use strict";
            Object.defineProperty(t, "__esModule", {value: !0}), t.calendarStyles = void 0, t.calendarStyles = "\n    .datedreamer__calendar {\n        box-shadow: none;\n    } \n"
        }
    }, r = {}, o = {}, (() => {
        "use strict";
        var e = o;
        Object.defineProperty(e, "__esModule", {value: !0}), e.range = e.calendarToggle = e.calendar = void 0;
        const t = i(933), n = (Object.defineProperty(e, "calendarToggle", {
            enumerable: !0, get: function () {
                return t.calendarToggle
            }
        }), i(255)), r = (Object.defineProperty(e, "calendar", {
            enumerable: !0, get: function () {
                return n.calendar
            }
        }), i(98));
        Object.defineProperty(e, "range", {
            enumerable: !0, get: function () {
                return r.range
            }
        })
    })(), o;

    function i(e) {
        var t = r[e];
        return void 0 !== t || (t = r[e] = {exports: {}}, n[e].call(t.exports, t, t.exports, i)), t.exports
    }

    var n, r, o
}), function (e, t) {
    "object" == typeof exports && "undefined" != typeof module ? t(exports) : "function" == typeof define && define.amd ? define(["exports"], t) : t((e = "undefined" != typeof globalThis ? globalThis : e || self).Popper = {})
}(this, function (e) {
    "use strict";

    function b(e) {
        var t;
        return null == e ? window : "[object Window]" !== e.toString() ? (t = e.ownerDocument) && t.defaultView || window : e
    }

    function v(e) {
        return e instanceof b(e).Element || e instanceof Element
    }

    function g(e) {
        return e instanceof b(e).HTMLElement || e instanceof HTMLElement
    }

    function i(e) {
        return "undefined" != typeof ShadowRoot && (e instanceof b(e).ShadowRoot || e instanceof ShadowRoot)
    }

    var M = Math.max, D = Math.min, _ = Math.round;

    function o() {
        var e = navigator.userAgentData;
        return null != e && e.brands && Array.isArray(e.brands) ? e.brands.map(function (e) {
            return e.brand + "/" + e.version
        }).join(" ") : navigator.userAgent
    }

    function u() {
        return !/^((?!chrome|android).)*safari/i.test(o())
    }

    function y(e, t, n) {
        void 0 === t && (t = !1), void 0 === n && (n = !1);
        var r = e.getBoundingClientRect(), i = 1, o = 1,
            t = (t && g(e) && (i = 0 < e.offsetWidth && _(r.width) / e.offsetWidth || 1, o = 0 < e.offsetHeight && _(r.height) / e.offsetHeight || 1), (v(e) ? b(e) : window).visualViewport),
            e = !u() && n, n = (r.left + (e && t ? t.offsetLeft : 0)) / i, e = (r.top + (e && t ? t.offsetTop : 0)) / o,
            t = r.width / i, i = r.height / o;
        return {width: t, height: i, top: e, right: n + t, bottom: e + i, left: n, x: n, y: e}
    }

    function w(e) {
        e = b(e);
        return {scrollLeft: e.pageXOffset, scrollTop: e.pageYOffset}
    }

    function x(e) {
        return e ? (e.nodeName || "").toLowerCase() : null
    }

    function E(e) {
        return ((v(e) ? e.ownerDocument : e.document) || window.document).documentElement
    }

    function L(e) {
        return y(E(e)).left + w(e).scrollLeft
    }

    function S(e) {
        return b(e).getComputedStyle(e)
    }

    function O(e) {
        var e = S(e), t = e.overflow, n = e.overflowX, e = e.overflowY;
        return /auto|scroll|overlay|hidden/.test(t + e + n)
    }

    function k(e) {
        var t = y(e), n = e.offsetWidth, r = e.offsetHeight;
        return Math.abs(t.width - n) <= 1 && (n = t.width), Math.abs(t.height - r) <= 1 && (r = t.height), {
            x: e.offsetLeft,
            y: e.offsetTop,
            width: n,
            height: r
        }
    }

    function A(e) {
        return "html" === x(e) ? e : e.assignedSlot || e.parentNode || (i(e) ? e.host : null) || E(e)
    }

    function C(e, t) {
        void 0 === t && (t = []);
        var n = function e(t) {
                return 0 <= ["html", "body", "#document"].indexOf(x(t)) ? t.ownerDocument.body : g(t) && O(t) ? t : e(A(t))
            }(e), e = n === (null == (e = e.ownerDocument) ? void 0 : e.body), r = b(n),
            r = e ? [r].concat(r.visualViewport || [], O(n) ? n : []) : n, n = t.concat(r);
        return e ? n : n.concat(C(A(r)))
    }

    function a(e) {
        return g(e) && "fixed" !== S(e).position ? e.offsetParent : null
    }

    function T(e) {
        for (var t, n = b(e), r = a(e); r && (t = r, 0 <= ["table", "td", "th"].indexOf(x(t))) && "static" === S(r).position;) r = a(r);
        return (!r || "html" !== x(r) && ("body" !== x(r) || "static" !== S(r).position)) && (r || function (e) {
            var t = /firefox/i.test(o());
            if (!/Trident/i.test(o()) || !g(e) || "fixed" !== S(e).position) {
                var n = A(e);
                for (i(n) && (n = n.host); g(n) && ["html", "body"].indexOf(x(n)) < 0;) {
                    var r = S(n);
                    if ("none" !== r.transform || "none" !== r.perspective || "paint" === r.contain || -1 !== ["transform", "perspective"].indexOf(r.willChange) || t && "filter" === r.willChange || t && r.filter && "none" !== r.filter) return n;
                    n = n.parentNode
                }
            }
            return null
        }(e)) || n
    }

    var q = "top", N = "bottom", W = "right", j = "left", H = "auto", z = [q, N, W, j], P = "start", R = "end",
        X = "viewport", $ = "popper", Z = z.reduce(function (e, t) {
            return e.concat([t + "-" + P, t + "-" + R])
        }, []), J = [].concat(z, [H]).reduce(function (e, t) {
            return e.concat([t, t + "-" + P, t + "-" + R])
        }, []),
        d = ["beforeRead", "read", "afterRead", "beforeMain", "main", "afterMain", "beforeWrite", "write", "afterWrite"];

    function p(e) {
        var n = new Map, r = new Set, i = [];
        return e.forEach(function (e) {
            n.set(e.name, e)
        }), e.forEach(function (e) {
            r.has(e.name) || function t(e) {
                r.add(e.name), [].concat(e.requires || [], e.requiresIfExists || []).forEach(function (e) {
                    r.has(e) || (e = n.get(e)) && t(e)
                }), i.push(e)
            }(e)
        }), i
    }

    function G(e, t) {
        var n = t.getRootNode && t.getRootNode();
        if (e.contains(t)) return !0;
        if (n && i(n)) {
            var r = t;
            do {
                if (r && e.isSameNode(r)) return !0
            } while (r = r.parentNode || r.host)
        }
        return !1
    }

    function Y(e) {
        return Object.assign({}, e, {left: e.x, top: e.y, right: e.x + e.width, bottom: e.y + e.height})
    }

    function Q(e, t, n) {
        return t === X ? Y((i = n, a = b(r = e), s = E(r), a = a.visualViewport, l = s.clientWidth, s = s.clientHeight, d = c = 0, a && (l = a.width, s = a.height, (o = u()) || !o && "fixed" === i) && (c = a.offsetLeft, d = a.offsetTop), {
            width: l,
            height: s,
            x: c + L(r),
            y: d
        })) : v(t) ? ((i = y(o = t, !1, "fixed" === n)).top = i.top + o.clientTop, i.left = i.left + o.clientLeft, i.bottom = i.top + o.clientHeight, i.right = i.left + o.clientWidth, i.width = o.clientWidth, i.height = o.clientHeight, i.x = i.left, i.y = i.top, i) : Y((a = E(e), l = E(a), s = w(a), c = null == (c = a.ownerDocument) ? void 0 : c.body, r = M(l.scrollWidth, l.clientWidth, c ? c.scrollWidth : 0, c ? c.clientWidth : 0), d = M(l.scrollHeight, l.clientHeight, c ? c.scrollHeight : 0, c ? c.clientHeight : 0), a = -s.scrollLeft + L(a), s = -s.scrollTop, "rtl" === S(c || l).direction && (a += M(l.clientWidth, c ? c.clientWidth : 0) - r), {
            width: r,
            height: d,
            x: a,
            y: s
        }));
        var r, i, o, a, s, l, c, d
    }

    function B(e) {
        return e.split("-")[0]
    }

    function I(e) {
        return e.split("-")[1]
    }

    function K(e) {
        return 0 <= ["top", "bottom"].indexOf(e) ? "x" : "y"
    }

    function ee(e) {
        var t, n = e.reference, r = e.element, e = e.placement, i = e ? B(e) : null, e = e ? I(e) : null,
            o = n.x + n.width / 2 - r.width / 2, a = n.y + n.height / 2 - r.height / 2;
        switch (i) {
            case q:
                t = {x: o, y: n.y - r.height};
                break;
            case N:
                t = {x: o, y: n.y + n.height};
                break;
            case W:
                t = {x: n.x + n.width, y: a};
                break;
            case j:
                t = {x: n.x - r.width, y: a};
                break;
            default:
                t = {x: n.x, y: n.y}
        }
        var s = i ? K(i) : null;
        if (null != s) {
            var l = "y" === s ? "height" : "width";
            switch (e) {
                case P:
                    t[s] = t[s] - (n[l] / 2 - r[l] / 2);
                    break;
                case R:
                    t[s] = t[s] + (n[l] / 2 - r[l] / 2)
            }
        }
        return t
    }

    function te(e) {
        return Object.assign({}, {top: 0, right: 0, bottom: 0, left: 0}, e)
    }

    function ne(n, e) {
        return e.reduce(function (e, t) {
            return e[t] = n, e
        }, {})
    }

    function V(e, t) {
        var n, r, i, o, a, s, t = t = void 0 === t ? {} : t, l = t.placement, l = void 0 === l ? e.placement : l,
            c = t.strategy, c = void 0 === c ? e.strategy : c, d = t.boundary, d = void 0 === d ? "clippingParents" : d,
            u = t.rootBoundary, u = void 0 === u ? X : u, p = t.elementContext, p = void 0 === p ? $ : p,
            h = t.altBoundary, h = void 0 !== h && h, t = t.padding, t = void 0 === t ? 0 : t,
            t = te("number" != typeof t ? t : ne(t, z)), f = e.rects.popper,
            h = e.elements[h ? p === $ ? "reference" : $ : p],
            c = (n = v(h) ? h : h.contextElement || E(e.elements.popper), h = u, r = c, o = "clippingParents" === (u = d) ? (a = C(A(o = n)), v(i = 0 <= ["absolute", "fixed"].indexOf(S(o).position) && g(o) ? T(o) : o) ? a.filter(function (e) {
                return v(e) && G(e, i) && "body" !== x(e)
            }) : []) : [].concat(u), a = [].concat(o, [h]), u = a[0], (h = a.reduce(function (e, t) {
                t = Q(n, t, r);
                return e.top = M(t.top, e.top), e.right = D(t.right, e.right), e.bottom = D(t.bottom, e.bottom), e.left = M(t.left, e.left), e
            }, Q(n, u, r))).width = h.right - h.left, h.height = h.bottom - h.top, h.x = h.left, h.y = h.top, h),
            d = y(e.elements.reference), u = ee({reference: d, element: f, strategy: "absolute", placement: l}),
            h = Y(Object.assign({}, f, u)), f = p === $ ? h : d, m = {
                top: c.top - f.top + t.top,
                bottom: f.bottom - c.bottom + t.bottom,
                left: c.left - f.left + t.left,
                right: f.right - c.right + t.right
            }, u = e.modifiersData.offset;
        return p === $ && u && (s = u[l], Object.keys(m).forEach(function (e) {
            var t = 0 <= [W, N].indexOf(e) ? 1 : -1, n = 0 <= [q, N].indexOf(e) ? "y" : "x";
            m[e] += s[n] * t
        })), m
    }

    var re = {placement: "bottom", modifiers: [], strategy: "absolute"};

    function ie() {
        for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++) t[n] = arguments[n];
        return !t.some(function (e) {
            return !(e && "function" == typeof e.getBoundingClientRect)
        })
    }

    function t(e) {
        var e = e = void 0 === e ? {} : e, t = e.defaultModifiers, l = void 0 === t ? [] : t, t = e.defaultOptions,
            c = void 0 === t ? re : t;
        return function (r, i, t) {
            void 0 === t && (t = c);
            var n, o, h = {
                placement: "bottom",
                orderedModifiers: [],
                options: Object.assign({}, re, c),
                modifiersData: {},
                elements: {reference: r, popper: i},
                attributes: {},
                styles: {}
            }, a = [], f = !1, m = {
                state: h, setOptions: function (e) {
                    e = "function" == typeof e ? e(h.options) : e;
                    s(), h.options = Object.assign({}, c, h.options, e), h.scrollParents = {
                        reference: v(r) ? C(r) : r.contextElement ? C(r.contextElement) : [],
                        popper: C(i)
                    };
                    e = [].concat(l, h.options.modifiers), t = e.reduce(function (e, t) {
                        var n = e[t.name];
                        return e[t.name] = n ? Object.assign({}, n, t, {
                            options: Object.assign({}, n.options, t.options),
                            data: Object.assign({}, n.data, t.data)
                        }) : t, e
                    }, {}), n = p(Object.keys(t).map(function (e) {
                        return t[e]
                    }));
                    var t, n, e = d.reduce(function (e, t) {
                        return e.concat(n.filter(function (e) {
                            return e.phase === t
                        }))
                    }, []);
                    return h.orderedModifiers = e.filter(function (e) {
                        return e.enabled
                    }), h.orderedModifiers.forEach(function (e) {
                        var t = e.name, n = e.options, e = e.effect;
                        "function" == typeof e && (e = e({
                            state: h,
                            name: t,
                            instance: m,
                            options: void 0 === n ? {} : n
                        }), a.push(e || function () {
                        }))
                    }), m.update()
                }, forceUpdate: function () {
                    if (!f) {
                        var e = h.elements, t = e.reference, e = e.popper;
                        if (ie(t, e)) {
                            h.rects = {
                                reference: (t = t, a = T(e), void 0 === (s = "fixed" === h.options.strategy) && (s = !1), l = g(a), c = g(a) && (u = (c = a).getBoundingClientRect(), d = _(u.width) / c.offsetWidth || 1, u = _(u.height) / c.offsetHeight || 1, 1 !== d || 1 !== u), d = E(a), u = y(t, c, s), t = {
                                    scrollLeft: 0,
                                    scrollTop: 0
                                }, p = {
                                    x: 0,
                                    y: 0
                                }, !l && s || ("body" === x(a) && !O(d) || (t = (l = a) !== b(l) && g(l) ? {
                                    scrollLeft: l.scrollLeft,
                                    scrollTop: l.scrollTop
                                } : w(l)), g(a) ? ((p = y(a, !0)).x += a.clientLeft, p.y += a.clientTop) : d && (p.x = L(d))), {
                                    x: u.left + t.scrollLeft - p.x,
                                    y: u.top + t.scrollTop - p.y,
                                    width: u.width,
                                    height: u.height
                                }), popper: k(e)
                            }, h.reset = !1, h.placement = h.options.placement, h.orderedModifiers.forEach(function (e) {
                                return h.modifiersData[e.name] = Object.assign({}, e.data)
                            });
                            for (var n, r, i, o = 0; o < h.orderedModifiers.length; o++) !0 !== h.reset ? (n = (i = h.orderedModifiers[o]).fn, r = i.options, i = i.name, "function" == typeof n && (h = n({
                                state: h,
                                options: void 0 === r ? {} : r,
                                name: i,
                                instance: m
                            }) || h)) : (h.reset = !1, o = -1)
                        }
                    }
                    var a, s, l, c, d, u, p
                }, update: (n = function () {
                    return new Promise(function (e) {
                        m.forceUpdate(), e(h)
                    })
                }, function () {
                    return o = o || new Promise(function (e) {
                        Promise.resolve().then(function () {
                            o = void 0, e(n())
                        })
                    })
                }), destroy: function () {
                    s(), f = !0
                }
            };
            return ie(r, i) && m.setOptions(t).then(function (e) {
                !f && t.onFirstUpdate && t.onFirstUpdate(e)
            }), m;

            function s() {
                a.forEach(function (e) {
                    return e()
                }), a = []
            }
        }
    }

    var l = {passive: !0}, n = {
        name: "eventListeners", enabled: !0, phase: "write", fn: function () {
        }, effect: function (e) {
            var t = e.state, n = e.instance, e = e.options, r = e.scroll, i = void 0 === r || r, r = e.resize,
                o = void 0 === r || r, a = b(t.elements.popper),
                s = [].concat(t.scrollParents.reference, t.scrollParents.popper);
            return i && s.forEach(function (e) {
                e.addEventListener("scroll", n.update, l)
            }), o && a.addEventListener("resize", n.update, l), function () {
                i && s.forEach(function (e) {
                    e.removeEventListener("scroll", n.update, l)
                }), o && a.removeEventListener("resize", n.update, l)
            }
        }, data: {}
    }, r = {
        name: "popperOffsets", enabled: !0, phase: "read", fn: function (e) {
            var t = e.state, e = e.name;
            t.modifiersData[e] = ee({
                reference: t.rects.reference,
                element: t.rects.popper,
                strategy: "absolute",
                placement: t.placement
            })
        }, data: {}
    }, oe = {top: "auto", right: "auto", bottom: "auto", left: "auto"};

    function s(e) {
        var t = e.popper, n = e.popperRect, r = e.placement, i = e.variation, o = e.offsets, a = e.position,
            s = e.gpuAcceleration, l = e.adaptive, c = e.roundOffsets, e = e.isFixed, d = o.x, d = void 0 === d ? 0 : d,
            u = o.y, u = void 0 === u ? 0 : u, p = "function" == typeof c ? c({x: d, y: u}) : {x: d, y: u}, d = p.x,
            u = p.y, p = o.hasOwnProperty("x"), o = o.hasOwnProperty("y"), h = j, f = q, m = window;
        l && (g = "clientHeight", v = "clientWidth", (y = T(t)) === b(t) && "static" !== S(y = E(t)).position && "absolute" === a && (g = "scrollHeight", v = "scrollWidth"), r !== q && (r !== j && r !== W || i !== R) || (f = N, u = (u - ((e && y === m && m.visualViewport ? m.visualViewport.height : y[g]) - n.height)) * (s ? 1 : -1)), r !== j && (r !== q && r !== N || i !== R) || (h = W, d = (d - ((e && y === m && m.visualViewport ? m.visualViewport.width : y[v]) - n.width)) * (s ? 1 : -1)));
        var v, g = Object.assign({position: a}, l && oe),
            y = !0 === c ? (r = {x: d, y: u}, i = b(t), e = r.y, i = i.devicePixelRatio || 1, {
                x: _(r.x * i) / i || 0,
                y: _(e * i) / i || 0
            }) : {x: d, y: u};
        return d = y.x, u = y.y, s ? Object.assign({}, g, ((v = {})[f] = o ? "0" : "", v[h] = p ? "0" : "", v.transform = (m.devicePixelRatio || 1) <= 1 ? "translate(" + d + "px, " + u + "px)" : "translate3d(" + d + "px, " + u + "px, 0)", v)) : Object.assign({}, g, ((n = {})[f] = o ? u + "px" : "", n[h] = p ? d + "px" : "", n.transform = "", n))
    }

    var c = {
        name: "computeStyles", enabled: !0, phase: "beforeWrite", fn: function (e) {
            var t = e.state, e = e.options, n = e.gpuAcceleration, n = void 0 === n || n, r = e.adaptive,
                r = void 0 === r || r, e = e.roundOffsets, e = void 0 === e || e, n = {
                    placement: B(t.placement),
                    variation: I(t.placement),
                    popper: t.elements.popper,
                    popperRect: t.rects.popper,
                    gpuAcceleration: n,
                    isFixed: "fixed" === t.options.strategy
                };
            null != t.modifiersData.popperOffsets && (t.styles.popper = Object.assign({}, t.styles.popper, s(Object.assign({}, n, {
                offsets: t.modifiersData.popperOffsets,
                position: t.options.strategy,
                adaptive: r,
                roundOffsets: e
            })))), null != t.modifiersData.arrow && (t.styles.arrow = Object.assign({}, t.styles.arrow, s(Object.assign({}, n, {
                offsets: t.modifiersData.arrow,
                position: "absolute",
                adaptive: !1,
                roundOffsets: e
            })))), t.attributes.popper = Object.assign({}, t.attributes.popper, {"data-popper-placement": t.placement})
        }, data: {}
    }, h = {
        name: "applyStyles", enabled: !0, phase: "write", fn: function (e) {
            var i = e.state;
            Object.keys(i.elements).forEach(function (e) {
                var t = i.styles[e] || {}, n = i.attributes[e] || {}, r = i.elements[e];
                g(r) && x(r) && (Object.assign(r.style, t), Object.keys(n).forEach(function (e) {
                    var t = n[e];
                    !1 === t ? r.removeAttribute(e) : r.setAttribute(e, !0 === t ? "" : t)
                }))
            })
        }, effect: function (e) {
            var r = e.state, i = {
                popper: {position: r.options.strategy, left: "0", top: "0", margin: "0"},
                arrow: {position: "absolute"},
                reference: {}
            };
            return Object.assign(r.elements.popper.style, i.popper), r.styles = i, r.elements.arrow && Object.assign(r.elements.arrow.style, i.arrow), function () {
                Object.keys(r.elements).forEach(function (e) {
                    var t = r.elements[e], n = r.attributes[e] || {},
                        e = Object.keys((r.styles.hasOwnProperty(e) ? r.styles : i)[e]).reduce(function (e, t) {
                            return e[t] = "", e
                        }, {});
                    g(t) && x(t) && (Object.assign(t.style, e), Object.keys(n).forEach(function (e) {
                        t.removeAttribute(e)
                    }))
                })
            }
        }, requires: ["computeStyles"]
    }, f = {
        name: "offset", enabled: !0, phase: "main", requires: ["popperOffsets"], fn: function (e) {
            var a = e.state, t = e.options, e = e.name, t = t.offset, s = void 0 === t ? [0, 0] : t,
                t = J.reduce(function (e, t) {
                    return e[t] = (t = t, n = a.rects, r = s, i = B(t), o = 0 <= [j, q].indexOf(i) ? -1 : 1, n = "function" == typeof r ? r(Object.assign({}, n, {placement: t})) : r, t = n[0] || 0, r = (n[1] || 0) * o, 0 <= [j, W].indexOf(i) ? {
                        x: r,
                        y: t
                    } : {x: t, y: r}), e;
                    var n, r, i, o
                }, {}), n = t[a.placement], r = n.x, n = n.y;
            null != a.modifiersData.popperOffsets && (a.modifiersData.popperOffsets.x += r, a.modifiersData.popperOffsets.y += n), a.modifiersData[e] = t
        }
    }, m = {left: "right", right: "left", bottom: "top", top: "bottom"};

    function F(e) {
        return e.replace(/left|right|bottom|top/g, function (e) {
            return m[e]
        })
    }

    var ae = {start: "end", end: "start"};

    function se(e) {
        return e.replace(/start|end/g, function (e) {
            return ae[e]
        })
    }

    var le = {
        name: "flip", enabled: !0, phase: "main", fn: function (e) {
            var u = e.state, t = e.options, e = e.name;
            if (!u.modifiersData[e]._skip) {
                for (var n = t.mainAxis, r = void 0 === n || n, n = t.altAxis, i = void 0 === n || n, n = t.fallbackPlacements, p = t.padding, h = t.boundary, f = t.rootBoundary, o = t.altBoundary, a = t.flipVariations, m = void 0 === a || a, v = t.allowedAutoPlacements, a = u.options.placement, t = B(a), n = n || (t !== a && m ? B(n = a) === H ? [] : (t = F(n), [se(n), t, se(t)]) : [F(a)]), s = [a].concat(n).reduce(function (e, t) {
                    return e.concat(B(t) === H ? (n = u, r = (e = e = void 0 === (e = {
                        placement: t,
                        boundary: h,
                        rootBoundary: f,
                        padding: p,
                        flipVariations: m,
                        allowedAutoPlacements: v
                    }) ? {} : e).placement, i = e.boundary, o = e.rootBoundary, a = e.padding, s = e.flipVariations, l = void 0 === (e = e.allowedAutoPlacements) ? J : e, c = I(r), e = c ? s ? Z : Z.filter(function (e) {
                        return I(e) === c
                    }) : z, d = (r = 0 === (r = e.filter(function (e) {
                        return 0 <= l.indexOf(e)
                    })).length ? e : r).reduce(function (e, t) {
                        return e[t] = V(n, {placement: t, boundary: i, rootBoundary: o, padding: a})[B(t)], e
                    }, {}), Object.keys(d).sort(function (e, t) {
                        return d[e] - d[t]
                    })) : t);
                    var n, r, i, o, a, s, l, c, d
                }, []), l = u.rects.reference, c = u.rects.popper, d = new Map, g = !0, y = s[0], b = 0; b < s.length; b++) {
                    var _ = s[b], w = B(_), x = I(_) === P, E = 0 <= [q, N].indexOf(w), L = E ? "width" : "height",
                        S = V(u, {placement: _, boundary: h, rootBoundary: f, altBoundary: o, padding: p}),
                        E = E ? x ? W : j : x ? N : q, x = (l[L] > c[L] && (E = F(E)), F(E)), L = [];
                    if (r && L.push(S[w] <= 0), i && L.push(S[E] <= 0, S[x] <= 0), L.every(function (e) {
                        return e
                    })) {
                        y = _, g = !1;
                        break
                    }
                    d.set(_, L)
                }
                if (g) for (var M = m ? 3 : 1; 0 < M && "break" !== function (t) {
                    var e = s.find(function (e) {
                        e = d.get(e);
                        if (e) return e.slice(0, t).every(function (e) {
                            return e
                        })
                    });
                    if (e) return y = e, "break"
                }(M); M--) ;
                u.placement !== y && (u.modifiersData[e]._skip = !0, u.placement = y, u.reset = !0)
            }
        }, requiresIfExists: ["offset"], data: {_skip: !1}
    };

    function U(e, t, n) {
        return M(e, D(t, n))
    }

    var ce = {
        name: "preventOverflow", enabled: !0, phase: "main", fn: function (e) {
            var t, n, r, i, o, a, s, l, c, d = e.state, u = e.options, e = e.name, p = u.mainAxis,
                p = void 0 === p || p, h = u.altAxis, h = void 0 !== h && h, f = u.boundary, m = u.rootBoundary,
                v = u.altBoundary, g = u.padding, y = u.tether, y = void 0 === y || y, u = u.tetherOffset,
                u = void 0 === u ? 0 : u, f = V(d, {boundary: f, rootBoundary: m, padding: g, altBoundary: v}),
                m = B(d.placement), g = I(d.placement), v = !g, b = K(m), _ = "x" === b ? "y" : "x",
                w = d.modifiersData.popperOffsets, x = d.rects.reference, E = d.rects.popper,
                u = "function" == typeof u ? u(Object.assign({}, d.rects, {placement: d.placement})) : u,
                u = "number" == typeof u ? {mainAxis: u, altAxis: u} : Object.assign({mainAxis: 0, altAxis: 0}, u),
                L = d.modifiersData.offset ? d.modifiersData.offset[d.placement] : null, S = {x: 0, y: 0};
            w && (p && (p = "y" === b ? "height" : "width", a = (s = w[b]) + f[n = "y" === b ? q : j], l = s - f[c = "y" === b ? N : W], t = y ? -E[p] / 2 : 0, i = (g === P ? x : E)[p], g = g === P ? -E[p] : -x[p], o = d.elements.arrow, o = y && o ? k(o) : {
                width: 0,
                height: 0
            }, n = (r = d.modifiersData["arrow#persistent"] ? d.modifiersData["arrow#persistent"].padding : {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            })[n], r = r[c], c = U(0, x[p], o[p]), o = v ? x[p] / 2 - t - c - n - u.mainAxis : i - c - n - u.mainAxis, i = v ? -x[p] / 2 + t + c + r + u.mainAxis : g + c + r + u.mainAxis, v = (n = d.elements.arrow && T(d.elements.arrow)) ? "y" === b ? n.clientTop || 0 : n.clientLeft || 0 : 0, g = s + i - (t = null != (p = null == L ? void 0 : L[b]) ? p : 0), c = U(y ? D(a, s + o - t - v) : a, s, y ? M(l, g) : l), w[b] = c, S[b] = c - s), h && (r = "y" == _ ? "height" : "width", i = (n = w[_]) + f["x" === b ? q : j], p = n - f["x" === b ? N : W], o = -1 !== [q, j].indexOf(m), v = null != (t = null == L ? void 0 : L[_]) ? t : 0, a = o ? i : n - x[r] - E[r] - v + u.altAxis, g = o ? n + x[r] + E[r] - v - u.altAxis : p, s = y && o ? (c = U(a, n, l = g), l < c ? l : c) : U(y ? a : i, n, y ? g : p), w[_] = s, S[_] = s - n), d.modifiersData[e] = S)
        }, requiresIfExists: ["offset"]
    }, de = {
        name: "arrow", enabled: !0, phase: "main", fn: function (e) {
            var t, n, r, i, o = e.state, a = e.name, e = e.options, s = o.elements.arrow,
                l = o.modifiersData.popperOffsets, c = B(o.placement), d = K(c),
                c = 0 <= [j, W].indexOf(c) ? "height" : "width";
            s && l && (e = te("number" != typeof (e = "function" == typeof (e = e.padding) ? e(Object.assign({}, o.rects, {placement: o.placement})) : e) ? e : ne(e, z)), t = k(s), i = "y" === d ? q : j, r = "y" === d ? N : W, n = o.rects.reference[c] + o.rects.reference[d] - l[d] - o.rects.popper[c], l = l[d] - o.rects.reference[d], s = (s = T(s)) ? "y" === d ? s.clientHeight || 0 : s.clientWidth || 0 : 0, i = e[i], e = s - t[c] - e[r], i = U(i, r = s / 2 - t[c] / 2 + (n / 2 - l / 2), e), o.modifiersData[a] = ((s = {})[d] = i, s.centerOffset = i - r, s))
        }, effect: function (e) {
            var t = e.state, e = e.options.element, e = void 0 === e ? "[data-popper-arrow]" : e;
            null != e && ("string" != typeof e || (e = t.elements.popper.querySelector(e))) && G(t.elements.popper, e) && (t.elements.arrow = e)
        }, requires: ["popperOffsets"], requiresIfExists: ["preventOverflow"]
    };

    function ue(e, t, n) {
        return {
            top: e.top - t.height - (n = void 0 === n ? {x: 0, y: 0} : n).y,
            right: e.right - t.width + n.x,
            bottom: e.bottom - t.height + n.y,
            left: e.left - t.width - n.x
        }
    }

    function pe(t) {
        return [q, W, N, j].some(function (e) {
            return 0 <= t[e]
        })
    }

    var he = {
        name: "hide", enabled: !0, phase: "main", requiresIfExists: ["preventOverflow"], fn: function (e) {
            var t = e.state, e = e.name, n = t.rects.reference, r = t.rects.popper, i = t.modifiersData.preventOverflow,
                o = V(t, {elementContext: "reference"}), a = V(t, {altBoundary: !0}), o = ue(o, n), n = ue(a, r, i),
                a = pe(o), r = pe(n);
            t.modifiersData[e] = {
                referenceClippingOffsets: o,
                popperEscapeOffsets: n,
                isReferenceHidden: a,
                hasPopperEscaped: r
            }, t.attributes.popper = Object.assign({}, t.attributes.popper, {
                "data-popper-reference-hidden": a,
                "data-popper-escaped": r
            })
        }
    }, fe = t({defaultModifiers: [n, r, c, h]}), me = [n, r, c, h, f, le, ce, de, he], ve = t({defaultModifiers: me});
    e.applyStyles = h, e.arrow = de, e.computeStyles = c, e.createPopper = ve, e.createPopperLite = fe, e.defaultModifiers = me, e.detectOverflow = V, e.eventListeners = n, e.flip = le, e.hide = he, e.offset = f, e.popperGenerator = t, e.popperOffsets = r, e.preventOverflow = ce, Object.defineProperty(e, "__esModule", {value: !0})
}), function (e, t) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = t(require("@popperjs/core")) : "function" == typeof define && define.amd ? define(["@popperjs/core"], t) : (e = e || self).tippy = t(e.Popper)
}(this, function (ee) {
    "use strict";
    var e = "undefined" != typeof window && "undefined" != typeof document, xe = e && !!window.msCrypto,
        r = "tippy-content", o = "tippy-backdrop", n = "tippy-arrow", i = "tippy-svg-arrow",
        te = {passive: !0, capture: !0}, ne = function () {
            return document.body
        };

    function re(e, t, n) {
        var r;
        return Array.isArray(e) ? null == (r = e[t]) ? Array.isArray(n) ? n[t] : n : r : e
    }

    function a(e, t) {
        e = {}.toString.call(e);
        return 0 === e.indexOf("[object") && -1 < e.indexOf(t + "]")
    }

    function ie(e, t) {
        return "function" == typeof e ? e.apply(void 0, t) : e
    }

    function oe(t, n) {
        var r;
        return 0 === n ? t : function (e) {
            clearTimeout(r), r = setTimeout(function () {
                t(e)
            }, n)
        }
    }

    function g(e, t) {
        var n = Object.assign({}, e);
        return t.forEach(function (e) {
            delete n[e]
        }), n
    }

    function ae(e) {
        return [].concat(e)
    }

    function se(e, t) {
        -1 === e.indexOf(t) && e.push(t)
    }

    function Ee(e) {
        return e.split("-")[0]
    }

    function le(e) {
        return [].slice.call(e)
    }

    function Le(n) {
        return Object.keys(n).reduce(function (e, t) {
            return void 0 !== n[t] && (e[t] = n[t]), e
        }, {})
    }

    function ce() {
        return document.createElement("div")
    }

    function s(t) {
        return ["Element", "Fragment"].some(function (e) {
            return a(t, e)
        })
    }

    function de(e) {
        return a(e, "MouseEvent")
    }

    function d(e) {
        return e && e._tippy && e._tippy.reference === e
    }

    function u(e) {
        return s(e) ? [e] : a(e, "NodeList") ? le(e) : Array.isArray(e) ? e : le(document.querySelectorAll(e))
    }

    function ue(e, t) {
        e.forEach(function (e) {
            e && (e.style.transitionDuration = t + "ms")
        })
    }

    function pe(e, t) {
        e.forEach(function (e) {
            e && e.setAttribute("data-state", t)
        })
    }

    function Se(e) {
        var t, e = ae(e)[0];
        return null != e && null != (t = e.ownerDocument) && t.body ? e.ownerDocument : document
    }

    function he(t, e, n) {
        var r = e + "EventListener";
        ["transitionend", "webkitTransitionEnd"].forEach(function (e) {
            t[r](e, n)
        })
    }

    function Me(e, t) {
        for (var n, r = t; r;) {
            if (e.contains(r)) return !0;
            r = null == r.getRootNode || null == (n = r.getRootNode()) ? void 0 : n.host
        }
        return !1
    }

    var fe = {isTouch: !1}, p = 0;

    function h() {
        fe.isTouch || (fe.isTouch = !0, window.performance && document.addEventListener("mousemove", v))
    }

    function v() {
        var e = performance.now();
        e - p < 20 && (fe.isTouch = !1, document.removeEventListener("mousemove", v)), p = e
    }

    function b() {
        var e, t = document.activeElement;
        d(t) && (e = t._tippy, t.blur) && !e.state.isVisible && t.blur()
    }

    function me(e) {
        return [e + "() was called on a" + ("destroy" === e ? "n already-" : " ") + "destroyed instance. This is a no-op but", "indicates a potential memory leak."].join(" ")
    }

    function _(e) {
        return e.replace(/[ \t]{2,}/g, " ").replace(/^[ \t]*/gm, "").trim()
    }

    function w(e) {
        return [_("\n  %ctippy.js\n\n  %c" + _(e) + "\n\n  %c👷‍ This is a development-only message. It will be removed in production.\n  "), "color: #00C584; font-size: 1.3em; font-weight: bold;", "line-height: 1.5", "color: #a6a095;"]
    }

    function ve(e, t) {
        e && !l.has(t) && (l.add(t), (e = console).warn.apply(e, w(t)))
    }

    function ge(e, t) {
        e && !l.has(t) && (l.add(t), (e = console).error.apply(e, w(t)))
    }

    var l = new Set, x = {animateFill: !1, followCursor: !1, inlinePositioning: !1, sticky: !1}, ye = Object.assign({
        appendTo: ne,
        aria: {content: "auto", expanded: "auto"},
        delay: 0,
        duration: [300, 250],
        getReferenceClientRect: null,
        hideOnClick: !0,
        ignoreAttributes: !1,
        interactive: !1,
        interactiveBorder: 2,
        interactiveDebounce: 0,
        moveTransition: "",
        offset: [0, 10],
        onAfterUpdate: function () {
        },
        onBeforeUpdate: function () {
        },
        onCreate: function () {
        },
        onDestroy: function () {
        },
        onHidden: function () {
        },
        onHide: function () {
        },
        onMount: function () {
        },
        onShow: function () {
        },
        onShown: function () {
        },
        onTrigger: function () {
        },
        onUntrigger: function () {
        },
        onClickOutside: function () {
        },
        placement: "top",
        plugins: [],
        popperOptions: {},
        render: null,
        showOnCreate: !1,
        touch: !0,
        trigger: "mouseenter focus",
        triggerTarget: null
    }, x, {
        allowHTML: !1,
        animation: "fade",
        arrow: !0,
        content: "",
        inertia: !1,
        maxWidth: 350,
        role: "tooltip",
        theme: "",
        zIndex: 9999
    }), E = Object.keys(ye);

    function De(r) {
        var e = (r.plugins || []).reduce(function (e, t) {
            var n = t.name, t = t.defaultValue;
            return n && (e[n] = void 0 !== r[n] ? r[n] : null != (n = ye[n]) ? n : t), e
        }, {});
        return Object.assign({}, r, e)
    }

    function Oe(e, t) {
        var i,
            e = Object.assign({}, t, {content: ie(t.content, [e])}, t.ignoreAttributes ? {} : (i = e, ((e = t.plugins) ? Object.keys(De(Object.assign({}, ye, {plugins: e}))) : E).reduce(function (t, n) {
                var r = (i.getAttribute("data-tippy-" + n) || "").trim();
                if (r) if ("content" === n) t[n] = r; else try {
                    t[n] = JSON.parse(r)
                } catch (e) {
                    t[n] = r
                }
                return t
            }, {})));
        return e.aria = Object.assign({}, ye.aria, e.aria), e.aria = {
            expanded: "auto" === e.aria.expanded ? t.interactive : e.aria.expanded,
            content: "auto" === e.aria.content ? t.interactive ? null : "describedby" : e.aria.content
        }, e
    }

    function L(e, n) {
        void 0 === e && (e = {}), void 0 === n && (n = []), Object.keys(e).forEach(function (t) {
            var e = g(ye, Object.keys(x));
            ve(!{}.hasOwnProperty.call(e, t) && 0 === n.filter(function (e) {
                return e.name === t
            }).length, ["`" + t + "`", "is not a valid prop. You may have spelled it incorrectly, or if it's", "a plugin, forgot to pass it in an array as props.plugins.", "\n\n", "All props: https://atomiks.github.io/tippyjs/v6/all-props/\n", "Plugins: https://atomiks.github.io/tippyjs/v6/plugins/"].join(" "))
        })
    }

    function S() {
        return "innerHTML"
    }

    function c(e, t) {
        e[S()] = t
    }

    function M(e) {
        var t = ce();
        return !0 === e ? t.className = n : (t.className = i, s(e) ? t.appendChild(e) : c(t, e)), t
    }

    function D(e, t) {
        s(t.content) ? (c(e, ""), e.appendChild(t.content)) : "function" != typeof t.content && (t.allowHTML ? c(e, t.content) : e.textContent = t.content)
    }

    function be(e) {
        var e = e.firstElementChild, t = le(e.children);
        return {
            box: e, content: t.find(function (e) {
                return e.classList.contains(r)
            }), arrow: t.find(function (e) {
                return e.classList.contains(n) || e.classList.contains(i)
            }), backdrop: t.find(function (e) {
                return e.classList.contains(o)
            })
        }
    }

    function O(o) {
        var a = ce(), e = ce(),
            t = (e.className = "tippy-box", e.setAttribute("data-state", "hidden"), e.setAttribute("tabindex", "-1"), ce());

        function n(e, t) {
            var n = be(a), r = n.box, i = n.content, n = n.arrow;
            t.theme ? r.setAttribute("data-theme", t.theme) : r.removeAttribute("data-theme"), "string" == typeof t.animation ? r.setAttribute("data-animation", t.animation) : r.removeAttribute("data-animation"), t.inertia ? r.setAttribute("data-inertia", "") : r.removeAttribute("data-inertia"), r.style.maxWidth = "number" == typeof t.maxWidth ? t.maxWidth + "px" : t.maxWidth, t.role ? r.setAttribute("role", t.role) : r.removeAttribute("role"), e.content === t.content && e.allowHTML === t.allowHTML || D(i, o.props), t.arrow ? n ? e.arrow !== t.arrow && (r.removeChild(n), r.appendChild(M(t.arrow))) : r.appendChild(M(t.arrow)) : n && r.removeChild(n)
        }

        return t.className = r, t.setAttribute("data-state", "hidden"), D(t, o.props), a.appendChild(e), e.appendChild(t), n(o.props, o.props), {
            popper: a,
            onUpdate: n
        }
    }

    O.$$tippy = !0;
    var ke = 1, _e = [], we = [];

    function k(a, e) {
        var r, t, n, i, o, s, l, c, d, u, p, h, f = Oe(a, Object.assign({}, ye, De(Le(e)))), m = !1, v = !1, g = !1,
            y = !1, b = [], _ = oe(U, f.interactiveDebounce), e = ke++, w = (c = f.plugins).filter(function (e, t) {
                return c.indexOf(e) === t
            }), x = {
                id: e,
                reference: a,
                popper: ce(),
                popperInstance: null,
                props: f,
                state: {isEnabled: !0, isVisible: !1, isDestroyed: !1, isMounted: !1, isShown: !1},
                plugins: w,
                clearDelayTimeouts: function () {
                    clearTimeout(r), clearTimeout(t), cancelAnimationFrame(n)
                },
                setProps: function (e) {
                    var t, n;
                    ve(x.state.isDestroyed, me("setProps")), x.state.isDestroyed || (O("onBeforeUpdate", [x, e]), V(), t = x.props, n = Oe(a, Object.assign({}, t, Le(e), {ignoreAttributes: !0})), x.props = n, I(), t.interactiveDebounce !== n.interactiveDebounce && (A(), _ = oe(U, n.interactiveDebounce)), t.triggerTarget && !n.triggerTarget ? ae(t.triggerTarget).forEach(function (e) {
                        e.removeAttribute("aria-expanded")
                    }) : n.triggerTarget && a.removeAttribute("aria-expanded"), k(), D(), u && u(t, n), x.popperInstance && (G(), N().forEach(function (e) {
                        requestAnimationFrame(e._tippy.popperInstance.forceUpdate)
                    })), O("onAfterUpdate", [x, e]))
                },
                setContent: function (e) {
                    x.setProps({content: e})
                },
                show: function () {
                    ve(x.state.isDestroyed, me("show"));
                    var e = x.state.isVisible, t = x.state.isDestroyed, n = !x.state.isEnabled,
                        r = fe.isTouch && !x.props.touch, i = re(x.props.duration, 0, ye.duration);
                    e || t || n || r || L().hasAttribute("disabled") || (O("onShow", [x], !1), !1 !== x.props.onShow(x) && (x.state.isVisible = !0, E() && (d.style.visibility = "visible"), D(), Y(), x.state.isMounted || (d.style.transition = "none"), E() && (e = M(), t = e.box, n = e.content, ue([t, n], 0)), s = function () {
                        var e, t;
                        x.state.isVisible && !y && (y = !0, d.offsetHeight, d.style.transition = x.props.moveTransition, E() && x.props.animation && (ue([e = (t = M()).box, t = t.content], i), pe([e, t], "visible")), P(), k(), se(we, x), null != (e = x.popperInstance) && e.forceUpdate(), O("onMount", [x]), x.props.animation) && E() && B(i, function () {
                            x.state.isShown = !0, O("onShown", [x])
                        })
                    }, r = x.props.appendTo, e = L(), (t = x.props.interactive && r === ne || "parent" === r ? e.parentNode : ie(r, [e])).contains(d) || t.appendChild(d), x.state.isMounted = !0, G(), ve(x.props.interactive && r === ye.appendTo && e.nextElementSibling !== d, ["Interactive tippy element may not be accessible via keyboard", "navigation because it is not directly after the reference element", "in the DOM source order.", "\n\n", "Using a wrapper <div> or <span> tag around the reference element", "solves this by creating a new parentNode context.", "\n\n", "Specifying `appendTo: document.body` silences this warning, but it", "assumes you are using a focus management solution to handle", "keyboard navigation.", "\n\n", "See: https://atomiks.github.io/tippyjs/v6/accessibility/#interactivity"].join(" "))))
                },
                hide: function () {
                    ve(x.state.isDestroyed, me("hide"));
                    var e, t = !x.state.isVisible, n = x.state.isDestroyed, r = !x.state.isEnabled,
                        i = re(x.props.duration, 1, ye.duration);
                    t || n || r || (O("onHide", [x], !1), !1 !== x.props.onHide(x) && (x.state.isVisible = !1, x.state.isShown = !1, m = y = !1, E() && (d.style.visibility = "hidden"), A(), T(), D(!0), E() && (t = M(), n = t.box, r = t.content, x.props.animation) && (ue([n, r], i), pe([n, r], "hidden")), P(), k(), x.props.animation ? E() && (e = x.unmount, B(i, function () {
                        !x.state.isVisible && d.parentNode && d.parentNode.contains(d) && e()
                    })) : x.unmount()))
                },
                hideWithInteractivity: function (e) {
                    ve(x.state.isDestroyed, me("hideWithInteractivity")), S().addEventListener("mousemove", _), se(_e, _), _(e)
                },
                enable: function () {
                    x.state.isEnabled = !0
                },
                disable: function () {
                    x.hide(), x.state.isEnabled = !1
                },
                unmount: function () {
                    ve(x.state.isDestroyed, me("unmount")), x.state.isVisible && x.hide();
                    x.state.isMounted && (Q(), N().forEach(function (e) {
                        e._tippy.unmount()
                    }), d.parentNode && d.parentNode.removeChild(d), we = we.filter(function (e) {
                        return e !== x
                    }), x.state.isMounted = !1, O("onHidden", [x]))
                },
                destroy: function () {
                    ve(x.state.isDestroyed, me("destroy")), x.state.isDestroyed || (x.clearDelayTimeouts(), x.unmount(), V(), delete a._tippy, x.state.isDestroyed = !0, O("onDestroy", [x]))
                }
            };
        return f.render ? (e = f.render(x), d = e.popper, u = e.onUpdate, p = (d.setAttribute("data-tippy-root", ""), d.id = "tippy-" + x.id, x.popper = d, a._tippy = x, d._tippy = x, w.map(function (e) {
            return e.fn(x)
        })), h = a.hasAttribute("aria-expanded"), I(), k(), D(), O("onCreate", [x]), f.showOnCreate && K(), d.addEventListener("mouseenter", function () {
            x.props.interactive && x.state.isVisible && x.clearDelayTimeouts()
        }), d.addEventListener("mouseleave", function () {
            x.props.interactive && 0 <= x.props.trigger.indexOf("mouseenter") && S().addEventListener("mousemove", _)
        })) : ge(!0, "render() function has not been supplied."), x;

        function j() {
            var e = x.props.touch;
            return Array.isArray(e) ? e : [e, 0]
        }

        function H() {
            return "hold" === j()[0]
        }

        function E() {
            var e;
            return null != (e = x.props.render) && e.$$tippy
        }

        function L() {
            return l || a
        }

        function S() {
            var e = L().parentNode;
            return e ? Se(e) : document
        }

        function M() {
            return be(d)
        }

        function z(e) {
            return x.state.isMounted && !x.state.isVisible || fe.isTouch || i && "focus" === i.type ? 0 : re(x.props.delay, e ? 0 : 1, ye.delay)
        }

        function D(e) {
            void 0 === e && (e = !1), d.style.pointerEvents = x.props.interactive && !e ? "" : "none", d.style.zIndex = "" + x.props.zIndex
        }

        function O(t, n, e) {
            void 0 === e && (e = !0), p.forEach(function (e) {
                e[t] && e[t].apply(e, n)
            }), e && (e = x.props)[t].apply(e, n)
        }

        function P() {
            var n, r, e = x.props.aria;
            e.content && (n = "aria-" + e.content, r = d.id, ae(x.props.triggerTarget || a).forEach(function (e) {
                var t = e.getAttribute(n);
                x.state.isVisible ? e.setAttribute(n, t ? t + " " + r : r) : (t = t && t.replace(r, "").trim()) ? e.setAttribute(n, t) : e.removeAttribute(n)
            }))
        }

        function k() {
            !h && x.props.aria.expanded && ae(x.props.triggerTarget || a).forEach(function (e) {
                x.props.interactive ? e.setAttribute("aria-expanded", x.state.isVisible && e === L() ? "true" : "false") : e.removeAttribute("aria-expanded")
            })
        }

        function A() {
            S().removeEventListener("mousemove", _), _e = _e.filter(function (e) {
                return e !== _
            })
        }

        function C(e) {
            if (!fe.isTouch || !g && "mousedown" !== e.type) {
                var t = e.composedPath && e.composedPath()[0] || e.target;
                if (!x.props.interactive || !Me(d, t)) {
                    if (ae(x.props.triggerTarget || a).some(function (e) {
                        return Me(e, t)
                    })) {
                        if (fe.isTouch) return;
                        if (x.state.isVisible && 0 <= x.props.trigger.indexOf("click")) return
                    } else O("onClickOutside", [x, e]);
                    !0 === x.props.hideOnClick && (x.clearDelayTimeouts(), x.hide(), v = !0, setTimeout(function () {
                        v = !1
                    }), x.state.isMounted || T())
                }
            }
        }

        function R() {
            g = !0
        }

        function $() {
            g = !1
        }

        function Y() {
            var e = S();
            e.addEventListener("mousedown", C, !0), e.addEventListener("touchend", C, te), e.addEventListener("touchstart", $, te), e.addEventListener("touchmove", R, te)
        }

        function T() {
            var e = S();
            e.removeEventListener("mousedown", C, !0), e.removeEventListener("touchend", C, te), e.removeEventListener("touchstart", $, te), e.removeEventListener("touchmove", R, te)
        }

        function B(e, t) {
            var n = M().box;

            function r(e) {
                e.target === n && (he(n, "remove", r), t())
            }

            if (0 === e) return t();
            he(n, "remove", o), he(n, "add", r), o = r
        }

        function q(t, n, r) {
            void 0 === r && (r = !1), ae(x.props.triggerTarget || a).forEach(function (e) {
                e.addEventListener(t, n, r), b.push({node: e, eventType: t, handler: n, options: r})
            })
        }

        function I() {
            H() && (q("touchstart", F, {passive: !0}), q("touchend", X, {passive: !0})), x.props.trigger.split(/\s+/).filter(Boolean).forEach(function (e) {
                if ("manual" !== e) switch (q(e, F), e) {
                    case "mouseenter":
                        q("mouseleave", X);
                        break;
                    case "focus":
                        q(xe ? "focusout" : "blur", Z);
                        break;
                    case "focusin":
                        q("focusout", Z)
                }
            })
        }

        function V() {
            b.forEach(function (e) {
                var t = e.node, n = e.eventType, r = e.handler, e = e.options;
                t.removeEventListener(n, r, e)
            }), b = []
        }

        function F(t) {
            var e, n = !1;
            !x.state.isEnabled || J(t) || v || (e = "focus" === (null == i ? void 0 : i.type), l = (i = t).currentTarget, k(), !x.state.isVisible && de(t) && _e.forEach(function (e) {
                return e(t)
            }), "click" === t.type && (x.props.trigger.indexOf("mouseenter") < 0 || m) && !1 !== x.props.hideOnClick && x.state.isVisible ? n = !0 : K(t), "click" === t.type && (m = !n), n && !e && W(t))
        }

        function U(e) {
            var s, l, t = e.target, t = L().contains(t) || d.contains(t);
            "mousemove" === e.type && t || (t = N().concat(d).map(function (e) {
                var t = null == (t = e._tippy.popperInstance) ? void 0 : t.state;
                return t ? {popperRect: e.getBoundingClientRect(), popperState: t, props: f} : null
            }).filter(Boolean), s = e.clientX, l = e.clientY, t.every(function (e) {
                var t, n, r, i = e.popperRect, o = e.popperState, e = e.props.interactiveBorder, a = Ee(o.placement),
                    o = o.modifiersData.offset;
                return !o || (t = "bottom" === a ? o.top.y : 0, n = "top" === a ? o.bottom.y : 0, r = "right" === a ? o.left.x : 0, a = "left" === a ? o.right.x : 0, o = i.top - l + t > e, t = l - i.bottom - n > e, n = i.left - s + r > e, r = s - i.right - a > e, o) || t || n || r
            }) && (A(), W(e)))
        }

        function X(e) {
            J(e) || 0 <= x.props.trigger.indexOf("click") && m || (x.props.interactive ? x.hideWithInteractivity(e) : W(e))
        }

        function Z(e) {
            x.props.trigger.indexOf("focusin") < 0 && e.target !== L() || x.props.interactive && e.relatedTarget && d.contains(e.relatedTarget) || W(e)
        }

        function J(e) {
            return !!fe.isTouch && H() !== 0 <= e.type.indexOf("touch")
        }

        function G() {
            Q();
            var e = x.props, t = e.popperOptions, n = e.placement, r = e.offset, i = e.getReferenceClientRect,
                e = e.moveTransition, o = E() ? be(d).arrow : null,
                i = i ? {getBoundingClientRect: i, contextElement: i.contextElement || L()} : a,
                r = [{name: "offset", options: {offset: r}}, {
                    name: "preventOverflow",
                    options: {padding: {top: 2, bottom: 2, left: 5, right: 5}}
                }, {name: "flip", options: {padding: 5}}, {
                    name: "computeStyles",
                    options: {adaptive: !e}
                }, {
                    name: "$$tippy", enabled: !0, phase: "beforeWrite", requires: ["computeStyles"], fn: function (e) {
                        var t, n = e.state;
                        E() && (t = M().box, ["placement", "reference-hidden", "escaped"].forEach(function (e) {
                            "placement" === e ? t.setAttribute("data-placement", n.placement) : n.attributes.popper["data-popper-" + e] ? t.setAttribute("data-" + e, "") : t.removeAttribute("data-" + e)
                        }), n.attributes.popper = {})
                    }
                }];
            E() && o && r.push({
                name: "arrow",
                options: {element: o, padding: 3}
            }), r.push.apply(r, (null == t ? void 0 : t.modifiers) || []), x.popperInstance = ee.createPopper(i, d, Object.assign({}, t, {
                placement: n,
                onFirstUpdate: s,
                modifiers: r
            }))
        }

        function Q() {
            x.popperInstance && (x.popperInstance.destroy(), x.popperInstance = null)
        }

        function N() {
            return le(d.querySelectorAll("[data-tippy-root]"))
        }

        function K(e) {
            x.clearDelayTimeouts(), e && O("onTrigger", [x, e]), Y();
            var e = z(!0), t = j(), n = t[0], t = t[1];
            (e = fe.isTouch && "hold" === n && t ? t : e) ? r = setTimeout(function () {
                x.show()
            }, e) : x.show()
        }

        function W(e) {
            x.clearDelayTimeouts(), O("onUntrigger", [x, e]), x.state.isVisible ? 0 <= x.props.trigger.indexOf("mouseenter") && 0 <= x.props.trigger.indexOf("click") && 0 <= ["mouseleave", "mousemove"].indexOf(e.type) && m || ((e = z(!1)) ? t = setTimeout(function () {
                x.state.isVisible && x.hide()
            }, e) : n = requestAnimationFrame(function () {
                x.hide()
            })) : T()
        }
    }

    function y(e, t) {
        var n = ye.plugins.concat((t = void 0 === t ? {} : t).plugins || []),
            r = (i = !(o = e), a = "[object Object]" === Object.prototype.toString.call(o) && !o.addEventListener, ge(i, ["tippy() was passed", "`" + String(o) + "`", "as its targets (first) argument. Valid types are: String, Element,", "Element[], or NodeList."].join(" ")), ge(a, ["tippy() was passed a plain object which is not supported as an argument", "for virtual positioning. Use props.getReferenceClientRect instead."].join(" ")), L(t, n), document.addEventListener("touchstart", h, te), window.addEventListener("blur", b), Object.assign({}, t, {plugins: n})),
            i = u(e), o = s(r.content), a = 1 < i.length,
            t = (ve(o && a, ["tippy() was passed an Element as the `content` prop, but more than", "one tippy instance was created by this invocation. This means the", "content element will only be appended to the last tippy instance.", "\n\n", "Instead, pass the .innerHTML of the element, or use a function that", "returns a cloned version of the element instead.", "\n\n", "1) content: element.innerHTML\n", "2) content: () => element.cloneNode(true)"].join(" ")), i.reduce(function (e, t) {
                t = t && k(t, r);
                return t && e.push(t), e
            }, []));
        return s(e) ? t[0] : t
    }

    y.defaultProps = ye, y.setDefaultProps = function (t) {
        L(t, []), Object.keys(t).forEach(function (e) {
            ye[e] = t[e]
        })
    }, y.currentInput = fe;
    var A = Object.assign({}, ee.applyStyles, {
        effect: function (e) {
            var e = e.state, t = {
                popper: {position: e.options.strategy, left: "0", top: "0", margin: "0"},
                arrow: {position: "absolute"},
                reference: {}
            };
            Object.assign(e.elements.popper.style, t.popper), e.styles = t, e.elements.arrow && Object.assign(e.elements.arrow.style, t.arrow)
        }
    }), C = {mouseover: "mouseenter", focusin: "focus", click: "click"};
    var T = {
        name: "animateFill", defaultValue: !1, fn: function (e) {
            var n, r, i, t;
            return null != (t = e.props.render) && t.$$tippy ? (t = be(e.popper), n = t.box, r = t.content, i = e.props.animateFill ? ((t = ce()).className = o, pe([t], "hidden"), t) : null, {
                onCreate: function () {
                    i && (n.insertBefore(i, n.firstElementChild), n.setAttribute("data-animatefill", ""), n.style.overflow = "hidden", e.setProps({
                        arrow: !1,
                        animation: "shift-away"
                    }))
                }, onMount: function () {
                    var e, t;
                    i && (e = n.style.transitionDuration, t = Number(e.replace("ms", "")), r.style.transitionDelay = Math.round(t / 10) + "ms", i.style.transitionDuration = e, pe([i], "visible"))
                }, onShow: function () {
                    i && (i.style.transitionDuration = "0ms")
                }, onHide: function () {
                    i && pe([i], "hidden")
                }
            }) : (ge(e.props.animateFill, "The `animateFill` plugin requires the default render function."), {})
        }
    };
    var f = {clientX: 0, clientY: 0}, m = [];

    function q(e) {
        var t = e.clientX, e = e.clientY;
        f = {clientX: t, clientY: e}
    }

    var N = {
        name: "followCursor", defaultValue: !1, fn: function (n) {
            var d = n.reference, t = Se(n.props.triggerTarget || d), r = !1, i = !1, e = !0, o = n.props;

            function a() {
                return "initial" === n.props.followCursor && n.state.isVisible
            }

            function s() {
                t.addEventListener("mousemove", u)
            }

            function l() {
                t.removeEventListener("mousemove", u)
            }

            function c() {
                r = !0, n.setProps({getReferenceClientRect: null}), r = !1
            }

            function u(e) {
                var t = !e.target || d.contains(e.target), o = n.props.followCursor, a = e.clientX, s = e.clientY,
                    e = d.getBoundingClientRect(), l = a - e.left, c = s - e.top;
                !t && n.props.interactive || n.setProps({
                    getReferenceClientRect: function () {
                        var e = d.getBoundingClientRect(), t = a, n = s,
                            r = ("initial" === o && (t = e.left + l, n = e.top + c), "horizontal" === o ? e.top : n),
                            i = "vertical" === o ? e.right : t, n = "horizontal" === o ? e.bottom : n,
                            e = "vertical" === o ? e.left : t;
                        return {width: i - e, height: n - r, top: r, right: i, bottom: n, left: e}
                    }
                })
            }

            function p() {
                n.props.followCursor && (m.push({instance: n, doc: t}), t.addEventListener("mousemove", q))
            }

            function h() {
                0 === (m = m.filter(function (e) {
                    return e.instance !== n
                })).filter(function (e) {
                    return e.doc === t
                }).length && t.removeEventListener("mousemove", q)
            }

            return {
                onCreate: p, onDestroy: h, onBeforeUpdate: function () {
                    o = n.props
                }, onAfterUpdate: function (e, t) {
                    t = t.followCursor;
                    r || void 0 !== t && o.followCursor !== t && (h(), t ? (p(), !n.state.isMounted || i || a() || s()) : (l(), c()))
                }, onMount: function () {
                    n.props.followCursor && !i && (e && (u(f), e = !1), a() || s())
                }, onTrigger: function (e, t) {
                    de(t) && (f = {clientX: t.clientX, clientY: t.clientY}), i = "focus" === t.type
                }, onHidden: function () {
                    n.props.followCursor && (c(), l(), e = !0)
                }
            }
        }
    };
    var W = {
        name: "inlinePositioning", defaultValue: !1, fn: function (i) {
            var t, h = i.reference;
            var f = -1, n = !1, r = [], o = {
                name: "tippyInlinePositioning", enabled: !0, phase: "afterWrite", fn: function (e) {
                    var p = e.state;
                    i.props.inlinePositioning && (-1 !== r.indexOf(p.placement) && (r = []), t !== p.placement && -1 === r.indexOf(p.placement) && (r.push(p.placement), i.setProps({
                        getReferenceClientRect: function () {
                            var t = Ee(p.placement), e = h.getBoundingClientRect(), n = le(h.getClientRects()), r = f;
                            if (n.length < 2 || null === t) return e;
                            if (2 === n.length && 0 <= r && n[0].left > n[1].right) return n[r] || e;
                            switch (t) {
                                case "top":
                                case "bottom":
                                    var i = n[0], o = n[n.length - 1], a = "top" === t, s = i.top, l = o.bottom,
                                        c = (a ? i : o).left, a = (a ? i : o).right;
                                    return {top: s, bottom: l, left: c, right: a, width: a - c, height: l - s};
                                case "left":
                                case "right":
                                    var d = Math.min.apply(Math, n.map(function (e) {
                                        return e.left
                                    })), u = Math.max.apply(Math, n.map(function (e) {
                                        return e.right
                                    })), i = n.filter(function (e) {
                                        return "left" === t ? e.left === d : e.right === u
                                    }), o = i[0].top, a = i[i.length - 1].bottom;
                                    return {top: o, bottom: a, left: d, right: u, width: u - d, height: a - o};
                                default:
                                    return e
                            }
                        }
                    })), t = p.placement)
                }
            };

            function e() {
                var e, t;
                n || (e = i.props, t = o, e = {
                    popperOptions: Object.assign({}, e.popperOptions, {
                        modifiers: [].concat(((null == (e = e.popperOptions) ? void 0 : e.modifiers) || []).filter(function (e) {
                            return e.name !== t.name
                        }), [t])
                    })
                }, n = !0, i.setProps(e), n = !1)
            }

            return {
                onCreate: e, onAfterUpdate: e, onTrigger: function (e, t) {
                    var n, r;
                    de(t) && (n = (r = le(i.reference.getClientRects())).find(function (e) {
                        return e.left - 2 <= t.clientX && e.right + 2 >= t.clientX && e.top - 2 <= t.clientY && e.bottom + 2 >= t.clientY
                    }), r = r.indexOf(n), f = -1 < r ? r : f)
                }, onHidden: function () {
                    f = -1
                }
            }
        }
    };
    var t, j, H = {
        name: "sticky", defaultValue: !1, fn: function (n) {
            var r = n.reference, i = n.popper;

            function o(e) {
                return !0 === n.props.sticky || n.props.sticky === e
            }

            var a = null, s = null;

            function l() {
                var e = o("reference") ? (n.popperInstance ? n.popperInstance.state.elements.reference : r).getBoundingClientRect() : null,
                    t = o("popper") ? i.getBoundingClientRect() : null;
                (e && z(a, e) || t && z(s, t)) && n.popperInstance && n.popperInstance.update(), a = e, s = t, n.state.isMounted && requestAnimationFrame(l)
            }

            return {
                onMount: function () {
                    n.props.sticky && l()
                }
            }
        }
    };

    function z(e, t) {
        return !e || !t || e.top !== t.top || e.right !== t.right || e.bottom !== t.bottom || e.left !== t.left
    }

    return e && (e = '.tippy-box[data-animation=fade][data-state=hidden]{opacity:0}[data-tippy-root]{max-width:calc(100vw - 10px)}.tippy-box{position:relative;background-color:#333;color:#fff;border-radius:4px;font-size:14px;line-height:1.4;white-space:normal;outline:0;transition-property:transform,visibility,opacity}.tippy-box[data-placement^=top]>.tippy-arrow{bottom:0}.tippy-box[data-placement^=top]>.tippy-arrow:before{bottom:-7px;left:0;border-width:8px 8px 0;border-top-color:initial;transform-origin:center top}.tippy-box[data-placement^=bottom]>.tippy-arrow{top:0}.tippy-box[data-placement^=bottom]>.tippy-arrow:before{top:-7px;left:0;border-width:0 8px 8px;border-bottom-color:initial;transform-origin:center bottom}.tippy-box[data-placement^=left]>.tippy-arrow{right:0}.tippy-box[data-placement^=left]>.tippy-arrow:before{border-width:8px 0 8px 8px;border-left-color:initial;right:-7px;transform-origin:center left}.tippy-box[data-placement^=right]>.tippy-arrow{left:0}.tippy-box[data-placement^=right]>.tippy-arrow:before{left:-7px;border-width:8px 8px 8px 0;border-right-color:initial;transform-origin:center right}.tippy-box[data-inertia][data-state=visible]{transition-timing-function:cubic-bezier(.54,1.5,.38,1.11)}.tippy-arrow{width:16px;height:16px;color:#333}.tippy-arrow:before{content:"";position:absolute;border-color:transparent;border-style:solid}.tippy-content{position:relative;padding:5px 9px;z-index:1}', (t = document.createElement("style")).textContent = e, t.setAttribute("data-tippy-stylesheet", ""), e = document.head, (j = document.querySelector("head>style,head>link")) ? e.insertBefore(t, j) : e.appendChild(t)), y.setDefaultProps({
        plugins: [T, N, W, H],
        render: O
    }), y.createSingleton = function (e, t) {
        void 0 === t && (t = {}), ge(!Array.isArray(e), ["The first argument passed to createSingleton() must be an array of", "tippy instances. The passed value was", String(e)].join(" "));
        var i, o = e, r = [], a = [], s = t.overrides, n = [], l = !1;

        function c() {
            a = o.map(function (e) {
                return ae(e.props.triggerTarget || e.reference)
            }).reduce(function (e, t) {
                return e.concat(t)
            }, [])
        }

        function d() {
            r = o.map(function (e) {
                return e.reference
            })
        }

        function u(t) {
            o.forEach(function (e) {
                t ? e.enable() : e.disable()
            })
        }

        function p(r) {
            return o.map(function (t) {
                var n = t.setProps;
                return t.setProps = function (e) {
                    n(e), t.reference === i && r.setProps(e)
                }, function () {
                    t.setProps = n
                }
            })
        }

        function h(e, t) {
            var n = a.indexOf(t);
            t !== i && (i = t, t = (s || []).concat("content").reduce(function (e, t) {
                return e[t] = o[n].props[t], e
            }, {}), e.setProps(Object.assign({}, t, {
                getReferenceClientRect: "function" == typeof t.getReferenceClientRect ? t.getReferenceClientRect : function () {
                    var e;
                    return null == (e = r[n]) ? void 0 : e.getBoundingClientRect()
                }
            })))
        }

        u(!1), d(), c();
        var e = {
            fn: function () {
                return {
                    onDestroy: function () {
                        u(!0)
                    }, onHidden: function () {
                        i = null
                    }, onClickOutside: function (e) {
                        e.props.showOnCreate && !l && (l = !0, i = null)
                    }, onShow: function (e) {
                        e.props.showOnCreate && !l && (l = !0, h(e, r[0]))
                    }, onTrigger: function (e, t) {
                        h(e, t.currentTarget)
                    }
                }
            }
        }, f = y(ce(), Object.assign({}, g(t, ["overrides"]), {
            plugins: [e].concat(t.plugins || []),
            triggerTarget: a,
            popperOptions: Object.assign({}, t.popperOptions, {modifiers: [].concat((null == (e = t.popperOptions) ? void 0 : e.modifiers) || [], [A])})
        })), m = f.show, v = (f.show = function (e) {
            var t;
            return m(), i || null != e ? i && null == e ? void 0 : "number" == typeof e ? r[e] && h(f, r[e]) : 0 <= o.indexOf(e) ? (t = e.reference, h(f, t)) : 0 <= r.indexOf(e) ? h(f, e) : void 0 : h(f, r[0])
        }, f.showNext = function () {
            var e = r[0];
            if (!i) return f.show(0);
            var t = r.indexOf(i);
            f.show(r[t + 1] || e)
        }, f.showPrevious = function () {
            var e = r[r.length - 1];
            if (!i) return f.show(e);
            var t = r.indexOf(i), t = r[t - 1] || e;
            f.show(t)
        }, f.setProps);
        return f.setProps = function (e) {
            s = e.overrides || s, v(e)
        }, f.setInstances = function (e) {
            u(!0), n.forEach(function (e) {
                return e()
            }), o = e, u(!1), d(), c(), n = p(f), f.setProps({triggerTarget: a})
        }, n = p(f), f
    }, y.delegate = function (e, r) {
        ge(!(r && r.target), ["You must specity a `target` prop indicating a CSS selector string matching", "the target elements that should receive a tippy."].join(" "));
        var i = [], o = [], a = !1, s = r.target, t = g(r, ["target"]),
            n = Object.assign({}, t, {trigger: "manual", touch: !1}),
            l = Object.assign({touch: ye.touch}, t, {showOnCreate: !0});

        function c(e) {
            var t, n;
            e.target && !a && (t = e.target.closest(s)) && (n = t.getAttribute("data-tippy-trigger") || r.trigger || ye.trigger, t._tippy || "touchstart" === e.type && "boolean" == typeof l.touch || "touchstart" !== e.type && n.indexOf(C[e.type]) < 0 || (n = y(t, l)) && (o = o.concat(n)))
        }

        function d(e, t, n, r) {
            e.addEventListener(t, n, r = void 0 === r ? !1 : r), i.push({node: e, eventType: t, handler: n, options: r})
        }

        return ae(t = y(e, n)).forEach(function (e) {
            var t = e.destroy, n = e.enable, r = e.disable;
            e.destroy = function (e) {
                (e = void 0 === e ? !0 : e) && o.forEach(function (e) {
                    e.destroy()
                }), o = [], i.forEach(function (e) {
                    var t = e.node, n = e.eventType, r = e.handler, e = e.options;
                    t.removeEventListener(n, r, e)
                }), i = [], t()
            }, e.enable = function () {
                n(), o.forEach(function (e) {
                    return e.enable()
                }), a = !1
            }, e.disable = function () {
                r(), o.forEach(function (e) {
                    return e.disable()
                }), a = !0
            }, d(e = (e = e).reference, "touchstart", c, te), d(e, "mouseover", c), d(e, "focusin", c), d(e, "click", c)
        }), t
    }, y.hideAll = function (e) {
        var e = void 0 === e ? {} : e, n = e.exclude, r = e.duration;
        we.forEach(function (e) {
            var t = !1;
            (t = n ? d(n) ? e.reference === n : e.popper === n.popper : t) || (t = e.props.duration, e.setProps({duration: r}), e.hide(), e.state.isDestroyed) || e.setProps({duration: t})
        })
    }, y.roundArrow = '<svg width="16" height="6" xmlns="http://www.w3.org/2000/svg"><path d="M0 6s1.796-.013 4.67-3.615C5.851.9 6.93.006 8 0c1.07-.006 2.148.887 3.343 2.385C14.233 6.005 16 6 16 6H0z"></svg>', y
});
var SimpleBar = function () {
    "use strict";
    var o = function (e, t) {
            return (o = Object.setPrototypeOf || ({__proto__: []} instanceof Array ? function (e, t) {
                e.__proto__ = t
            } : function (e, t) {
                for (var n in t) Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
            }))(e, t)
        }, e = !("undefined" == typeof window || !window.document || !window.document.createElement),
        t = "object" == typeof global && global && global.Object === Object && global,
        n = "object" == typeof self && self && self.Object === Object && self, r = t || n || Function("return this")(),
        t = r.Symbol, n = Object.prototype, a = n.hasOwnProperty, s = n.toString, l = t ? t.toStringTag : void 0,
        c = Object.prototype.toString, d = t ? t.toStringTag : void 0;
    var i = /\s/, u = /^\s+/;

    function y(e) {
        var t = typeof e;
        return null != e && ("object" == t || "function" == t)
    }

    var p = /^[-+]0x[0-9a-f]+$/i, j = /^0b[01]+$/i, H = /^0o[0-7]+$/i, z = parseInt;

    function b(e) {
        if ("number" == typeof e) return e;
        if ("symbol" == typeof (t = e) || null != t && "object" == typeof t && "[object Symbol]" == function (e) {
            if (null == e) return void 0 === e ? "[object Undefined]" : "[object Null]";
            if (d && d in Object(e)) {
                var t = e, n = a.call(t, l), r = t[l];
                try {
                    var i = !(t[l] = void 0)
                } catch (t) {
                }
                var o = s.call(t);
                return i && (n ? t[l] = r : delete t[l]), o
            }
            return c.call(e)
        }(t)) return NaN;
        if ("string" != typeof (e = y(e) ? y(t = "function" == typeof e.valueOf ? e.valueOf() : e) ? t + "" : t : e)) return 0 === e ? e : +e;
        e = (t = e) && t.slice(0, function (e) {
            for (var t = e.length; t-- && i.test(e.charAt(t));) ;
            return t
        }(t) + 1).replace(u, "");
        var t = j.test(e);
        return t || H.test(e) ? z(e.slice(2), t ? 2 : 8) : p.test(e) ? NaN : +e
    }

    var _ = function () {
        return r.Date.now()
    }, P = Math.max, R = Math.min;

    function h(r, n, e) {
        var i, o, a, s, l, c, d = 0, u = !1, p = !1, t = !0;
        if ("function" != typeof r) throw new TypeError("Expected a function");

        function h(e) {
            var t = i, n = o;
            return i = o = void 0, d = e, s = r.apply(n, t)
        }

        function f(e) {
            var t = e - c;
            return void 0 === c || n <= t || t < 0 || p && a <= e - d
        }

        function m() {
            var e, t = _();
            if (f(t)) return v(t);
            l = setTimeout(m, (e = n - (t - c), p ? R(e, a - (t - d)) : e))
        }

        function v(e) {
            return l = void 0, t && i ? h(e) : (i = o = void 0, s)
        }

        function g() {
            var e = _(), t = f(e);
            if (i = arguments, o = this, c = e, t) {
                if (void 0 === l) return d = e = c, l = setTimeout(m, n), u ? h(e) : s;
                if (p) return clearTimeout(l), l = setTimeout(m, n), h(c)
            }
            return void 0 === l && (l = setTimeout(m, n)), s
        }

        return n = b(n) || 0, y(e) && (u = !!e.leading, a = (p = "maxWait" in e) ? P(b(e.maxWait) || 0, n) : a, t = "trailing" in e ? !!e.trailing : t), g.cancel = function () {
            void 0 !== l && clearTimeout(l), i = c = o = l = void (d = 0)
        }, g.flush = function () {
            return void 0 === l ? s : v(_())
        }, g
    }

    var f = function () {
        return (f = Object.assign || function (e) {
            for (var t, n = 1, r = arguments.length; n < r; n++) for (var i in t = arguments[n]) Object.prototype.hasOwnProperty.call(t, i) && (e[i] = t[i]);
            return e
        }).apply(this, arguments)
    }, m = null, v = null;

    function g() {
        if (null === m) {
            if ("undefined" == typeof document) return m = 0;
            var e = document.body, t = document.createElement("div"),
                n = (t.classList.add("simplebar-hide-scrollbar"), e.appendChild(t), t.getBoundingClientRect().right);
            e.removeChild(t), m = n
        }
        return m
    }

    function w(e) {
        return e && e.ownerDocument && e.ownerDocument.defaultView ? e.ownerDocument.defaultView : window
    }

    function x(e) {
        return e && e.ownerDocument ? e.ownerDocument : document
    }

    e && window.addEventListener("resize", function () {
        v !== window.devicePixelRatio && (v = window.devicePixelRatio, m = null)
    });

    function E(e) {
        return Array.prototype.reduce.call(e, function (e, t) {
            var n = t.name.match(/data-simplebar-(.+)/);
            if (n) {
                var r = n[1].replace(/\W+(.)/g, function (e, t) {
                    return t.toUpperCase()
                });
                switch (t.value) {
                    case "true":
                        e[r] = !0;
                        break;
                    case "false":
                        e[r] = !1;
                        break;
                    case void 0:
                        e[r] = !0;
                        break;
                    default:
                        e[r] = t.value
                }
            }
            return e
        }, {})
    }

    function L(e, t) {
        e && (e = e.classList).add.apply(e, t.split(" "))
    }

    function S(t, e) {
        t && e.split(" ").forEach(function (e) {
            t.classList.remove(e)
        })
    }

    function M(e) {
        return ".".concat(e.split(" ").join("."))
    }

    var n = Object.freeze({
        __proto__: null,
        getElementWindow: w,
        getElementDocument: x,
        getOptions: E,
        addClasses: L,
        removeClasses: S,
        classNamesToQuery: M
    }), D = w, O = x, t = E, k = L, A = S, C = M, T = (W.getRtlHelpers = function () {
        if (!W.rtlHelpers) {
            var e = document.createElement("div"),
                e = (e.innerHTML = '<div class="simplebar-dummy-scrollbar-size"><div></div></div>', e.firstElementChild),
                t = null == e ? void 0 : e.firstElementChild;
            if (!t) return null;
            document.body.appendChild(e), e.scrollLeft = 0;
            var n = W.getOffset(e), r = W.getOffset(t), t = (e.scrollLeft = -999, W.getOffset(t));
            document.body.removeChild(e), W.rtlHelpers = {
                isScrollOriginAtZero: n.left !== r.left,
                isScrollingToNegative: r.left !== t.left
            }
        }
        return W.rtlHelpers
    }, W.prototype.getScrollbarWidth = function () {
        try {
            return this.contentWrapperEl && "none" === getComputedStyle(this.contentWrapperEl, "::-webkit-scrollbar").display || "scrollbarWidth" in document.documentElement.style || "-ms-overflow-style" in document.documentElement.style ? 0 : g()
        } catch (e) {
            return g()
        }
    }, W.getOffset = function (e) {
        var t = e.getBoundingClientRect(), n = O(e), e = D(e);
        return {
            top: t.top + (e.pageYOffset || n.documentElement.scrollTop),
            left: t.left + (e.pageXOffset || n.documentElement.scrollLeft)
        }
    }, W.prototype.init = function () {
        e && (this.initDOM(), this.rtlHelpers = W.getRtlHelpers(), this.scrollbarWidth = this.getScrollbarWidth(), this.recalculate(), this.initListeners())
    }, W.prototype.initDOM = function () {
        var e;
        this.wrapperEl = this.el.querySelector(C(this.classNames.wrapper)), this.contentWrapperEl = this.options.scrollableNode || this.el.querySelector(C(this.classNames.contentWrapper)), this.contentEl = this.options.contentNode || this.el.querySelector(C(this.classNames.contentEl)), this.offsetEl = this.el.querySelector(C(this.classNames.offset)), this.maskEl = this.el.querySelector(C(this.classNames.mask)), this.placeholderEl = this.findChild(this.wrapperEl, C(this.classNames.placeholder)), this.heightAutoObserverWrapperEl = this.el.querySelector(C(this.classNames.heightAutoObserverWrapperEl)), this.heightAutoObserverEl = this.el.querySelector(C(this.classNames.heightAutoObserverEl)), this.axis.x.track.el = this.findChild(this.el, "".concat(C(this.classNames.track)).concat(C(this.classNames.horizontal))), this.axis.y.track.el = this.findChild(this.el, "".concat(C(this.classNames.track)).concat(C(this.classNames.vertical))), this.axis.x.scrollbar.el = (null == (e = this.axis.x.track.el) ? void 0 : e.querySelector(C(this.classNames.scrollbar))) || null, this.axis.y.scrollbar.el = (null == (e = this.axis.y.track.el) ? void 0 : e.querySelector(C(this.classNames.scrollbar))) || null, this.options.autoHide || (k(this.axis.x.scrollbar.el, this.classNames.visible), k(this.axis.y.scrollbar.el, this.classNames.visible))
    }, W.prototype.initListeners = function () {
        var e, t, n = this, r = D(this.el);
        this.el.addEventListener("mouseenter", this.onMouseEnter), this.el.addEventListener("pointerdown", this.onPointerEvent, !0), this.el.addEventListener("mousemove", this.onMouseMove), this.el.addEventListener("mouseleave", this.onMouseLeave), null != (t = this.contentWrapperEl) && t.addEventListener("scroll", this.onScroll), r.addEventListener("resize", this.onWindowResize), this.contentEl && (window.ResizeObserver && (e = !1, t = r.ResizeObserver || ResizeObserver, this.resizeObserver = new t(function () {
            e && r.requestAnimationFrame(function () {
                n.recalculate()
            })
        }), this.resizeObserver.observe(this.el), this.resizeObserver.observe(this.contentEl), r.requestAnimationFrame(function () {
            e = !0
        })), this.mutationObserver = new r.MutationObserver(function () {
            r.requestAnimationFrame(function () {
                n.recalculate()
            })
        }), this.mutationObserver.observe(this.contentEl, {childList: !0, subtree: !0, characterData: !0}))
    }, W.prototype.recalculate = function () {
        var e, t, n, r, i, o, a, s;
        this.heightAutoObserverEl && this.contentEl && this.contentWrapperEl && this.wrapperEl && this.placeholderEl && (s = D(this.el), this.elStyles = s.getComputedStyle(this.el), this.isRtl = "rtl" === this.elStyles.direction, s = this.contentEl.offsetWidth, o = this.heightAutoObserverEl.offsetHeight <= 1, a = this.heightAutoObserverEl.offsetWidth <= 1 || 0 < s, e = this.contentWrapperEl.offsetWidth, t = this.elStyles.overflowX, n = this.elStyles.overflowY, this.contentEl.style.padding = "".concat(this.elStyles.paddingTop, " ").concat(this.elStyles.paddingRight, " ").concat(this.elStyles.paddingBottom, " ").concat(this.elStyles.paddingLeft), this.wrapperEl.style.margin = "-".concat(this.elStyles.paddingTop, " -").concat(this.elStyles.paddingRight, " -").concat(this.elStyles.paddingBottom, " -").concat(this.elStyles.paddingLeft), r = this.contentEl.scrollHeight, i = this.contentEl.scrollWidth, this.contentWrapperEl.style.height = o ? "auto" : "100%", this.placeholderEl.style.width = a ? "".concat(s || i, "px") : "auto", this.placeholderEl.style.height = "".concat(r, "px"), o = this.contentWrapperEl.offsetHeight, this.axis.x.isOverflowing = 0 !== s && s < i, this.axis.y.isOverflowing = o < r, this.axis.x.isOverflowing = "hidden" !== t && this.axis.x.isOverflowing, this.axis.y.isOverflowing = "hidden" !== n && this.axis.y.isOverflowing, this.axis.x.forceVisible = "x" === this.options.forceVisible || !0 === this.options.forceVisible, this.axis.y.forceVisible = "y" === this.options.forceVisible || !0 === this.options.forceVisible, this.hideNativeScrollbar(), a = this.axis.x.isOverflowing ? this.scrollbarWidth : 0, s = this.axis.y.isOverflowing ? this.scrollbarWidth : 0, this.axis.x.isOverflowing = this.axis.x.isOverflowing && e - s < i, this.axis.y.isOverflowing = this.axis.y.isOverflowing && o - a < r, this.axis.x.scrollbar.size = this.getScrollbarSize("x"), this.axis.y.scrollbar.size = this.getScrollbarSize("y"), this.axis.x.scrollbar.el && (this.axis.x.scrollbar.el.style.width = "".concat(this.axis.x.scrollbar.size, "px")), this.axis.y.scrollbar.el && (this.axis.y.scrollbar.el.style.height = "".concat(this.axis.y.scrollbar.size, "px")), this.positionScrollbar("x"), this.positionScrollbar("y"), this.toggleTrackVisibility("x"), this.toggleTrackVisibility("y"))
    }, W.prototype.getScrollbarSize = function (e) {
        var t, n;
        return this.axis[e = void 0 === e ? "y" : e].isOverflowing && this.contentEl ? (t = this.contentEl[this.axis[e].scrollSizeAttr], e = null != (n = null == (n = this.axis[e].track.el) ? void 0 : n[this.axis[e].offsetSizeAttr]) ? n : 0, n = Math.max(~~(e / t * e), this.options.scrollbarMinSize), this.options.scrollbarMaxSize ? Math.min(n, this.options.scrollbarMaxSize) : n) : 0
    }, W.prototype.positionScrollbar = function (e) {
        var t, n, r, i, o, a = this.axis[e = void 0 === e ? "y" : e].scrollbar;
        this.axis[e].isOverflowing && this.contentWrapperEl && a.el && this.elStyles && (t = this.contentWrapperEl[this.axis[e].scrollSizeAttr], n = (null == (n = this.axis[e].track.el) ? void 0 : n[this.axis[e].offsetSizeAttr]) || 0, r = parseInt(this.elStyles[this.axis[e].sizeAttr], 10), i = this.contentWrapperEl[this.axis[e].scrollOffsetAttr], i = "x" === e && this.isRtl && null != (o = W.getRtlHelpers()) && o.isScrollOriginAtZero ? -i : i, "x" === e && this.isRtl && (i = null != (o = W.getRtlHelpers()) && o.isScrollingToNegative ? i : -i), o = ~~((n - a.size) * (i / (t - r))), o = "x" === e && this.isRtl ? -o + (n - a.size) : o, a.el.style.transform = "x" === e ? "translate3d(".concat(o, "px, 0, 0)") : "translate3d(0, ".concat(o, "px, 0)"))
    }, W.prototype.toggleTrackVisibility = function (e) {
        var t = this.axis[e = void 0 === e ? "y" : e].track.el, n = this.axis[e].scrollbar.el;
        t && n && this.contentWrapperEl && (this.axis[e].isOverflowing || this.axis[e].forceVisible ? (t.style.visibility = "visible", this.contentWrapperEl.style[this.axis[e].overflowAttr] = "scroll", this.el.classList.add("".concat(this.classNames.scrollable, "-").concat(e))) : (t.style.visibility = "hidden", this.contentWrapperEl.style[this.axis[e].overflowAttr] = "hidden", this.el.classList.remove("".concat(this.classNames.scrollable, "-").concat(e))), this.axis[e].isOverflowing ? n.style.display = "block" : n.style.display = "none")
    }, W.prototype.showScrollbar = function (e) {
        this.axis[e = void 0 === e ? "y" : e].isOverflowing && !this.axis[e].scrollbar.isVisible && (k(this.axis[e].scrollbar.el, this.classNames.visible), this.axis[e].scrollbar.isVisible = !0)
    }, W.prototype.hideScrollbar = function (e) {
        this.axis[e = void 0 === e ? "y" : e].isOverflowing && this.axis[e].scrollbar.isVisible && (A(this.axis[e].scrollbar.el, this.classNames.visible), this.axis[e].scrollbar.isVisible = !1)
    }, W.prototype.hideNativeScrollbar = function () {
        this.offsetEl && (this.offsetEl.style[this.isRtl ? "left" : "right"] = this.axis.y.isOverflowing || this.axis.y.forceVisible ? "-".concat(this.scrollbarWidth, "px") : "0px", this.offsetEl.style.bottom = this.axis.x.isOverflowing || this.axis.x.forceVisible ? "-".concat(this.scrollbarWidth, "px") : "0px")
    }, W.prototype.onMouseMoveForAxis = function (e) {
        var t = this.axis[e = void 0 === e ? "y" : e];
        t.track.el && t.scrollbar.el && (t.track.rect = t.track.el.getBoundingClientRect(), t.scrollbar.rect = t.scrollbar.el.getBoundingClientRect(), this.isWithinBounds(t.track.rect) ? (this.showScrollbar(e), k(t.track.el, this.classNames.hover), (this.isWithinBounds(t.scrollbar.rect) ? k : A)(t.scrollbar.el, this.classNames.hover)) : (A(t.track.el, this.classNames.hover), this.options.autoHide && this.hideScrollbar(e)))
    }, W.prototype.onMouseLeaveForAxis = function (e) {
        A(this.axis[e = void 0 === e ? "y" : e].track.el, this.classNames.hover), A(this.axis[e].scrollbar.el, this.classNames.hover), this.options.autoHide && this.hideScrollbar(e)
    }, W.prototype.onDragStart = function (e, t) {
        void 0 === t && (t = "y");
        var n = O(this.el), r = D(this.el), i = this.axis[t].scrollbar, e = "y" === t ? e.pageY : e.pageX;
        this.axis[t].dragOffset = e - ((null == (e = i.rect) ? void 0 : e[this.axis[t].offsetAttr]) || 0), this.draggedAxis = t, k(this.el, this.classNames.dragging), n.addEventListener("mousemove", this.drag, !0), n.addEventListener("mouseup", this.onEndDrag, !0), null === this.removePreventClickId ? (n.addEventListener("click", this.preventClick, !0), n.addEventListener("dblclick", this.preventClick, !0)) : (r.clearTimeout(this.removePreventClickId), this.removePreventClickId = null)
    }, W.prototype.onTrackClick = function (e, t) {
        var n, r, i, o, a, s = this, l = (void 0 === t && (t = "y"), this.axis[t]);
        this.options.clickOnTrack && l.scrollbar.el && this.contentWrapperEl && (e.preventDefault(), n = D(this.el), this.axis[t].scrollbar.rect = l.scrollbar.el.getBoundingClientRect(), e = null != (l = null == (e = this.axis[t].scrollbar.rect) ? void 0 : e[this.axis[t].offsetAttr]) ? l : 0, l = parseInt(null != (l = null == (l = this.elStyles) ? void 0 : l[this.axis[t].sizeAttr]) ? l : "0px", 10), r = this.contentWrapperEl[this.axis[t].scrollOffsetAttr], i = ("y" === t ? this.mouseY - e : this.mouseX - e) < 0 ? -1 : 1, o = -1 === i ? r - l : r + l, (a = function () {
            s.contentWrapperEl && (-1 === i ? o < r && (r -= 40, s.contentWrapperEl[s.axis[t].scrollOffsetAttr] = r, n.requestAnimationFrame(a)) : r < o && (r += 40, s.contentWrapperEl[s.axis[t].scrollOffsetAttr] = r, n.requestAnimationFrame(a)))
        })())
    }, W.prototype.getContentElement = function () {
        return this.contentEl
    }, W.prototype.getScrollElement = function () {
        return this.contentWrapperEl
    }, W.prototype.removeListeners = function () {
        var e = D(this.el);
        this.el.removeEventListener("mouseenter", this.onMouseEnter), this.el.removeEventListener("pointerdown", this.onPointerEvent, !0), this.el.removeEventListener("mousemove", this.onMouseMove), this.el.removeEventListener("mouseleave", this.onMouseLeave), this.contentWrapperEl && this.contentWrapperEl.removeEventListener("scroll", this.onScroll), e.removeEventListener("resize", this.onWindowResize), this.mutationObserver && this.mutationObserver.disconnect(), this.resizeObserver && this.resizeObserver.disconnect(), this.onMouseMove.cancel(), this.onWindowResize.cancel(), this.onStopScrolling.cancel(), this.onMouseEntered.cancel()
    }, W.prototype.unMount = function () {
        this.removeListeners()
    }, W.prototype.isWithinBounds = function (e) {
        return this.mouseX >= e.left && this.mouseX <= e.left + e.width && this.mouseY >= e.top && this.mouseY <= e.top + e.height
    }, W.prototype.findChild = function (e, t) {
        var n = e.matches || e.webkitMatchesSelector || e.mozMatchesSelector || e.msMatchesSelector;
        return Array.prototype.filter.call(e.children, function (e) {
            return n.call(e, t)
        })[0]
    }, W.rtlHelpers = null, W.defaultOptions = {
        forceVisible: !1,
        clickOnTrack: !0,
        scrollbarMinSize: 25,
        scrollbarMaxSize: 0,
        ariaLabel: "scrollable content",
        classNames: {
            contentEl: "simplebar-content",
            contentWrapper: "simplebar-content-wrapper",
            offset: "simplebar-offset",
            mask: "simplebar-mask",
            wrapper: "simplebar-wrapper",
            placeholder: "simplebar-placeholder",
            scrollbar: "simplebar-scrollbar",
            track: "simplebar-track",
            heightAutoObserverWrapperEl: "simplebar-height-auto-observer-wrapper",
            heightAutoObserverEl: "simplebar-height-auto-observer",
            visible: "simplebar-visible",
            horizontal: "simplebar-horizontal",
            vertical: "simplebar-vertical",
            hover: "simplebar-hover",
            dragging: "simplebar-dragging",
            scrolling: "simplebar-scrolling",
            scrollable: "simplebar-scrollable",
            mouseEntered: "simplebar-mouse-entered"
        },
        scrollableNode: null,
        contentNode: null,
        autoHide: !0
    }, W.getOptions = t, W.helpers = n, W), t = T.helpers, q = t.getOptions, N = t.addClasses, n = function (r) {
        function i() {
            for (var e = [], t = 0; t < arguments.length; t++) e[t] = arguments[t];
            var n = r.apply(this, e) || this;
            return i.instances.set(e[0], n), n
        }

        var e = i, t = r;
        if ("function" != typeof t && null !== t) throw new TypeError("Class extends value " + String(t) + " is not a constructor or null");

        function n() {
            this.constructor = e
        }

        return o(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n), i.initDOMLoadedElements = function () {
            document.removeEventListener("DOMContentLoaded", this.initDOMLoadedElements), window.removeEventListener("load", this.initDOMLoadedElements), Array.prototype.forEach.call(document.querySelectorAll("[data-simplebar]"), function (e) {
                "init" === e.getAttribute("data-simplebar") || i.instances.has(e) || new i(e, q(e.attributes))
            })
        }, i.removeObserver = function () {
            var e;
            null != (e = i.globalObserver) && e.disconnect()
        }, i.prototype.initDOM = function () {
            var e, t, n = this;
            if (!Array.prototype.filter.call(this.el.children, function (e) {
                return e.classList.contains(n.classNames.wrapper)
            }).length) {
                for (this.wrapperEl = document.createElement("div"), this.contentWrapperEl = document.createElement("div"), this.offsetEl = document.createElement("div"), this.maskEl = document.createElement("div"), this.contentEl = document.createElement("div"), this.placeholderEl = document.createElement("div"), this.heightAutoObserverWrapperEl = document.createElement("div"), this.heightAutoObserverEl = document.createElement("div"), N(this.wrapperEl, this.classNames.wrapper), N(this.contentWrapperEl, this.classNames.contentWrapper), N(this.offsetEl, this.classNames.offset), N(this.maskEl, this.classNames.mask), N(this.contentEl, this.classNames.contentEl), N(this.placeholderEl, this.classNames.placeholder), N(this.heightAutoObserverWrapperEl, this.classNames.heightAutoObserverWrapperEl), N(this.heightAutoObserverEl, this.classNames.heightAutoObserverEl); this.el.firstChild;) this.contentEl.appendChild(this.el.firstChild);
                this.contentWrapperEl.appendChild(this.contentEl), this.offsetEl.appendChild(this.contentWrapperEl), this.maskEl.appendChild(this.offsetEl), this.heightAutoObserverWrapperEl.appendChild(this.heightAutoObserverEl), this.wrapperEl.appendChild(this.heightAutoObserverWrapperEl), this.wrapperEl.appendChild(this.maskEl), this.wrapperEl.appendChild(this.placeholderEl), this.el.appendChild(this.wrapperEl), null != (e = this.contentWrapperEl) && e.setAttribute("tabindex", "0"), null != (e = this.contentWrapperEl) && e.setAttribute("role", "region"), null != (e = this.contentWrapperEl) && e.setAttribute("aria-label", this.options.ariaLabel)
            }
            this.axis.x.track.el && this.axis.y.track.el || (e = document.createElement("div"), t = document.createElement("div"), N(e, this.classNames.track), N(t, this.classNames.scrollbar), e.appendChild(t), this.axis.x.track.el = e.cloneNode(!0), N(this.axis.x.track.el, this.classNames.horizontal), this.axis.y.track.el = e.cloneNode(!0), N(this.axis.y.track.el, this.classNames.vertical), this.el.appendChild(this.axis.x.track.el), this.el.appendChild(this.axis.y.track.el)), T.prototype.initDOM.call(this), this.el.setAttribute("data-simplebar", "init")
        }, i.prototype.unMount = function () {
            T.prototype.unMount.call(this), i.instances.delete(this.el)
        }, i.initHtmlApi = function () {
            this.initDOMLoadedElements = this.initDOMLoadedElements.bind(this), "undefined" != typeof MutationObserver && (this.globalObserver = new MutationObserver(i.handleMutations), this.globalObserver.observe(document, {
                childList: !0,
                subtree: !0
            })), "complete" === document.readyState || "loading" !== document.readyState && !document.documentElement.doScroll ? window.setTimeout(this.initDOMLoadedElements) : (document.addEventListener("DOMContentLoaded", this.initDOMLoadedElements), window.addEventListener("load", this.initDOMLoadedElements))
        }, i.handleMutations = function (e) {
            e.forEach(function (e) {
                e.addedNodes.forEach(function (e) {
                    1 === e.nodeType && (e.hasAttribute("data-simplebar") ? !i.instances.has(e) && document.documentElement.contains(e) && new i(e, q(e.attributes)) : e.querySelectorAll("[data-simplebar]").forEach(function (e) {
                        "init" !== e.getAttribute("data-simplebar") && !i.instances.has(e) && document.documentElement.contains(e) && new i(e, q(e.attributes))
                    }))
                }), e.removedNodes.forEach(function (e) {
                    1 === e.nodeType && ("init" === e.getAttribute("data-simplebar") ? i.instances.has(e) && !document.documentElement.contains(e) && i.instances.get(e).unMount() : Array.prototype.forEach.call(e.querySelectorAll('[data-simplebar="init"]'), function (e) {
                        i.instances.has(e) && !document.documentElement.contains(e) && i.instances.get(e).unMount()
                    }))
                })
            })
        }, i.instances = new WeakMap, i
    }(T);

    function W(e, t) {
        void 0 === t && (t = {});
        var a = this;
        if (this.removePreventClickId = null, this.minScrollbarWidth = 20, this.stopScrollDelay = 175, this.isScrolling = !1, this.isMouseEntering = !1, this.scrollXTicking = !1, this.scrollYTicking = !1, this.wrapperEl = null, this.contentWrapperEl = null, this.contentEl = null, this.offsetEl = null, this.maskEl = null, this.placeholderEl = null, this.heightAutoObserverWrapperEl = null, this.heightAutoObserverEl = null, this.rtlHelpers = null, this.scrollbarWidth = 0, this.resizeObserver = null, this.mutationObserver = null, this.elStyles = null, this.isRtl = null, this.mouseX = 0, this.mouseY = 0, this.onMouseMove = function () {
        }, this.onWindowResize = function () {
        }, this.onStopScrolling = function () {
        }, this.onMouseEntered = function () {
        }, this.onScroll = function () {
            var e = D(a.el);
            a.scrollXTicking || (e.requestAnimationFrame(a.scrollX), a.scrollXTicking = !0), a.scrollYTicking || (e.requestAnimationFrame(a.scrollY), a.scrollYTicking = !0), a.isScrolling || (a.isScrolling = !0, k(a.el, a.classNames.scrolling)), a.showScrollbar("x"), a.showScrollbar("y"), a.onStopScrolling()
        }, this.scrollX = function () {
            a.axis.x.isOverflowing && a.positionScrollbar("x"), a.scrollXTicking = !1
        }, this.scrollY = function () {
            a.axis.y.isOverflowing && a.positionScrollbar("y"), a.scrollYTicking = !1
        }, this._onStopScrolling = function () {
            A(a.el, a.classNames.scrolling), a.options.autoHide && (a.hideScrollbar("x"), a.hideScrollbar("y")), a.isScrolling = !1
        }, this.onMouseEnter = function () {
            a.isMouseEntering || (k(a.el, a.classNames.mouseEntered), a.showScrollbar("x"), a.showScrollbar("y"), a.isMouseEntering = !0), a.onMouseEntered()
        }, this._onMouseEntered = function () {
            A(a.el, a.classNames.mouseEntered), a.options.autoHide && (a.hideScrollbar("x"), a.hideScrollbar("y")), a.isMouseEntering = !1
        }, this._onMouseMove = function (e) {
            a.mouseX = e.clientX, a.mouseY = e.clientY, (a.axis.x.isOverflowing || a.axis.x.forceVisible) && a.onMouseMoveForAxis("x"), (a.axis.y.isOverflowing || a.axis.y.forceVisible) && a.onMouseMoveForAxis("y")
        }, this.onMouseLeave = function () {
            a.onMouseMove.cancel(), (a.axis.x.isOverflowing || a.axis.x.forceVisible) && a.onMouseLeaveForAxis("x"), (a.axis.y.isOverflowing || a.axis.y.forceVisible) && a.onMouseLeaveForAxis("y"), a.mouseX = -1, a.mouseY = -1
        }, this._onWindowResize = function () {
            a.scrollbarWidth = a.getScrollbarWidth(), a.hideNativeScrollbar()
        }, this.onPointerEvent = function (e) {
            var t, n;
            a.axis.x.track.el && a.axis.y.track.el && a.axis.x.scrollbar.el && a.axis.y.scrollbar.el && (a.axis.x.track.rect = a.axis.x.track.el.getBoundingClientRect(), a.axis.y.track.rect = a.axis.y.track.el.getBoundingClientRect(), (a.axis.x.isOverflowing || a.axis.x.forceVisible) && (t = a.isWithinBounds(a.axis.x.track.rect)), (a.axis.y.isOverflowing || a.axis.y.forceVisible) && (n = a.isWithinBounds(a.axis.y.track.rect)), t || n) && (e.stopPropagation(), "pointerdown" === e.type) && "touch" !== e.pointerType && (t && (a.axis.x.scrollbar.rect = a.axis.x.scrollbar.el.getBoundingClientRect(), a.isWithinBounds(a.axis.x.scrollbar.rect) ? a.onDragStart(e, "x") : a.onTrackClick(e, "x")), n) && (a.axis.y.scrollbar.rect = a.axis.y.scrollbar.el.getBoundingClientRect(), a.isWithinBounds(a.axis.y.scrollbar.rect) ? a.onDragStart(e, "y") : a.onTrackClick(e, "y"))
        }, this.drag = function (e) {
            var t, n, r, i, o;
            a.draggedAxis && a.contentWrapperEl && (t = null != (t = null == (t = (o = a.axis[a.draggedAxis].track).rect) ? void 0 : t[a.axis[a.draggedAxis].sizeAttr]) ? t : 0, n = a.axis[a.draggedAxis].scrollbar, r = null != (r = null == (r = a.contentWrapperEl) ? void 0 : r[a.axis[a.draggedAxis].scrollSizeAttr]) ? r : 0, i = parseInt(null != (i = null == (i = a.elStyles) ? void 0 : i[a.axis[a.draggedAxis].sizeAttr]) ? i : "0px", 10), e.preventDefault(), e.stopPropagation(), e = ("y" === a.draggedAxis ? e.pageY : e.pageX) - (null != (e = null == (e = o.rect) ? void 0 : e[a.axis[a.draggedAxis].offsetAttr]) ? e : 0) - a.axis[a.draggedAxis].dragOffset, o = (e = "x" === a.draggedAxis && a.isRtl ? (null != (o = null == (o = o.rect) ? void 0 : o[a.axis[a.draggedAxis].sizeAttr]) ? o : 0) - n.size - e : e) / (t - n.size) * (r - i), "x" === a.draggedAxis && a.isRtl && (o = null != (e = W.getRtlHelpers()) && e.isScrollingToNegative ? -o : o), a.contentWrapperEl[a.axis[a.draggedAxis].scrollOffsetAttr] = o)
        }, this.onEndDrag = function (e) {
            var t = O(a.el), n = D(a.el);
            e.preventDefault(), e.stopPropagation(), A(a.el, a.classNames.dragging), t.removeEventListener("mousemove", a.drag, !0), t.removeEventListener("mouseup", a.onEndDrag, !0), a.removePreventClickId = n.setTimeout(function () {
                t.removeEventListener("click", a.preventClick, !0), t.removeEventListener("dblclick", a.preventClick, !0), a.removePreventClickId = null
            })
        }, this.preventClick = function (e) {
            e.preventDefault(), e.stopPropagation()
        }, this.el = e, this.options = f(f({}, W.defaultOptions), t), this.classNames = f(f({}, W.defaultOptions.classNames), t.classNames), this.axis = {
            x: {
                scrollOffsetAttr: "scrollLeft",
                sizeAttr: "width",
                scrollSizeAttr: "scrollWidth",
                offsetSizeAttr: "offsetWidth",
                offsetAttr: "left",
                overflowAttr: "overflowX",
                dragOffset: 0,
                isOverflowing: !0,
                forceVisible: !1,
                track: {size: null, el: null, rect: null, isVisible: !1},
                scrollbar: {size: null, el: null, rect: null, isVisible: !1}
            },
            y: {
                scrollOffsetAttr: "scrollTop",
                sizeAttr: "height",
                scrollSizeAttr: "scrollHeight",
                offsetSizeAttr: "offsetHeight",
                offsetAttr: "top",
                overflowAttr: "overflowY",
                dragOffset: 0,
                isOverflowing: !0,
                forceVisible: !1,
                track: {size: null, el: null, rect: null, isVisible: !1},
                scrollbar: {size: null, el: null, rect: null, isVisible: !1}
            }
        }, "object" != typeof this.el || !this.el.nodeName) throw new Error("Argument passed to SimpleBar must be an HTML element instead of ".concat(this.el));
        this.onMouseMove = function (e, t) {
            var n = !0, r = !0;
            if ("function" != typeof e) throw new TypeError("Expected a function");
            return y(t) && (n = "leading" in t ? !!t.leading : n, r = "trailing" in t ? !!t.trailing : r), h(e, 64, {
                leading: n,
                maxWait: 64,
                trailing: r
            })
        }(this._onMouseMove), this.onWindowResize = h(this._onWindowResize, 64, {leading: !0}), this.onStopScrolling = h(this._onStopScrolling, this.stopScrollDelay), this.onMouseEntered = h(this._onMouseEntered, this.stopScrollDelay), this.init()
    }

    return e && n.initHtmlApi(), n
}();

function humanFileSize(e, t = !1, n = 1) {
    var r = t ? 1e3 : 1024;
    if (Math.abs(e) < r) return e + " B";
    var i = t ? ["kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"] : ["KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"];
    let o = -1;
    for (var a = 10 ** n; e /= r, ++o, Math.round(Math.abs(e) * a) / a >= r && o < i.length - 1;) ;
    return e.toFixed(n) + " " + i[o]
}

function debounce(n, r) {
    return function (...e) {
        var t = this.lastCall;
        this.lastCall = Date.now(), t && this.lastCall - t <= r && clearTimeout(this.lastCallTimer), this.lastCallTimer = setTimeout(() => n(...e), r)
    }
}

const isElementVisible = function (e) {
    const t = e.clientHeight, n = window.pageYOffset + e.getBoundingClientRect().top,
        r = window.pageXOffset + e.getBoundingClientRect().left,
        i = window.pageXOffset + e.getBoundingClientRect().right,
        o = window.pageYOffset + e.getBoundingClientRect().bottom, a = window.pageYOffset, s = window.pageXOffset,
        l = window.pageXOffset + document.documentElement.clientWidth,
        c = window.pageYOffset + document.documentElement.clientHeight;
    return o + t > a && n + t < c && i + t > s && r + t < l
};

function stripUnit(e) {
    return e / (0 * e + 1)
}

function vw(e, t = 1920) {
    return stripUnit(e) / stripUnit(t) * 100 + "vw"
}

function vwh(e, t = 1366) {
    return stripUnit(e) / stripUnit(t) * 100 + "vw"
}

function vwt(e, t = 768) {
    return stripUnit(e) / stripUnit(t) * 100 + "vw"
}

function vwm(e, t = 375) {
    return stripUnit(e) / stripUnit(t) * 100 + "vw"
}

function getCoords(e) {
    e = e.getBoundingClientRect();
    return {
        top: e.top + window.pageYOffset,
        right: e.right + window.pageXOffset,
        bottom: e.bottom + window.pageYOffset,
        left: e.left + window.pageXOffset
    }
}

(() => {
    var e = document.querySelector(".filter__menu-open");
    const t = document.querySelector(".filter__menu");
    var n, r = document.querySelector(".filter__menu-close"), i = document.querySelector(".filter__menu-mask");
    t && (n = () => {
        t.classList.remove("filter__menu--active")
    }, e.addEventListener("click", () => {
        t.classList.add("filter__menu--active")
    }), r.addEventListener("click", n), i.addEventListener("click", n))
})(), (() => {
    const e = document.querySelector(".sidebar-modal");
    var t, n = document.querySelector(".sidebar-modal__close"), r = document.querySelector(".sidebar-modal__mask"),
        i = document.querySelector(".mobilebar-open-menu");
    e && (t = () => {
        e.classList.remove("sidebar-modal--active")
    }, i.addEventListener("click", () => {
        e.classList.add("sidebar-modal--active")
    }), n.addEventListener("click", t), r.addEventListener("click", t))
})(), window.onload = () => {
    {
        const n = document.querySelectorAll(".form-control__dropdown");
        n.forEach(n => {
            const r = n.querySelector(".form-control__dropdown-input");
            var e = n.querySelector(".form-control__dropdown-top");
            const i = n.querySelector(".form-control__dropdown-current");
            var t = n.querySelectorAll(".form-control__dropdown-item");
            const o = n.querySelector(".form-control__dropdown-list");
            var a = n.clientWidth, s = o.clientWidth;
            r?.value && t.forEach(e => {
                e.getAttribute("data-value") === r.value && (i.innerHTML = e.innerHTML)
            }), o.style.width = a < s ? "max-content" : "100%", i.style.maxWidth = "" + vw(n.offsetWidth - 24), window.matchMedia("(max-width: 1401px)").matches && (i.style.maxWidth = "" + vwh(n.offsetWidth - 24)), window.matchMedia("(max-width: 991px)").matches && (i.style.maxWidth = "" + vwt(n.offsetWidth - 24)), window.matchMedia("(max-width: 576px)").matches && (i.style.maxWidth = "" + vwm(n.offsetWidth - 24)), t.forEach(t => {
                t.addEventListener("click", () => {
                    var e = t.getAttribute("data-value");
                    r.value = e, r.dispatchEvent(new Event("change")), i.innerHTML = t.innerHTML, n.classList.remove("form-control__dropdown--list-top"), n.classList.remove("form-control__dropdown--active")
                })
            }), e.addEventListener("click", () => {
                n.classList.toggle("form-control__dropdown--active"), isElementVisible(o) ? n.classList.remove("form-control__dropdown--list-top") : n.classList.add("form-control__dropdown--list-top")
            })
        }), document.addEventListener("click", t => {
            n.forEach(e => {
                e.contains(t.target) || (e.classList.remove("form-control__dropdown--active"), e.classList.remove("form-control__dropdown--list-top"))
            })
        })
    }
    {
        const r = document.querySelector(".table-top-form");
        var e, t;
        r && (e = r.querySelectorAll(".form-control__dropdown"), t = r.querySelectorAll(".form-control__input"), e.forEach(e => {
            e.querySelectorAll(".form-control__dropdown-item").forEach(e => {
                e.addEventListener("click", () => {
                    r.submit()
                })
            })
        }), t.forEach(e => {
            e.addEventListener("keydown", e => {
                "Enter" === e.key && r.submit()
            })
        }))
    }
}, document.querySelectorAll(".notification").forEach(e => {
    e.querySelector(".notification__close").addEventListener("click", () => {
        e.classList.remove("notification--active")
    })
});
let vh = .01 * window.innerHeight;
document.documentElement.style.setProperty("--vh", vh + "px"), window.addEventListener("load", () => {
    var e = window.innerHeight;
    window.matchMedia("(max-width: 991px").matches && (document.body.style.height = e + "px")
}), window.addEventListener("resize", () => {
    var e = window.innerHeight;
    window.matchMedia("(max-width: 991px").matches && (document.body.style.height = e + "px")
}), (() => {
    const e = document.querySelector(".content__head-toggle"), t = document.querySelector(".content__head-btns");
    e && e.addEventListener("click", () => {
        e.classList.toggle("content__head-toggle--active"), t.classList.toggle("content__head-btns--active")
    })
})(), (() => {
    const n = document.querySelector(".sidebar");
    if (n) {
        var e = document.querySelector(".sidebar__btn");
        const l = [...document.querySelectorAll(".menu__item"), ...document.querySelectorAll(".menu__sub")];
        var t, r, i, o, a = JSON.parse(localStorage.getItem("currentUserLocation")) || null;
        let s = !1;
        l.forEach((i, o) => {
            var e = i.querySelector(".menu__item-link") || i.querySelector(".menu__sub-link"),
                t = i.querySelectorAll(".menu__sub");
            const n = i.querySelector(".menu__list");
            var r = i.querySelectorAll(".menu__list-item");
            const a = n?.clientHeight ? n.clientHeight + 8 : 0;
            r.forEach((e, t) => {
                e.addEventListener("click", () => {
                    console.log(e), e.parentElement.getAttribute("data-menu-level") <= 3 && (console.log("click"), localStorage.setItem("currentUserLocation", JSON.stringify({
                        menuIndex: o,
                        subIndex: null,
                        linkIndex: t
                    })))
                })
            }), t.forEach((e, r) => {
                e = e ? e.querySelectorAll(".menu__list-item") : [];
                e.length && e.forEach((e, t) => {
                    var n = e.parentElement.getAttribute("data-menu-level");
                    i.classList.contains("menu__item") && 4 <= n && e.addEventListener("click", () => {
                        localStorage.setItem("currentUserLocation", JSON.stringify({
                            menuIndex: o,
                            subIndex: r,
                            linkIndex: t
                        }))
                    })
                })
            }), n ? (n.style.maxHeight = "0", e.addEventListener("click", () => {
                s ? i.classList.toggle("menu__item--active-icon") : i.classList.toggle("active"), i.classList.contains("active") ? (n.style = `max-height: ${a}px`, n.setAttribute("data-height", a)) : n.style = "max-height: 0px"
            })) : e.addEventListener("click", () => {
                s ? i.classList.toggle("menu__item--active-icon") : i.classList.toggle("active")
            })
        }), e && e.addEventListener("click", () => {
            n.classList.toggle("sidebar--closed"), s = !s, l.forEach(e => {
                var t = e.querySelector(".menu__list");
                n.classList.contains("sidebar--closed") ? e.classList.contains("active") && (e.classList.remove("active"), t.style = "max-height: 0px", e.classList.add("menu__item--active-icon")) : e.classList.contains("menu__item--active-icon") && (e.classList.add("active"), e.classList.remove("menu__item--active-icon"), t.style = `max-height: ${t.getAttribute("data-height")}px`)
            })
        }), a && ({
            menuIndex: e,
            subIndex: a,
            linkIndex: t
        } = a, null !== a ? (r = (a = (i = (o = document.querySelectorAll(".menu__item")[e]).querySelector(".menu__list")).querySelectorAll(".menu__sub")[a]).querySelectorAll(".menu__list-item")[t], i.style = "max-height: unset", a.querySelector(".menu__list").style = "max-height: unset", o.classList.add("active"), a.classList.add("active"), r.classList.add("menu__list-item--active")) : (o = (i = document.querySelectorAll(".menu__item")[e]).querySelector(".menu__list"), a = i.querySelectorAll(".menu__list-item")[t], i.classList.add("active"), a.classList.add("menu__list-item--active"), o && (o.style = "max-height: unset"))), window.innerWidth < 1401 && 991 < window.innerWidth && (n.classList.add("sidebar--closed"), s = !0, l.forEach(e => {
            var t = e.querySelector(".menu__list");
            e.classList.contains("active") && (e.classList.remove("active"), t.style = "max-height: 0px", e.classList.add("menu__item--active-icon"))
        }))
    }
})(), document.querySelectorAll(".form-control__input[type=password]").forEach(e => {
    const t = e.parentElement;
    t.querySelector(".form-control-show-pass").addEventListener("click", function () {
        "password" === e.getAttribute("type") ? (e.setAttribute("type", "text"), t.classList.add("form-control--shown-pass")) : (t.classList.remove("form-control--shown-pass"), e.setAttribute("type", "password"))
    })
}), (() => {
    var e = document.querySelectorAll(".form-control__input"), t = document.querySelectorAll(".form-control__textarea");
    [...e, ...t].forEach(e => {
        e.oninput = function () {
            e.classList.contains("form-control__textarea") ? (this.style.height = "auto", this.style.height = vw(this.scrollHeight)) : this.value ? e.classList.add("form-control__input--writing") : e.classList.remove("form-control__input--writing")
        }
    })
})();
const initializeDropArea = (e, t) => {
        const n = e.querySelector(".drop-area");

        function r(e) {
            e.preventDefault(), e.stopPropagation()
        }

        function i(e) {
            n.classList.add("highlight")
        }

        function o(e) {
            n.classList.remove("highlight")
        }

        ["dragenter", "dragover", "dragleave", "drop"].forEach(e => {
            n.addEventListener(e, r, !1), document.body.addEventListener(e, r, !1)
        }), ["dragenter", "dragover"].forEach(e => {
            n.addEventListener(e, i, !1)
        }), ["dragleave", "drop"].forEach(e => {
            n.addEventListener(e, o, !1)
        }), n.addEventListener("drop", t, !1)
    }, calendar = (document.querySelectorAll(".form-control__files").forEach(e => {
        const t = e.querySelector(".form-control__files-input"), r = e.querySelector(".form-control__files-uploaded"),
            n = e.querySelector(".form-control__files-head"), i = e.querySelector(".progressbar");
        e.querySelector(".progressbar__progress"), e.querySelector(".btn-upload");
        let f = [];
        const o = function (e) {
            var {name: e, size: t, src: n, idx: r} = e;
            const i = document.createElement("div");
            var o = document.createElement("div"), a = document.createElement("img"), s = document.createElement("div"),
                l = document.createElement("div"), c = document.createElement("div"), d = document.createElement("div"),
                u = document.createElement("button"), p = document.createElement("div"), h = document.createElement("div");
            return p.innerHTML = `
                <div class="progressbar__bg">
                    <div class="progressbar__progress"></div>
                </div>
            `, o.appendChild(a), h.appendChild(l), h.appendChild(c), d.appendChild(u), s.appendChild(p), s.appendChild(h), s.appendChild(d), i.appendChild(o), i.appendChild(s), i.classList.add("form-control__files-item"), o.classList.add("form-control__files-item-img"), s.classList.add("form-control__files-item-info"), h.classList.add("form-control__files-item-text"), l.classList.add("form-control__files-item-title"), c.classList.add("form-control__files-item-size"), d.classList.add("form-control__files-item-btns"), p.classList.add("form-control__files-item-progressbar", "progressbar"), u.classList.add("files-item-remove-file", "BtnSecondarySm", "BtnIconLeft"), i.setAttribute("data-id", r), a.setAttribute("src", n), u.addEventListener("click", () => {
                var e = i;
                e.remove(), e = e.getAttribute("data-id"), f.splice(e, 1)
            }), c.innerHTML = humanFileSize(t), l.innerHTML = e, u.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                        fill="#0D0D0D" />
                </svg>
                <span>Удалить</span>
            `, i
        };
        initializeDropArea(e, e => {
            e.preventDefault();
            var t = e.dataTransfer.files;
            e.dataTransfer.items ? a([...t]) : [...e.dataTransfer.files].forEach((e, t) => {
            })
        });
        const a = e => {
            n.style.display = "none", i.style.display = "block";
            for (let e = r.querySelectorAll(".form-control__files-item").length; e < f.length; e++) ((t, n) => {
                var e = new FileReader;
                e.onload = function (e) {
                    e = {src: e.target.result, name: t.name, size: t.size, idx: n};
                    r.insertAdjacentElement("beforeend", o(e))
                }, e.readAsDataURL(t)
            })(f[e], e);
            n.style.display = "block", i.style.display = "none"
        };
        t.addEventListener("change", () => {
            var e = Array.from(t.files);
            f = [...f, ...e], console.log(f), a(e)
        })
    }), document.querySelectorAll(".form-control__file").forEach(r => {
        const i = r.querySelector(".form-control__file-input"), o = r.querySelector(".form-control__file-img"),
            a = r.querySelector(".form-control__file-title"), s = r.querySelector(".form-control__file-size"),
            l = r.querySelector("input[type=hidden]"), c = r.querySelector(".form-control__file-progressbar"),
            d = r.querySelector(".form-control__file-progressbar .progressbar__progress"),
            u = r.querySelector(".form-control__file-head");
        var e = r.querySelector(".btn-file-reset");
        r.querySelector(".btn-file-upload"), r.querySelector(".btn-file-choose");
        const p = () => {
            var e = new FormData,
                t = (e.append("file", i.files[0]), c.style.display = "flex", u.style.display = "none", new XMLHttpRequest);
            t.upload.addEventListener("progress", function (e) {
                var t = i.files[0].size;
                e.loaded <= t && (t = Math.round(e.loaded / t * 100), d.style.width = t + "%", d.innerHTML = percent + "%"), e.loaded == e.total && (d.style.width = "100%", u.style = null, c.style.display = "none")
            }), t.addEventListener("load", e => {
                200 == e.target.status && (e = e.target.responseText, u.style = null, c.style.display = "none", l.value = e)
            }), t.open("POST", "?action=upload_file"), t.timeout = 45e3, t.send(e)
        }, h = () => {
            var e = new FormData;
            const t = l.dataset.path;
            e.append("path", t), e.append("img", i.files[0]), c.style.display = "flex", u.style.display = "none";
            var n = new XMLHttpRequest;
            n.upload.addEventListener("progress", function (e) {
                var t = i.files[0].size;
                e.loaded <= t && (t = Math.round(e.loaded / t * 100), d.style.width = t + "%", d.innerHTML = percent + "%"), e.loaded == e.total && (d.style.width = "100%")
            }), n.addEventListener("load", e => {
                200 == e.target.status && (e = e.target.responseText, l.value = e, o.setAttribute("src", "/uf/images/" + t + "source/" + e), u.style = null, c.style.display = "none")
            }), n.open("POST", "?action=upload_img"), n.timeout = 45e3, n.send(e)
        };
        initializeDropArea(r, e => {
            e.preventDefault();
            var t, n = e.dataTransfer.files;
            i.files = n, e.dataTransfer.items ? (n = [...n], r.classList.add("form-control__file--active"), t = new FileReader, a.innerHTML = n[0].name, s.innerHTML = humanFileSize(n[0].size), s.style.display = "block", t.onload = function (e) {
            }, t.readAsDataURL(n[0]), (o ? h : p)()) : [...e.dataTransfer.files].forEach((e, t) => {
            })
        }), e && e.addEventListener("click", () => {
            i.value = "", r.classList.remove("form-control__file--active"), o?.setAttribute("src", ""), s.style.display = "none", a.innerHTML = "Перетащите или загрузите файл", c.style = null, d.style = null, u.style = null
        }), i.addEventListener("change", function (e) {
            var t;
            this.files && this.files[0] && (r.classList.add("form-control__file--active"), t = new FileReader, a.innerHTML = this.files[0].name, s.innerHTML = humanFileSize(this.files[0].size), s.style.display = "block", t.onload = function (e) {
            }, t.readAsDataURL(this.files[0]), (o ? h : p)())
        })
    }), (() => {
        const e = document.querySelector(".modal-account");
        var t, n;
        e && (t = document.getElementById("modal-account-btn-close"), n = document.getElementById("modal-account-btn-open"), document.querySelector(".modal-account__mask").addEventListener("click", () => {
            e.classList.remove("modal-account--active")
        }), n.addEventListener("click", () => {
            e.classList.add("modal-account--active")
        }), t.addEventListener("click", () => {
            e.classList.remove("modal-account--active")
        }))
    })(), (() => {
        const t = document.querySelector(".modal-help");
        var e, n;
        t && (e = t.querySelector(".modal-help__mask"), n = t.querySelector(".modal-help__close"), document.querySelectorAll(".modal-help-open").forEach(e => {
            e.addEventListener("click", () => {
                t.classList.add("modal-help--active"), document.querySelector(".sidebar-modal").classList.remove("sidebar-modal--active")
            })
        }), e.addEventListener("click", () => {
            t.classList.remove("modal-help--active")
        }), n.addEventListener("click", () => {
            t.classList.remove("modal-help--active")
        }))
    })(), (() => {
        const t = document.querySelector(".modal-info");
        var e, n;
        t && (e = t.querySelector(".modal-info__mask"), n = t.querySelector(".modal-info__close"), document.querySelectorAll(".btn-modal-info").forEach(e => e.addEventListener("click", () => {
            t.classList.add("modal-info--active")
        })), e.addEventListener("click", () => {
            t.classList.remove("modal-info--active")
        }), n.addEventListener("click", () => {
            t.classList.remove("modal-info--active")
        }))
    })(), (() => {
        const t = document.querySelector(".modal-context");
        var e, n, r;
        t && (e = t.querySelector(".modal-context__mask"), n = t.querySelector(".modal-context__close"), r = t.querySelector(".modal-context-good"), document.querySelectorAll(".btn-modal-context").forEach(e => e.addEventListener("click", () => {
            t.classList.add("modal-context--active")
        })), e.addEventListener("click", () => {
            t.classList.remove("modal-context--active")
        }), n.addEventListener("click", () => {
            t.classList.remove("modal-context--active")
        }), r.addEventListener("click", () => {
            t.classList.remove("modal-context--active")
        }))
    })(), (() => {
        const t = document.querySelector(".modal-point");
        var e, n;
        t && (e = t.querySelector(".modal-point__mask"), n = t.querySelector(".modal-point__close"), document.querySelectorAll(".btn-modal-point").forEach(e => e.addEventListener("click", () => {
            t.classList.add("modal-point--active")
        })), e.addEventListener("click", () => {
            t.classList.remove("modal-point--active")
        }), n.addEventListener("click", () => {
            t.classList.remove("modal-point--active")
        }))
    })(), new datedreamer.calendar({
        element: "#calendar",
        format: "YYYY-MM-DD",
        iconNext: `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M9 11L12 14L15 11" stroke="#0D0D0D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    `,
        iconPrev: `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M9 11L12 14L15 11" stroke="#0D0D0D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    `,
        inputLabel: "Set a date",
        inputPlaceholder: "Enter a date",
        hideInputs: !0,
        darkMode: !1,
        theme: "unstyled",
        styles: `
        

        .datedreamer__calendar {
            border-radius: 0.208vw;
            box-shadow: 0 0 0.78125vw rgba(41, 108, 204, 0.2);
            padding: 0.625vw;

        } 
        @media screen and (max-width: 1401px) {
            .datedreamer__calendar {
                border-radius: 0.286vw;
                box-shadow: 0 0 1.071vw rgba(41, 108, 204, 0.2);
                padding: 0.857vw;
            }
        }
        @media screen and (max-width: 991px) {
            .datedreamer__calendar {
                border-radius: 0.404vw;
                box-shadow: 0 0 1.514vw rgba(41, 108, 204, 0.2);
                padding: 1.211vw;
            }
        }
        @media screen and (max-width: 576px) {
            .datedreamer__calendar {
                border-radius: 0.694vw;
                box-shadow: 0 0 02.604vw rgba(41, 108, 204, 0.2);
                padding: 2.083vw;
            }
        }

        .datedreamer__calendar {
            max-width: fit-content;
        }
        
        .datedreamer__calendar_days {
            gap: 0.208vw;
            margin-top: 0.625vw;
        }

        @media screen and (max-width: 1401px) {
            .datedreamer__calendar_days {
                margin-top: 0.857vw;
                gap: 0.286vw;
            }
        }

        @media screen and (max-width: 991px) {
            .datedreamer__calendar_days {
                margin-top: 1.211vw;
                gap: 0.404vw;
            }
        }
        @media screen and (max-width: 576px) {
            .datedreamer__calendar_days {
                margin-top: 2.083vw;
                gap: 0.694vw;
            }
        }
        
        
        .datedreamer__calendar_day button:hover {
            background-color: #DFE4EB;
        }
        
        .datedreamer__calendar_day button:active {
            background-color: #919599;
        }
        
        .datedreamer__calendar_inputs, .datedreamer__calendar_errors, .datedreamer__calendar_days {
            margin-top: 0.625vw;
        }
        
        .datedreamer__calendar_day-header, .datedreamer__calendar_title {
            font-family: Inter;
            line-height: 150%;
            font-weight: 600;
            font-size: 0.7291666667vw;
            color: #262626;
            gap: 0.625vw;
            text-align: center;
            background-color: transparent;
            transition: background-color 200ms, color 200ms;
            text-decoration: none;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            color: #343434;

        }
        @media screen and (max-width: 1401px) {
            .datedreamer__calendar_day-header, .datedreamer__calendar_title {
                font-size: 1.0248901903vw;
            }
        }
        @media screen and (max-width: 991px) {
            .datedreamer__calendar_day-header, .datedreamer__calendar_title {
                font-size: 1.8229166667vw;
            }
        }
        @media screen and (max-width: 576px) {
            .datedreamer__calendar_day-header, .datedreamer__calendar_title {
                font-size: 3.7333333333vw;
            }
        }
        
        .datedreamer__calendar_next {
            transform: rotate(-90deg);
        }

        .datedreamer__calendar_prev {
            transform: rotate(90deg);
        }
        
        .datedreamer__calendar_next, .datedreamer__calendar_prev {
            width: auto;
            height: auto;
        } 
        .datedreamer__calendar_next svg, .datedreamer__calendar_prev svg {
            width: 1.25vw;
            height: 1.25vw;

            transform: scale(1);

        }

        @media screen and (max-width: 1401px) {
            .datedreamer__calendar_next svg, .datedreamer__calendar_prev svg {
                width: 1.756954612vw;
                height: 1.756954612vw;
            }
        }
        
        @media screen and (max-width: 991px) {
            .datedreamer__calendar_next svg, .datedreamer__calendar_prev svg {
                width: 3.125vw;
                height: 3.125vw;
            }
        }
        
        @media screen and (max-width: 576px) {
            .datedreamer__calendar_next svg, .datedreamer__calendar_prev svg {
                width: 6.4vw;
                height: 6.4vw;
            }
        }

        .datedreamer__calendar_day button {
            outline: none;
            border: none;
            box-shadow: none;
            font-family: Inter;
            line-height: 150%;
            font-weight: 400;
            background-color: transparent;
            gap: 0.4166666667vw;
            transition: background-color 200ms, color 200ms;
            color: #343434;
            
            border: 0.104vw solid #DFE4EB;
            font-size: 0.7291666667vw;
            border-radius: 0.208vw;
            padding: 0.1041666667vw;
            width: 1.6666666667vw;
            height: 1.6666666667vw;

            border-radius: 0.4166666667vw;
            padding: 0.4166666667vw 0.7291666667vw;
            border: 0.1041666667vw solid #dfe4eb;

            display: flex;
            align-items: center;
            justify-content: center;

        }
        @media screen and (max-width: 1401px) {
            .datedreamer__calendar_day button {
                border-radius: 0.5856515373vw;
                padding: 0.5856515373vw 1.0248901903vw;
                border-width: 0.1464128843vw;
                gap: 0.5856515373vw;
                font-size: 1.0248901903vw;
                width: 2.343vw;
                height: 2.343vw;
            }
        }
        @media screen and (max-width: 991px) {
            .datedreamer__calendar_day button {
                border-radius: 1.0416666667vw;
                padding: 1.0416666667vw 1.8229166667vw;
                border-width: 0.2604166667vw;
                gap: 1.0416666667vw;
                font-size: 1.8229166667vw;
                width: 4.036vw;
                height: 4.036vw;
            }
        }
        @media screen and (max-width: 576px) {
            .datedreamer__calendar_day button {
                border-radius: 2.1333333333vw;
                padding: 2.1333333333vw 3.7333333333vw;
                border-width: 0.5333333333vw;
                gap: 2.1333333333vw;
                font-size: 3.7333333333vw;
                width: 5.556vw;
                height: 8.556000000000001vw;
            }
        }

       .datedreamer__calendar_day.disabled button {
           color: #919599;
       }

       .datedreamer__calendar_day.active button {
            background-color: #919599;
            color: #ffffff;
            border-color: #919599;
       }
     `,
        onChange: e => {
            document.querySelector("#calendar > input[type=date]").value = e.detail
        },
        onRender: e => {
        }
    })), tooltips = (document.querySelectorAll(".table__body-item-settings").forEach(e => {
        const t = e.querySelector(".table__body-item-settings-btns"),
            n = e.querySelectorAll(".table__body-item-settings-btn"),
            r = e.querySelector(".table__body-item-settings-toggle");
        e = e.querySelector(".table__body-item-settings-close");
        window.matchMedia("(max-width: 576px)").matches ? t.style.maxWidth = "" + vwm(40 * n.length + (8 * n.length - 1)) : t.style.maxWidth = "" + vwt(40 * n.length + (8 * n.length - 1)), r.addEventListener("click", () => {
            n.forEach((e, t) => {
                window.matchMedia("(max-width: 576px)").matches ? e.style.right = "" + vwm(8 * t) : e.style.right = "" + vwt(8 * t), e.style.zIndex = 3
            }), r.classList.add("table__body-item-settings-toggle--active"), t.classList.add("table__body-item-settings-btns--active")
        }), e.addEventListener("click", () => {
            t.classList.remove("table__body-item-settings-btns--active"), r.classList.remove("table__body-item-settings-toggle--active"), n.forEach((e, t) => {
                e.style.right = 0, e.style.zIndex = 1
            })
        })
    }), document.querySelectorAll(".table__body-row").forEach(e => {
        e.querySelector(".table-btn-editor")?.addEventListener("click", () => {
            document.querySelector(".modal-point").classList.add("modal-point--active")
        })
    }), document.querySelectorAll(".tooltip")), openPointModal = (tippy(".tooltip-row1-edit", {
        content: "Редактировать",
        placement: "bottom",
        arrow: !1,
        animation: "fade"
    }), tippy(".tooltip-row1-delete", {
        content: "Удалить",
        placement: "bottom",
        arrow: !1,
        animation: "fade"
    }), tippy(".tooltip-row1-info", {
        content: "Инфомрация",
        placement: "bottom",
        arrow: !1,
        animation: "fade"
    }), document.addEventListener("DOMContentLoaded", () => {
        document.querySelector(".tables") && document.querySelector(".table__body").querySelectorAll(".table__body-row").forEach(e => {
            const n = e.querySelector(".form-control__switch[data-autoupdate]");
            n.addEventListener("click", () => {
                var e = n.parentElement.parentElement, t = e.getAttribute("data-pk").trim().replace(/\D/g, ""),
                    e = e.getAttribute("data-field").trim().replace(/\W/g, "");
                $.ajax({
                    type: "POST", url: `?ajax=1&action=boolChange&field=${e}&pk=` + t, success: e => {
                        console.log(e)
                    }
                })
            })
        })
    }), document.addEventListener("DOMContentLoaded", () => {
        if (!document.querySelector(".tables")) return;
        var e = document.querySelector(".table__head"), t = document.querySelector(".table__body");
        const n = e.querySelector(".table-data-select .form-control__checkbox"), r = t.querySelectorAll(".table__body-row");
        document.getElementById("btn-control-add");
        const i = document.getElementById("btn-control-edit"), o = document.getElementById("btn-control-copy"),
            a = document.getElementById("btn-control-delete"), s = (n.addEventListener("change", e => {
                e.target.checked ? r.forEach(e => {
                    e.querySelector(".table-data-select .form-control__checkbox").checked = !0, i.disabled = !1, o.disabled = !1, a.disabled = !1
                }) : r.forEach(e => {
                    e.querySelector(".table-data-select .form-control__checkbox").checked = !1, i.disabled = !0, o.disabled = !0, a.disabled = !0
                })
            }), r.forEach(e => {
                e.querySelector(".table-data-select .form-control__checkbox").addEventListener("change", e => {
                    !0 === l() && (n.checked = !0), e.target.checked ? (i.disabled = !1, o.disabled = !1, a.disabled = !1) : (n.checked = !1) === s() && (i.disabled = !0, o.disabled = !0, a.disabled = !0)
                })
            }), () => {
                for (let e = 0; e < r.length; e++) if (!0 === r[e].querySelector(".table-data-select .form-control__checkbox").checked) return !0;
                return !1
            }), l = () => {
                for (let e = 0; e < r.length; e++) if (!1 === r[e].querySelector(".table-data-select .form-control__checkbox").checked) return !1;
                return !0
            }
    }), () => {
        document.querySelector(".modal-point").classList.add("modal-point--active")
    }), closePointModal = () => {
        document.querySelector(".modal-point").classList.remove("modal-point--active")
    }, openDeleteModal = () => {
        document.querySelector(".modal-delete").classList.add("modal-delete--active")
    }, closeDeleteModal = () => {
        document.querySelector(".modal-delete").classList.remove("modal-delete--active")
    }, openInfoModal = () => {
        document.querySelector(".modal-info").classList.add("modal-info--active")
    }, closeInfoModal = () => {
        document.querySelector(".modal-info").classList.remove("modal-info--active")
    },
    initializeDropdown = (window.openPointModal = openPointModal, window.closePointModal = closePointModal, window.openDeleteModal = openDeleteModal, window.closeDeleteModal = closeDeleteModal, window.openInfoModal = openInfoModal, window.closeInfoModal = closeInfoModal, function (e) {
        const n = document.getElementById(e);
        e = n.getAttribute("data-initialized");
        if (!e) {
            n.setAttribute("data-initialized", "true");
            const r = n.querySelector(".form-control__dropdown-input");
            e = n.querySelector(".form-control__dropdown-top");
            const i = n.querySelector(".form-control__dropdown-current");
            var t = n.querySelectorAll(".form-control__dropdown-item");
            const o = n.querySelector(".form-control__dropdown-list");
            n.clientWidth, o.clientWidth;
            r?.value && t.forEach(e => {
                e.getAttribute("data-value") === r.value && (i.innerHTML = e.innerHTML)
            }), o.style.width = "100%", t.forEach(t => {
                t.addEventListener("click", () => {
                    var e = t.getAttribute("data-value");
                    r.value = e, r.dispatchEvent(new Event("change")), i.innerHTML = t.innerHTML, n.classList.remove("form-control__dropdown--list-top"), n.classList.remove("form-control__dropdown--active")
                })
            }), e.addEventListener("click", () => {
                n.classList.toggle("form-control__dropdown--active"), isElementVisible(o) ? n.classList.remove("form-control__dropdown--list-top") : n.classList.add("form-control__dropdown--list-top")
            })
        }
    });
window.initializeDropdown = initializeDropdown, document.querySelectorAll(".form-control__tags").forEach(t => {
    t.parentElement;
    const o = t.querySelector(".form-control__tags-input"), a = t.querySelector(".form-control__tags-list"),
        s = t.querySelector(".form-control__tags-popup"), l = t.querySelector(".form-control__tags-value");
    t.querySelector(".form-control__tags-help");
    const e = t.dataset.readonly;
    let c = [];

    function i(e, t) {
        c.push(t);
        var n = a.querySelectorAll(".form-control__tags-item");
        const r = document.createElement("li");
        var i = document.createElement("span"),
            e = (i.innerHTML = e, r.classList.add("form-control__tags-item"), r.setAttribute("data-value", t), r.appendChild(i), r.addEventListener("click", () => d(r, t)), c);
        c = [...new Set(c)], console.log(c, e), c.length === e.length && (l.value = c.join(","), o.value = "", n.length ? n[n.length - 1].insertAdjacentElement("afterend", r) : a.insertAdjacentElement("afterbegin", r), s.classList.remove("form-control__tags-popup--active"))
    }

    const n = Array.from(t.querySelectorAll(".list1__item")), d = function (e, t) {
        e.remove(), c.splice(c.indexOf(t), 1), l.value = c.join(","), r(t)
    }, r = e => {
    };
    if (l.value) {
        for (let e = 0; e < l.value.split(",").length; e++) {
            const h = l.value.split(",")[e];
            var u = s.querySelector(`.list1__item[data-value='${h}']`);
            if (u) {
                const f = document.createElement("li");
                var p = document.createElement("span");
                p.innerHTML = u.innerHTML, f.classList.add("form-control__tags-item"), f.setAttribute("data-value", h), c.push(h), f.appendChild(p), f.addEventListener("click", () => d(f, h)), a.insertAdjacentElement("afterbegin", f)
            }
        }
        o.size = 1
    }
    s.querySelector(".list1__items").innerHTML = "", o.addEventListener("input", r => {
        o.size = r.target.value.length + 1, r.target.value.length < 2 ? (s.classList.add("form-control__tags-popup--active"), s.classList.remove("form-control__tags-popup--top"), isElementVisible(s) || s.classList.remove("form-control__tags-popup--top"), s.querySelector(".list1__items").innerHTML = "<li class='list1__item list1__item--selected'>Введите хотя бы 2 символа</li>") : 2 <= r.target.value.length && (s.querySelector(".list1__items").innerHTML = "", s.classList.remove("form-control__tags-popup--top"), n.forEach(e => {
            const t = e.innerHTML;
            var n = document.createElement("li");
            n.classList.add("list1__item"), n.innerHTML = t, n.setAttribute("data-value", e.dataset.value), n.addEventListener("click", () => {
                i(t, e.dataset.value)
            }), t.toLowerCase().includes(r.target.value.toLowerCase()) ? n.style.display = "flex" : n.style.display = "none", s.querySelector(".list1__items").insertAdjacentElement("beforeend", n)
        }), isElementVisible(s) || s.classList.add("form-control__tags-popup--top"), 0 !== Array.from(s.querySelectorAll(".list1__item")).filter(e => "flex" === e.style.display).length) ? s.classList.add("form-control__tags-popup--active") : s.classList.remove("form-control__tags-popup--active")
    }), o.addEventListener("keydown", r => {
        var t;
        "false" === e ? "Enter" === r.key && o.value.trim() && !Array.from(a.querySelectorAll(".form-control__tags-item")).map(e => e.innerText).includes(o.value.trim()) && i(r.target.value, r.target.value) : "Enter" === r.key && o.value.trim() && (t = r.target.value, Array.from(n).find(e => e.innerHTML.toLocaleLowerCase() == t.toLocaleLowerCase())) && (n.forEach(e => {
            if (e.innerHTML.toLocaleLowerCase() === r.target.value.toLocaleLowerCase()) {
                i(e.innerHTML, e.getAttribute("data-value"));
                var t = s.querySelectorAll(".list1__item");
                for (let e = 0; e < t.length; e++) {
                    var n = t[e];
                    c.includes(n.getAttribute("data-value")) && (n.style = "display: none !important")
                }
            }
        }), o.size = 1), "Backspace" === r.key && o.value
    }), o.addEventListener("focus", e => {
        s.classList.add("form-control__tags-popup--active"), s.classList.remove("form-control__tags-popup--top"), isElementVisible(s) || s.classList.add("form-control__tags-popup--top"), s.querySelector(".list1__items").innerHTML = "<li class='list1__item list1__item--selected'>Введите хотя бы 2 символа</li>"
    }), document.addEventListener("click", e => {
        t.contains(e.target) || (s.classList.remove("form-control__tags-popup--active"), s.classList.remove("form-control__tags-popup--top"))
    })
});