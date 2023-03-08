import './global.js';
import '../css/lineas.scss';

import $ from 'jquery';
import { Tooltip } from 'bootstrap';

const DATE_RFC2822 = "ddd, DD MMM YYYY HH:mm:ss ZZ";

function updateTime(callback) {
    const now = new Date();
    $("#wrapper-lineas .linea").each(function () {
        const status = $(this).data("status");
        let t;
        if (status === "idle") {
            t = new Date($(this).data("last_close"));
        } else if (status == "busy") {
            t = new Date($(this).data("last_open"));
        } else {
            return;
        }

        let d = new Date(now.getTime() - t.getTime());
        let sh = d.getTime() / 1000 / 3600 >> 0;
        sh = sh < 10 ? ('0'+sh) : sh;
        let sm = d.getMinutes() < 10 ? ('0'+d.getMinutes()) : (''+d.getMinutes());
        let ss = d.getSeconds() < 10 ? ('0'+d.getSeconds()) : (''+d.getSeconds());
        $(this).find(".linea-time").html(`${sh}:${sm}:${ss}`)

        let m = d.getTime() / 1000 / 60 >> 0;
        if (m == 0) {
            $(this).find(".linea-time-text").html(`${d.getSeconds()} segundos`);
        } else if (m == 1) {
            $(this).find(".linea-time-text").html(`${m} minuto y ${d.getSeconds()} segundos`);
        } else {
            $(this).find(".linea-time-text").html(`${m} minutos y ${d.getSeconds()} segundos`);
        }
    })

    if (callback) {
        callback();
    }
}

function updateData(callback) {
    $.get("/api/lineas", function(data, textStatus, jqXHR) {
        let d = Date.parse(jqXHR.getResponseHeader("date"));
        $("#wrapper-lineas .linea").each(function () {
            const id = $(this).data("id");
            data[id].last_open = (Date.parse(data[id].last_open) + Date.now() - d);
            data[id].last_close =  (Date.parse(data[id].last_close) + Date.now() - d);
            $(this).data(data[id]);
            $(this).attr("data-status", data[id].status);
        });

        updateTime(callback);
    });
}

function updateDataInterval() {
    let updateInterval = $("#wrapper-lineas").data("update-interval") * 1000;

    if (isNaN(updateInterval)) return;

    if (updateInterval < 500) {
        throw "Update interval can't be less than 500 ms";
    }
    
    setTimeout(updateData, updateInterval, updateDataInterval)
}

function hideUsage() {
    $('#usageInfo').hide();
    $('#changeLineButtons a').each(function() {
        let href = $(this).prop('href') + '?hideUsage';
        $(this).prop('href', href);
    });
}

$('#usageInfo button.close').click(hideUsage);

function clickToggleLinea() {
    console.log("Clicked toggleLinea");
    let b = $(this);
    const id = $(this).find(".linea").data("id");
    b.prop('disabled', true);

    $.get(`/api/lineas/toggle/${id}`, function () {
        updateData(function () {b.prop('disabled', false)});
    });
}

$('button#wrapper-lineas').click(clickToggleLinea);
$('tr.linea').click(function() {
   window.location = `/lineas/${$(this).data('id')}`;
});

const qr_copy_button = document.getElementById("qr-copy-button");
const qr_tooltip = qr_copy_button?new Tooltip(qr_copy_button, {trigger: 'manual', title: 'Enlace copiado', placement: 'right'}):null;
$('#qr-copy-button').on('click', function(e) {
    navigator.clipboard.writeText($(e.target).data('link'));

    qr_tooltip.show();

    setTimeout(function() { console.log("Hiding tooltip"); $(e.target).tooltip('hide'); }, 2000);
});

// Equivalent to $().ready, which is deprecated
$(function () {
    const params = new URLSearchParams(window.location.search);
    console.log(params);
    if (params.has('hideUsage')) {
        hideUsage();
    }

    updateTime();
    setInterval(updateTime, 1000);

    if (params.has('updateInterval')) {
        $("#wrapper-lineas").data("update-interval", params.get("updateInterval"));
    }

    updateData(updateDataInterval);
});
