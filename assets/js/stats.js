import Plotly from 'plotly.js-dist';

var layout = {
    title: 'Edad de los participantes',
    font:{
        family: 'Raleway, sans-serif'
    },
    showlegend: false,
    xaxis: {
        tickangle: -45
    },
    yaxis: {
        zeroline: false,
        gridwidth: 2
    },
    bargap :0.05
};

$.ajax({
    url: "/api/stats",
    type: "GET",
    success: function(result) {
        console.log(result);
        var xarr = []; var yarr = [];
        result["ages"].forEach(function(r) {
           xarr.push(r["age"]);
           yarr.push(r["cnt"]);
        });
        var data = [{
            x: xarr,
            y: yarr,
            type: 'bar',
            marker: {
                color: '#933a16'
            }
        }];

        Plotly.newPlot('ageplot', data, layout);
    },
    error: function(error) {
        console.error(error)
    }
});

