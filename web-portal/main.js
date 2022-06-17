const rows = document.getElementsByClassName("countryRow");
const myTable = document.getElementsByClassName("table table-dark table-hover")[0];
for (let i = 0; i < rows.length; i++) {
    console.log(myTable.rows[i].cells[0].innerHTML)
    rows[i].addEventListener("click", () => {
        document.cookie = "country=" + myTable.rows[i + 1].cells[0].innerHTML;
        window.location.href = "statistic.php";
    })
}
const map = L.map('map');
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFyaWFuY2hvbWEiLCJhIjoiY2t2ZTg3ZXNvMzBkNTJxb2s2Ym96a2V5biJ9.qfsDejIYDuqW_CRBFl97mw', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'your.mapbox.access.token'
}).addTo(map);


let value = ('; ' + document.cookie).split(`; coordinates=`).pop().split(';')[0];
value = value.split('%2C')
map.setView([value[2], value[1]], 5)

for (let i = 1; i < value.length; i += 2) {
    L.marker([value[i + 1], value[i]]).addTo(map);
}

const xyValues = [parseInt(('; ' + document.cookie).split(`; 0TO6=`).pop().split(';')[0]),
    parseInt(('; ' + document.cookie).split(`; 6TO15=`).pop().split(';')[0]),
    parseInt(('; ' + document.cookie).split(`; 15TO21=`).pop().split(';')[0]),
    parseInt(('; ' + document.cookie).split(`; 21TO24=`).pop().split(';')[0]),
];
console.log(xyValues)
const labels = ["00:00-6:00", "6:00-15:00", "15:00-21:00", "21:00-24:00"];

new Chart("myChart", {
    type: "line",
    data: {
        labels: labels,
        datasets: [{
            label: 'Počet prihlásení',
            data: xyValues,
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        scales:
            {
                xAxes:
                    [{
                        scaleLabel: {
                            display: true,
                            labelString: 'čas'
                        },
                        ticks: {fontSize: 20}

                    }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'počet prihlásení',
                    },
                    ticks: {fontSize: 20}

                }]
            }
    }
});

