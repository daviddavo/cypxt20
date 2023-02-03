import Plotly from 'plotly.js-dist';
import { dark_template } from './stats/dark-template';



function isDarkMode() {
    return document.body.style.colorScheme == "dark";
}

function getCommonLayout() {
    return {
        font:{
            family: 'Raleway, sans-serif'
        },
        paper_bgcolor: 'rgba(0,0,0,0)',
        plot_bgcolor: 'rgba(0,0,0,0)',
        template: isDarkMode()?dark_template:{},
    };
}

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

    let layout = {...getCommonLayout()};
    
    console.log("Using layout:", layout);
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
        showlegend: false,
        xaxis: {
            tickangle: -45
        },
        yaxis: {
            zeroline: false,
            gridwidth: 2
        },
        bargap :0.05,
        ...getCommonLayout(),
    };

    Plotly.newPlot('ageplot', data, layout);
}

document.addEventListener("DOMContentLoaded", function() {
    let observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutationRecord) {
            let cl = getCommonLayout();
            Plotly.relayout('cntplot', cl);
            Plotly.relayout('ageplot', cl);
        })
    });
    observer.observe(document.body, {attributes: true, attributeFilter: ['style']});
});

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

