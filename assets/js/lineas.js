import './global.js';
import '../css/lineas.scss';

function updateTime() {
    const now = new Date();
    $("#table-lineas .linea").each(function () {
        const status = $(this).data("status");
        console.log($(this).data());
        let t;
        if (status === "idle") {
            t = new Date($(this).data("last_close"));
        } else if (status == "busy") {
            t = new Date($(this).data("last_open"));
        } else {
            return;
        }

        let d = new Date(now.getTime() - t.getTime());
        let h = d.getTime() / 1000 / 3600 >> 0;
        h = h < 10 ? ('0'+h) : h;
        let m = d.getMinutes() < 10 ? ('0'+d.getMinutes()) : (''+d.getMinutes());
        let s = d.getSeconds() < 10 ? ('0'+d.getSeconds()) : (''+d.getSeconds());
        $(this).find(".linea-time").html(`${h}:${m}:${s}`)
    })
}

function updateData() {
    $.get("/api/lineas", function(data) {
        $("#table-lineas .linea").each(function () {
            const id = $(this).data("id");
            $(this).data(data[id]);
            $(this).attr("data-status", data[id].status);
        });
    });
}

$().ready(function () {
    updateTime();
    setInterval(updateTime, 1000);

    updateData();
    setInterval(updateData, 5000);
});