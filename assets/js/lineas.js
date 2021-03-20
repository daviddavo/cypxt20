import './global.js';
import '../css/lineas.scss';

function updateTime() {
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
}

function updateData() {
    $.get("/api/lineas", function(data) {
        $("#wrapper-lineas .linea").each(function () {
            const id = $(this).data("id");
            $(this).data(data[id]);
            $(this).attr("data-status", data[id].status);
        });
    });
}

$('#usageInfo button').click(function() {
    $('#usageInfo').hide();
})

$().ready(function () {
    updateTime();
    setInterval(updateTime, 1000);

    updateData();
    setInterval(updateData, 5000);
});