import Plotly from 'plotly.js-dist';

const commonLayout = {
    font:{
        family: 'Raleway, sans-serif'
    },
    paper_bgcolor: 'rgba(0,0,0,0)',
    plot_bgcolor: 'rgba(0,0,0,0)'
};

const barlayout = {
    showlegend: false,
    xaxis: {
        tickangle: -45
    },
    yaxis: {
        zeroline: false,
        gridwidth: 2
    },
    bargap :0.05,
    ...commonLayout
};

function plotCnt(ages, expected) {
    let sum = 0;
    ages.forEach(function(r) {
        sum += +r["cnt"];
    });

    let data = [{
        type: "indicator",
        mode: "gauge+number",
        title: {text: "Inscritos"},
        value: sum,
        gauge: {axis: {visible: true, range: [0, expected]}}
    }];

    $('#totalcnt').text(sum);

    let layout = {...commonLayout};
    Plotly.newPlot('cntplot', data, layout);
}

function plotAges(ages) {
    let xarr = []; let yarr = [];
    let sum = 0;
    ages.forEach(function(r) {
        sum += +r["cnt"];
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

    let layout = {
        title: 'Edad de los participantes',
        ...barlayout
    };

    Plotly.newPlot('ageplot', data, layout);
}

$.ajax({
    url: "/api/stats",
    type: "GET",
    success: function(result) {
        plotAges(result["ages"]);
        plotCnt(result["ages"], result["expected"]);
    },
    error: function(error) {
        console.error(error)
    }
});

