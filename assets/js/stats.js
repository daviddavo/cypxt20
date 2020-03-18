import Plotly from 'plotly.js-dist';

const barlayout = {
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

function plotAges(ages) {
    let xarr = []; let yarr = [];
    ages.forEach(function(r) {
        xarr.push(r["age"]);
        yarr.push(r["cnt"]);
    });
    let data = [{
        x: xarr,
        y: yarr,
        type: 'bar',
        marker: {
            color: '#932117'
        }
    }];

    let layout = {...barlayout};
    layout.title = 'Edad de los participantes';

    Plotly.newPlot('ageplot', data, layout);
}

function plotHours(hours) {
    let xarr = []; let yarr = [];
    for (let h in hours) {
        let hn = +h+1;
        xarr.push(`${h}:00-${hn}:00`);
        yarr.push(hours[h]);
    }

    let data = [{
        x: xarr,
        y: yarr,
        type: 'bar',
        marker: {
            color: '#2cff5c'
        }
    }];

    let layout = {...barlayout};
    layout.title = 'Horas seleccionadas';
    layout.xaxis = {
        tickangle: -45
    };

    console.log(layout);

    Plotly.newPlot('hourplot', data, layout);
}

$.ajax({
    url: "/api/stats",
    type: "GET",
    success: function(result) {
        plotAges(result["ages"]);
        plotHours(result["hours"]);
    },
    error: function(error) {
        console.error(error)
    }
});

