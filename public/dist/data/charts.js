// Load Charts and the corechart package
google.charts.load('current', {
    'packages': ['corechart', 'geochart']
});

// Set a callback for Nigeria GeoChart
google.charts.setOnLoadCallback(drawNigeriaGeoChart);

// Set a callback for Nigeria Line Chart
google.charts.setOnLoadCallback(drawNigeriaStatsChart);

// Set a callback for World GeoChart
google.charts.setOnLoadCallback(drawWorldGeoChart);

// Nigeria GeoChart
function drawNigeriaGeoChart() {

    // AJAX Call
    var nigeriaGeoData = $.ajax({
        url: "dist/data/nigeriaGeo.php",
        dataType: "json",
        async: false
    }).responseText;

    // // Create the data table.
    var data = new google.visualization.DataTable(nigeriaGeoData);

    // Set chart options
    var options = {
        // height: 500,
        region: 'NG',
        domain: 'NG',
        displayMode: 'regions',
        resolution: 'provinces',
        enableRegionInteractivity: true,
        colorAxis: {
            colors: ['#ffd167', '#ff0c07', '#ff0c07', '#ff0c07', '#ff0c07']
        },
        datalessRegionColor: '#FFFFFF',
        defaultColor: '#eee',
        tooltip: {
            showColorCode: true,
            textStyle: {
                fontName: 'Work Sans',
                fontSize: '12'
            },
            trigger: 'focus'
        },
        legend: {
            position: 'none'
        }
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.GeoChart(document.getElementById('nigeria-geo'));
    chart.draw(data, options);
}

// Nigeria Stats Chart
function drawNigeriaStatsChart() {

    // AJAX Call
    var rawData = $.ajax({
        url: "dist/data/nigeriaStats.php",
        dataType: "json",
        async: false
    }).responseText;

    var nigeriaStats = $.parseJSON(rawData);

    // // Create the data table.
    var data = new google.visualization.DataTable(nigeriaStats.Data);

    var options = {
        height: 275,
        curveType: 'none',
        animation: {
            "startup": false,
            duration: 1000,
            easing: 'out',
        },
        legend: {
            position: 'none'
        },
        vAxis: {
            title: 'Cases',
            titleTextStyle: {
                color: '#212529',
                fontName: 'Work Sans',
                fontSize: '15',
                italic: 'false'
            },
            textStyle: {
                color: '#212529',
                fontName: 'Work Sans',
                fontSize: '13',
                italic: 'false'
            },
            viewWindow: {
                min: 0
            },
            gridlines: {
                color: '#EEEEEE',
            },
            baselineColor: '#EEEEEE'
        },
        series: [{
            color: '#ffc107'
        }, {
            color: '#dc3545'
        }, {
            color: '#28a745'
        }],
        pointSize: 5,
        tooltip: {
            showColorCode: true,
            textStyle: {
                fontName: 'Work Sans',
                fontSize: '12'
            },
            trigger: 'focus'
        },
        // trendlines: {
        //     0: {
        //         type: 'exponential',
        //         color: '#ffc107',
        //         lineWidth: 1,
        //         opacity: 0.4,
        //         showR2: false,
        //         visibleInLegend: false
        //     },
        //     1: {
        //         type: 'exponential',
        //         color: '#dc3545',
        //         lineWidth: 1,
        //         opacity: 0.4,
        //         showR2: false,
        //         visibleInLegend: false
        //     },
        //     2: {
        //         type: 'exponential',
        //         color: '#28a745',
        //         lineWidth: 1,
        //         opacity: 0.4,
        //         showR2: false,
        //         visibleInLegend: false
        //     }
        // },
        hAxis: {
            viewWindowMode: 'pretty',
            textStyle: {
                color: '#212529',
                fontName: 'Work Sans',
                fontSize: '13',
                italic: 'false'
            },
            slantedText: false,
            slantedTextAngle: 45,
            gridlines: {
                count: 0,
                color: 'none'
            },
            ticks: [{
                v: new Date(parseDate(nigeriaStats.startDateV)),
                f: nigeriaStats.startDateF
            }, {
                v: new Date(parseDate(nigeriaStats.middleDateV)),
                f: nigeriaStats.middleDateF
            }, {
                v: new Date(parseDate(nigeriaStats.endDateV)),
                f: nigeriaStats.endDateF
            }]
        }
    };

    var chart = new google.visualization.ScatterChart(document.getElementById('nigeria-stats'));

    chart.draw(data, options);
}

// World GeoChart
function drawWorldGeoChart() {

    // AJAX Call
    var worldGeoData = $.ajax({
        url: "dist/data/worldGeo.php",
        dataType: "json",
        async: false
    }).responseText;

    // // Create the data table.
    var data = new google.visualization.DataTable(worldGeoData);

    // Set chart options
    var options = {
        // height: 500,
        region: 'world',
        domain: 'NG',
        displayMode: 'regions',
        enableRegionInteractivity: true,
        colorAxis: {
            colors: ['#ffd167', '#ff0c07', '#ff0c07', '#ff0c07', '#ff0c07', '#ff0c07', '#ff0c07']
        },
        datalessRegionColor: '#999999',
        tooltip: {
            isHtml: true,
            showColorCode: true,
            textStyle: {
                fontName: 'Work Sans',
                fontSize: '12'
            },
            trigger: 'focus',
        },
        series: {
            1: {
                labelInLegend: 'Cases per million',
                visibleInLegend: true,
            }
        },
        legend: {
            position: 'bottom',
            alignment: 'start',
            textStyle: {
                fontSize: 1
            }
        }
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.GeoChart(document.getElementById('world-geo'));
    chart.draw(data, options);
}