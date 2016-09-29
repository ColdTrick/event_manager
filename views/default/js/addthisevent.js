/*
	AddThisEvent v1.6.0 <http://addthisevent.com>
	Copyright (c) 2012-2015 Michael Nilsson
*/
function $d(d) {
    return document.getElementById(d)
}
var proc = location.protocol;
if (proc != 'https:') {
    proc = 'http:'
}
var _base_path = proc + '//addthisevent.com';
var _image_path = _base_path + '/gfx/icon-calendar-t5.png';
var _ate_license = '';
var _ate_mouse = false;
var _ate_css = 'true';
var _ate_callback = '';
var _ate_dropdown = '';
var _ate_lbl_outlook = 'Outlook';
var _ate_lbl_google = 'Google <em>(online)</em>';
var _ate_lbl_yahoo = 'Yahoo <em>(online)</em>';
var _ate_lbl_outlookcom = 'Outlook.com <em>(online)</em>';
var _ate_lbl_appleical = 'Apple iCalendar';
var _ate_lbl_fb_event = 'Facebook Event';
var _ate_show_outlook = true;
var _ate_show_google = true;
var _ate_show_yahoo = true;
var _ate_show_outlookcom = true;
var _ate_show_appleical = true;
var _ate_show_facebook = true;
var _d_rd = false;
if (typeof SVGRect != "undefined") {
    _image_path = _base_path + '/gfx/icon-calendar-t1.svg'
}
var addthisevent = function() {
    var D = false,
        dropzind = 999999,
        dropzcx = 1,
        olddrop = '',
        dropmousetim, css1 = false,
        css2 = false;
    return {
        generate: function() {
            try {
                _image_path = _image_path
            } catch (e) {
                _image_path = _base_path + '/gfx/icon-calendar-t5.png'
            }
            try {
                _ate_license = _license
            } catch (e) {}
            try {
                _ate_mouse = _mouse
            } catch (e) {}
            try {
                _ate_css = _css
            } catch (e) {}
            var b = addthisevent.glicense(_ate_license);
            var c = document.getElementsByTagName('*');
            for (var d = 0; d < c.length; d += 1) {
                var f = '',
                    fbevent = false,
                    str = c[d].className,
                    htmx = '';
                if (addthisevent.hasclass(c[d], 'addthisevent')) {
                    var g = c[d].getElementsByTagName('*');
                    for (var m = 0; m < g.length; m += 1) {
                        if (addthisevent.hasclass(g[m], '_url') || addthisevent.hasclass(g[m], 'url')) {
                            g[m].style.display = 'none'
                        }
                        if (addthisevent.hasclass(g[m], '_start') || addthisevent.hasclass(g[m], 'start')) {
                            g[m].style.display = 'none';
                            f += '&dstart=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_end') || addthisevent.hasclass(g[m], 'end')) {
                            g[m].style.display = 'none';
                            f += '&dend=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_zonecode') || addthisevent.hasclass(g[m], 'zonecode')) {
                            g[m].style.display = 'none';
                            f += '&dzone=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_timezone') || addthisevent.hasclass(g[m], 'timezone')) {
                            g[m].style.display = 'none';
                            f += '&dtime=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_summary') || addthisevent.hasclass(g[m], 'summary') || addthisevent.hasclass(g[m], 'title')) {
                            g[m].style.display = 'none';
                            f += '&dsum=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_description') || addthisevent.hasclass(g[m], 'description')) {
                            g[m].style.display = 'none';
                            f += '&ddesc=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_location') || addthisevent.hasclass(g[m], 'location')) {
                            g[m].style.display = 'none';
                            f += '&dloca=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_organizer') || addthisevent.hasclass(g[m], 'organizer')) {
                            g[m].style.display = 'none';
                            f += '&dorga=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_organizer_email') || addthisevent.hasclass(g[m], 'organizer_email')) {
                            g[m].style.display = 'none';
                            f += '&dorgaem=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_attendees') || addthisevent.hasclass(g[m], 'attendees')) {
                            g[m].style.display = 'none';
                            f += '&datte=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_all_day_event') || addthisevent.hasclass(g[m], 'all_day_event')) {
                            g[m].style.display = 'none';
                            f += '&dallday=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_date_format') || addthisevent.hasclass(g[m], 'date_format')) {
                            g[m].style.display = 'none';
                            f += '&dateformat=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_alarm_reminder') || addthisevent.hasclass(g[m], 'alarm_reminder')) {
                            g[m].style.display = 'none';
                            f += '&alarm=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_recurring') || addthisevent.hasclass(g[m], 'recurring')) {
                            g[m].style.display = 'none';
                            f += '&drule=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_uid') || addthisevent.hasclass(g[m], 'uid')) {
                            g[m].style.display = 'none';
                            f += '&uid=' + encodeURIComponent(addthisevent.htmlencode(g[m].innerHTML))
                        }
                        if (addthisevent.hasclass(g[m], '_facebook_event') || addthisevent.hasclass(g[m], 'facebook_event')) {
                            if (g[m].innerHTML != '') {
                                g[m].style.display = 'none';
                                var h = g[m].innerHTML.replace(/ /gi, "");
                                f += '&fbevent=' + encodeURIComponent(h);
                                fbevent = true
                            }
                        }
                    }
                    f = f.replace(/'/gi, "´");
                    if (_ate_dropdown != '') {
                        _ate_dropdown = _ate_dropdown + ',';
                        var i = _ate_dropdown.split(',');
                        for (var a = 0; a < i.length; a += 1) {
                            if (_ate_show_appleical && i[a] == 'ical' || _ate_show_appleical && i[a] == 'appleical') {
                                htmx += '<span class="ateappleical" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'appleical\',\'' + f + '\');">' + _ate_lbl_appleical + '</span>'
                            }
                            if (_ate_show_google && i[a] == 'google') {
                                htmx += '<span class="ategoogle" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'google\',\'' + f + '\');">' + _ate_lbl_google + '</span>'
                            }
                            if (_ate_show_outlook && i[a] == 'outlook') {
                                htmx += '<span class="ateoutlook" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'outlook\',\'' + f + '\');">' + _ate_lbl_outlook + '</span>'
                            }
                            if (_ate_show_outlookcom && i[a] == 'hotmail' || _ate_show_outlookcom && i[a] == 'outlookcom') {
                                htmx += '<span class="ateoutlookcom" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'outlookcom\',\'' + f + '\');">' + _ate_lbl_outlookcom + '</span>'
                            }
                            if (_ate_show_yahoo && i[a] == 'yahoo') {
                                htmx += '<span class="ateyahoo" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'yahoo\',\'' + f + '\');">' + _ate_lbl_yahoo + '</span>'
                            }
                            if (fbevent && i[a] == 'facebook') {
                                if (_ate_show_facebook && i[a] == 'facebook') {
                                    htmx += '<span class="atefacebook" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'facebook\',\'' + f + '\');">' + _ate_lbl_fb_event + '</span>'
                                }
                            }
                        }
                    } else {
                        if (_ate_show_appleical) {
                            htmx += '<span class="ateappleical" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'appleical\',\'' + f + '\');">' + _ate_lbl_appleical + '</span>'
                        }
                        if (_ate_show_google) {
                            htmx += '<span class="ategoogle" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'google\',\'' + f + '\');">' + _ate_lbl_google + '</span>'
                        }
                        if (_ate_show_outlook) {
                            htmx += '<span class="ateoutlook" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'outlook\',\'' + f + '\');">' + _ate_lbl_outlook + '</span>'
                        }
                        if (_ate_show_outlookcom) {
                            htmx += '<span class="ateoutlookcom" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'outlookcom\',\'' + f + '\');">' + _ate_lbl_outlookcom + '</span>'
                        }
                        if (_ate_show_yahoo) {
                            htmx += '<span class="ateyahoo" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'yahoo\',\'' + f + '\');">' + _ate_lbl_yahoo + '</span>'
                        }
                        if (fbevent) {
                            if (_ate_show_facebook) {
                                htmx += '<span class="atefacebook" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'facebook\',\'' + f + '\');">' + _ate_lbl_fb_event + '</span>'
                            }
                        }
                    }
                    if (!b) {
                        htmx += '<em class="copyx"><em class="brx"></em><em class="frs" data-ref="' + dropzcx + '" onclick="addthisevent.click(this,\'home\');">AddThisEvent</em></em>'
                    }
                    c[d].id = 'atedrop' + dropzcx;
                    c[d].className = c[d].className.replace(/addthisevent/gi, '');
                    c[d].className = c[d].className + ' addthisevent-drop';
                    c[d].title = '';
                    var j = c[d].getAttribute('data-direct');
                    if (j) {
                        c[d].setAttribute('data-url', f);
                        c[d].setAttribute('data-ref', dropzcx);
                        c[d].onclick = function() {
                            addthisevent.direct(this);
                            return false
                        }
                    } else {
                        if (_ate_mouse) {
                            c[d].onmouseover = function() {
                                clearTimeout(dropmousetim);
                                addthisevent.show(this, 'auto', 'auto', true)
                            };
                            c[d].onmouseout = function() {
                                dropmousetim = setTimeout("addthisevent.out();", 200)
                            };
                            c[d].onclick = function() {
                                return false
                            }
                        } else {
                            c[d].onclick = function() {
                                addthisevent.show(this, 'auto', 'auto');
                                return false
                            }
                        }
                    }
                    var k = c[d];
                    var l = document.createElement('span');
                    l.className = 'addthisevent_icon';
                    k.appendChild(l);
                    var n = document.createElement('span');
                    n.id = 'atedrop' + dropzcx + '-drop';
                    n.className = 'addthisevent_dropdown';
                    n.innerHTML = htmx;
                    k.appendChild(n);
                    dropzcx++
                }
            }
            if (_ate_css == 'false') {
                addthisevent.trycss()
            } else {
                addthisevent.applycss(b)
            }
        },
        direct: function(f) {
            var a = f.getAttribute('data-url');
            var b = f.getAttribute('data-direct');
            addthisevent.click(f, b, a)
        },
        click: function(f, a, b) {
            var c = '',
                ref = location.href,
                nw = true,
                now = new Date();
            if (a == 'outlook') {
                c = _base_path + '/create/?service=OUTLOOK' + b + '&reference=' + ref;
                nw = false
            }
            if (a == 'google') {
                c = _base_path + '/create/?service=GOOGLE' + b + '&reference=' + ref
            }
            if (a == 'yahoo') {
                c = _base_path + '/create/?service=YAHOO' + b + '&reference=' + ref
            }
            if (a == 'outlookcom') {
                c = _base_path + '/create/?service=OUTLOOKCOM' + b + '&reference=' + ref
            }
            if (a == 'appleical') {
                c = _base_path + '/create/?service=APPLEICAL' + b + '&reference=' + ref;
                nw = false
            }
            if (a == 'facebook') {
                c = _base_path + '/create/?service=FACEBOOK' + b + '&reference=' + ref
            }
            if (a == 'home') {
                c = _base_path
            }
            if (c != '') {
            	c = c + '&client=' + _ate_license;
                if (a != 'home') {
                    var d = f.getAttribute('data-ref');
                    var g = $d('atedrop' + d);
                    if (g) {
                        var h = g.getAttribute('data-track');
                        if (h != null) {
                            h = h.replace(/ate-calendar/gi, a);
                            try {
                                eval(h)
                            } catch (e) {}
                        }
                    }
                }
                if (nw) {
                    window.open(c)
                } else {
                    location.href = c
                }
            }
            if (_ate_callback) {
                for (var i = 0; i < _ate_callback.length; i++) {
                    try {
                        eval(_ate_callback[i])
                    } catch (e) {
                        alert(e.description)
                    }
                }
            }
        },
        applycss: function(a) {
            if (!css2) {
                var b;
                b = '.addthisevent-drop {display:inline-block;position:relative;z-index:999998;font-family:Roboto,"Helvetica Neue",Helvetica,Optima,Segoe,"Segoe UI",Candara,Calibri,Arial,sans-serif;color:#000!important;font-weight:300;line-height:100%;background-color:#fff;border:1px solid;border-color:#e5e6e9 #dfe0e4 #d0d1d5;font-size:15px;text-decoration:none;padding:13px 12px 12px 43px;-webkit-border-radius:3px;border-radius:3px;cursor:pointer;-webkit-font-smoothing:antialiased!important;text-shadow:1px 1px 1px rgba(0,0,0,0.004);}';
                b += '.addthisevent-drop:hover {border:1px solid #aab9d4;color:#000;font-size:15px;text-decoration:none;}';
                b += '.addthisevent-drop:active {top:1px;}';
                b += '.addthisevent-selected {background-color:#f9f9f9;}';
                b += '.addthisevent-drop .addthisevent_icon {width:18px;height:18px;position:absolute;z-index:1;left:12px;top:10px;background:url(' + _image_path + ') no-repeat;background-size:18px 18px;}';
                if (a) {
                    b += '.addthisevent_dropdown {width:200px;position:absolute;z-index:99999;padding:6px 0px 6px 0px;background:#fff;text-align:left;display:none;margin-top:-2px;margin-left:-1px;border-top:1px solid #c8c8c8;border-right:1px solid #bebebe;border-bottom:1px solid #a8a8a8;border-left:1px solid #bebebe;-moz-border-radius:2px;-webkit-border-radius:2px;-webkit-box-shadow:1px 3px 6px rgba(0,0,0,0.15);-moz-box-shadow:1px 3px 6px rgba(0,0,0,0.15);box-shadow:1px 3px 6px rgba(0,0,0,0.15);}'
                } else {
                    b += '.addthisevent_dropdown {width:200px;position:absolute;z-index:99999;padding:6px 0px 0px 0px;background:#fff;text-align:left;display:none;margin-top:-2px;margin-left:-1px;border-top:1px solid #c8c8c8;border-right:1px solid #bebebe;border-bottom:1px solid #a8a8a8;border-left:1px solid #bebebe;-moz-border-radius:2px;-webkit-border-radius:2px;-webkit-box-shadow:1px 3px 6px rgba(0,0,0,0.15);-moz-box-shadow:1px 3px 6px rgba(0,0,0,0.15);box-shadow:1px 3px 6px rgba(0,0,0,0.15);}'
                }
                b += '.addthisevent_dropdown span {display:block;line-height:100%;background:#fff;text-decoration:none;font-size:14px;color:#333;padding:9px 10px 9px 40px;}';
                b += '.addthisevent_dropdown span:hover {background-color:#f4f4f4;color:#000;text-decoration:none;font-size:14px;}';
                b += '.addthisevent span {display:none!important;}';
                b += '.addthisevent-drop .data {display:none!important;}';
                b += '.addthisevent_dropdown .copyx {height:21px;display:block;position:relative;cursor:default;}';
                b += '.addthisevent_dropdown .brx {height:1px;overflow:hidden;background:#e0e0e0;position:absolute;z-index:100;left:10px;right:10px;top:9px;}';
                b += '.addthisevent_dropdown .frs {position:absolute;top:5px;cursor:pointer;right:10px;padding-left:10px;font-style:normal!important;font-weight:normal!important;text-align:right;z-index:101;line-height:110%!important;background:#fff;text-decoration:none;font-size:9px!important;color:#cacaca!important;}';
                b += '.addthisevent_dropdown .frs:hover {color:#6d84b4;}';
                b += '.addthisevent_dropdown .ateappleical {background-image:url(' + _base_path + '/gfx/dropdown-apple-t1.png);background-repeat:no-repeat;background-position:13px 50%;}';
                b += '.addthisevent_dropdown .ateoutlook {background-image:url(' + _base_path + '/gfx/dropdown-outlook-t1.png);background-repeat:no-repeat;background-position:13px 50%;}';
                b += '.addthisevent_dropdown .ateoutlookcom {background-image:url(' + _base_path + '/gfx/dropdown-outlook-t1.png);background-repeat:no-repeat;background-position:13px 50%;}';
                b += '.addthisevent_dropdown .ategoogle {background-image:url(' + _base_path + '/gfx/dropdown-google-t1.png);background-repeat:no-repeat;background-position:13px 50%;}';
                b += '.addthisevent_dropdown .ateyahoo {background-image:url(' + _base_path + '/gfx/dropdown-yahoo-t1.png);background-repeat:no-repeat;background-position:13px 50%;}';
                b += '.addthisevent_dropdown .atefacebook {background-image:url(' + _base_path + '/gfx/dropdown-facebook-t1.png);background-repeat:no-repeat;background-position:13px 50%;}';
                b += '.addthisevent_dropdown em {font-size:12px!important;color:#999!important;}';
                var c = document.createElement("style");
                c.type = "text/css";
                c.id = "ate_css";
                if (c.styleSheet) {
                    c.styleSheet.cssText = b
                } else {
                    c.appendChild(document.createTextNode(b))
                }
                document.getElementsByTagName("head")[0].appendChild(c);
                css2 = true
            }
        },
        trycss: function() {
//            if (!css1) {
//                try {
//                    var a = '.addthisevent {visibility:hidden;}';
//                    a += '.addthisevent-drop .data {display:none!important;}';
//                    a += '.addthisevent-drop {background-image:url(' + _base_path + '/gfx/icon-calendar-t5.png), url(' + _base_path + '/gfx/icon-calendar-t1.svg), url(' + _base_path + '/gfx/dropdown-apple-t1.png), url(' + _base_path + '/gfx/dropdown-facebook-t1.png), url(' + _base_path + '/gfx/dropdown-google-t1.png), url(' + _base_path + '/gfx/dropdown-outlook-t1.png), url(' + _base_path + '/gfx/dropdown-yahoo-t1.png);background-position:-9999px -9999px;background-repeat:no-repeat;}';
//                    var b = document.createElement("style");
//                    b.type = "text/css";
//                    if (b.styleSheet) {
//                        b.styleSheet.cssText = a
//                    } else {
//                        b.appendChild(document.createTextNode(a))
//                    }
//                    document.getElementsByTagName("head")[0].appendChild(b)
//                } catch (e) {}
//                css1 = true
//            }
        },
        removecss: function() {
            try {
                return (hdx = $d('ate_css')) ? hdx.parentNode.removeChild(hdx) : false
            } catch (e) {}
        },
        show: function(f, o, a, b) {
            var c = f.id;
            var d = $d(c);
            var g = $d(c + '-drop');
            if (d && g) {
                if (olddrop != c) {
                    addthisevent.hide(olddrop)
                }
                var h = addthisevent.getstyle(g, 'display');
                try {
                    f.blur()
                } catch (e) {};
                if (h == 'block') {
                    if (b) {} else {
                        addthisevent.hide(c)
                    }
                } else {
                    olddrop = c;
                    d.className = d.className + ' addthisevent-selected';
                    d.style.zIndex = dropzind++;
                    g.style.left = '0px';
                    g.style.top = '0px';
                    g.style.display = 'block';
                    setTimeout("addthisevent.tim();", 350);
                    D = false;
                    var i = parseInt(d.offsetHeight);
                    var j = parseInt(d.offsetWidth);
                    var k = parseInt(g.offsetHeight);
                    var l = parseInt(g.offsetWidth);
                    var m = addthisevent.viewport();
                    var n = m.split('/');
                    var p = parseInt(n[0]);
                    var q = parseInt(n[1]);
                    var r = parseInt(n[2]);
                    var s = parseInt(n[3]);
                    var t = addthisevent.elementposition(g);
                    var u = t.split('/');
                    var v = parseInt(u[0]);
                    var w = parseInt(u[1]);
                    var x = w + k;
                    var y = q + s;
                    var z = v + l;
                    var A = p + r;
                    var B = 0,
                        dropy = 0;
                    if (o == 'down' && a == 'left') {
                        B = '0px';
                        dropy = i + 'px'
                    } else if (o == 'up' && a == 'left') {
                        B = '0px';
                        dropy = -k + 'px'
                    } else if (o == 'down' && a == 'right') {
                        B = -(l - j) + 'px';
                        dropy = i + 'px'
                    } else if (o == 'up' && a == 'right') {
                        B = -(l - j) + 'px';
                        dropy = -k + 'px'
                    } else if (o == 'auto' && a == 'left') {
                        B = '0px';
                        if (x > y) {
                            dropy = -k + 'px'
                        } else {
                            dropy = i + 'px'
                        }
                    } else if (o == 'auto' && a == 'right') {
                        B = -(l - j) + 'px';
                        if (x > y) {
                            dropy = -k + 'px'
                        } else {
                            dropy = i + 'px'
                        }
                    } else {
                        if (x > y) {
                            dropy = -k + 'px'
                        } else {
                            dropy = i + 'px'
                        }
                        if (z > A) {
                            B = -(l - j) + 'px'
                        } else {
                            B = '0px'
                        }
                    }
                    g.style.left = B;
                    g.style.top = dropy;
                    var C = 'ontouchstart' in document.documentElement ? 'touchstart' : 'click';
                    if (document.addEventListener) {
                        document.addEventListener(C, function() {
                            if (D) {
                                setTimeout(function() {
                                    addthisevent.force(c)
                                }, 300)
                            }
                        }, false)
                    } else if (document.attachEvent) {
                        document.attachEvent("on" + C, function() {
                            if (D) {
                                setTimeout(function() {
                                    addthisevent.force(c)
                                }, 300)
                            }
                        })
                    } else {
                        document.onclick = function() {
                            addthisevent.force(c)
                        }
                    }
                }
            }
        },
        force: function(f) {
            var a = $d(f);
            var b = $d(f + '-drop');
            if (a && b) {
                if (D && b.style.display == 'block') {
                    setTimeout("addthisevent.hide('" + f + "');", 350)
                }
            }
        },
        out: function() {
            addthisevent.force(olddrop)
        },
        hide: function(f) {
            var a = $d(f);
            var b = $d(f + '-drop');
            if (a && b) {
                a.className = a.className.replace(/addthisevent-selected/gi, '');
                b.style.display = 'none';
                b.style.zIndex = ''
            }
        },
        tim: function() {
            D = true
        },
        viewport: function() {
            var w = 0,
                h = 0,
                y = 0,
                x = 0;
            if (typeof(window.innerWidth) == 'number') {
                w = window.innerWidth;
                h = window.innerHeight
            } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
                w = document.documentElement.clientWidth;
                h = document.documentElement.clientHeight
            } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
                w = document.body.clientWidth;
                h = document.body.clientHeight
            }
            if (document.all) {
                x = (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
                y = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop
            } else {
                x = window.pageXOffset;
                y = window.pageYOffset
            }
            return w + '/' + h + '/' + x + '/' + y
        },
        elementposition: function(a) {
            var x = 0,
                y = 0;
            if (a.offsetParent) {
                x = a.offsetLeft;
                y = a.offsetTop;
                while (a = a.offsetParent) {
                    x += a.offsetLeft;
                    y += a.offsetTop
                }
            }
            return x + '/' + y
        },
        getstyle: function(a, b) {
            var x = a;
            var y;
            if (x.currentStyle) {
                y = x.currentStyle[b]
            } else if (window.getComputedStyle) {
                y = document.defaultView.getComputedStyle(x, null).getPropertyValue(b)
            }
            return y
        },
        glicense: function(f) {
            var b = location.href;
            var c = true;
            var d = f;
            var e = d.length;
            if (e != '') {
                var a = d.substring(0, 1);
                var z = d.substring(9, 10);
                var m = d.substring(17, 18);
                if (a != 'a') {
                    c = false
                }
                if (z != 'z') {
                    c = false
                }
                if (m != 'm') {
                    c = false
                }
            } else {
                c = false
            }
            if (b.indexOf('addthisevent.com') == -1 && d == 'aao8iuet5zp9iqw5sm9z' || b.indexOf('addevent.to') == -1 && d == 'aao8iuet5zp9iqw5sm9z') {
                c = true
            }
            return c
        },
        refresh: function() {
            var a = document.getElementsByTagName('*');
            for (var d = 0; d < a.length; d += 1) {
                if (addthisevent.hasclass(a[d], 'addthisevent-drop')) {
                    a[d].className = a[d].className.replace(/addthisevent-drop/gi, '');
                    a[d].className = a[d].className.replace(/addthisevent/gi, '');
                    a[d].className = a[d].className + ' addthisevent'
                }
            }
            addthisevent.generate()
        },
        callcack: function(f) {
            _ate_callback = f
        },
        setlabel: function(l, t) {
            var x = l.toLowerCase();
            if (x == 'outlook') {
                _ate_lbl_outlook = t
            }
            if (x == 'outlookcom') {
                _ate_lbl_outlookcom = t
            }
            if (x == 'google') {
                _ate_lbl_google = t
            }
            if (x == 'yahoo') {
                _ate_lbl_yahoo = t
            }
            if (x == 'appleical') {
                _ate_lbl_appleical = t
            }
            if (x == 'facebookevent') {
                _ate_lbl_fb_event = t
            }
        },
        settings: function(c) {
            if (c.license != undefined) {
                _ate_license = c.license
            }
            if (c.css != undefined) {
                if (c.css) {
                    _ate_css = 'true'
                } else {
                    _ate_css = 'false';
                    addthisevent.removecss()
                }
            }
            if (c.mouse != undefined) {
                _ate_mouse = c.mouse
            }
            if (c.outlook != undefined) {
                if (c.outlook.show != undefined) {
                    _ate_show_outlook = c.outlook.show
                }
            }
            if (c.google != undefined) {
                if (c.google.show != undefined) {
                    _ate_show_google = c.google.show
                }
            }
            if (c.yahoo != undefined) {
                if (c.yahoo.show != undefined) {
                    _ate_show_yahoo = c.yahoo.show
                }
            }
            if (c.hotmail != undefined) {
                if (c.hotmail.show != undefined) {
                    _ate_show_outlookcom = c.hotmail.show
                }
            }
            if (c.outlookcom != undefined) {
                if (c.outlookcom.show != undefined) {
                    _ate_show_outlookcom = c.outlookcom.show
                }
            }
            if (c.ical != undefined) {
                if (c.ical.show != undefined) {
                    _ate_show_appleical = c.ical.show
                }
            }
            if (c.appleical != undefined) {
                if (c.appleical.show != undefined) {
                    _ate_show_appleical = c.appleical.show
                }
            }
            if (c.facebook != undefined) {
                if (c.facebook.show != undefined) {
                    _ate_show_facebook = c.facebook.show
                }
            }
            if (c.outlook != undefined) {
                if (c.outlook.text != undefined) {
                    _ate_lbl_outlook = c.outlook.text
                }
            }
            if (c.google != undefined) {
                if (c.google.text != undefined) {
                    _ate_lbl_google = c.google.text
                }
            }
            if (c.yahoo != undefined) {
                if (c.yahoo.text != undefined) {
                    _ate_lbl_yahoo = c.yahoo.text
                }
            }
            if (c.hotmail != undefined) {
                if (c.hotmail.text != undefined) {
                    _ate_lbl_outlookcom = c.hotmail.text
                }
            }
            if (c.outlookcom != undefined) {
                if (c.outlookcom.text != undefined) {
                    _ate_lbl_outlookcom = c.outlookcom.text
                }
            }
            if (c.ical != undefined) {
                if (c.ical.text != undefined) {
                    _ate_lbl_appleical = c.ical.text
                }
            }
            if (c.appleical != undefined) {
                if (c.appleical.text != undefined) {
                    _ate_lbl_appleical = c.appleical.text
                }
            }
            if (c.facebook != undefined) {
                if (c.facebook.text != undefined) {
                    _ate_lbl_fb_event = c.facebook.text
                }
            }
            if (c.dropdown != undefined) {
                if (c.dropdown.order != undefined) {
                    _ate_dropdown = c.dropdown.order
                }
            }
            if (c.callback != undefined) {
                _ate_callback = c.callback
            }
        },
        hasclass: function(e, c) {
            return new RegExp('(\\s|^)' + c + '(\\s|$)').test(e.className)
        },
        htmlencode: function(a) {
            var b = a.replace(/<br\s*[\/]?>/gi, "\n");
            b = b.replace(/<(?:.|\n)*?>/gm, '');
            b = b.replace(/(^\s+|\s+$)/g, '');
            var c = document.createElement("div");
            var d = document.createTextNode(b);
            c.appendChild(d);
            return c.innerHTML
        }
    }
}();
if (window.addEventListener) {
    window.addEventListener("DOMContentLoaded", function() {
        _d_rd = true;
        addthisevent.trycss();
        addthisevent.generate()
    }, false);
    window.addEventListener("load", function() {
        addthisevent.generate()
    }, false)
} else if (window.attachEvent) {
    window.attachEvent("onreadystatechange", function() {
        _d_rd = true;
        addthisevent.trycss();
        addthisevent.generate()
    });
    window.attachEvent("onload", function() {
        addthisevent.generate()
    })
} else {
    window.onload = function() {
        addthisevent.generate()
    }
}
if (!_d_rd) {
    setTimeout("addthisevent.trycss();addthisevent.generate();", 20)
}
