'use strict';

// Custom chart functions for the dashboard
function barChart(element, currency, series, categories) {
    // Create ApexCharts options
    const options = {
        series: series,
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
                borderRadius: 5,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return Math.round(val); // Format as integer
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    cssClass: 'apexcharts-xaxis-label',
                }
            }
        },
        yaxis: {
            min: 0,
            max: 20,
            tickAmount: 5, // Show 5 ticks (0, 5, 10, 15, 20)
            forceNiceScale: false,
            labels: {
                formatter: function(val) {
                    return Math.round(val); // Format as integer
                },
                style: {
                    cssClass: 'apexcharts-yaxis-label',
                }
            },
            title: {
                text: 'Số lượng hợp đồng',
                style: {
                    fontSize: '14px',
                    fontWeight: 600,
                }
            }
        },
        fill: {
            opacity: 1,
            colors: ['#6c5ce7', '#ffa62b']
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return Math.round(val) + " hợp đồng";
                }
            }
        },
        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            floating: false,
            fontSize: '14px',
            fontFamily: 'Helvetica, Arial',
            fontWeight: 400,
        },
        colors: ['#6c5ce7', '#ffa62b'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    show: false
                }
            }
        }]
    };

    // Create the chart
    const chart = new ApexCharts(element, options);
    chart.render();
    
    return chart;
}

function barAmountChart(element, currency, series, categories) {
    // Create ApexCharts options for amount chart
    const options = {
        series: series,
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
                borderRadius: 5,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return currency + ' ' + val.toLocaleString();
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    cssClass: 'apexcharts-xaxis-label',
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return currency + ' ' + val.toLocaleString();
                },
                style: {
                    cssClass: 'apexcharts-yaxis-label',
                }
            },
            title: {
                text: 'Tổng số tiền đầu tư',
                style: {
                    fontSize: '14px',
                    fontWeight: 600,
                }
            }
        },
        fill: {
            opacity: 1,
            colors: ['#ffa62b', '#6c5ce7']
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return currency + ' ' + val.toLocaleString();
                }
            }
        },
        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            floating: false,
            fontSize: '14px',
            fontFamily: 'Helvetica, Arial',
            fontWeight: 400,
        },
        colors: ['#ffa62b', '#6c5ce7'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    show: false
                }
            }
        }]
    };

    // Create the chart
    const chart = new ApexCharts(element, options);
    chart.render();
    
    return chart;
}

function piChart(element, labels, data) {
    const ctx = element.getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#6c5ce7',
                    '#ffa62b',
                    '#ff7675',
                    '#45aaf2',
                    '#05dfd7',
                    '#FF00F6',
                    '#1e90ff',
                    '#2ed573',
                    '#eccc68',
                    '#ff5200',
                ],
                borderColor: [
                    'rgba(231, 80, 90, 0.75)'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            aspectRatio: 1,
            responsive: true,
            maintainAspectRatio: true,
            elements: {
                line: {
                    tension: 0
                }
            },
            scales: {
                xAxes: [{
                    display: false
                }],
                yAxes: [{
                    display: false
                }]
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    boxWidth: 15,
                    fontFamily: 'Roboto'
                }
            }
        }
    });
    
    return myChart;
} 