<link rel="stylesheet" href="/styles/custom_2.css">
<link rel="stylesheet" href="/styles/custom.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css">
<link rel="shortcut icon" href="../panel/gt_logo.png" type="image/png">
<script src="/styles/main.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/styles/Chart.min.css">
<script src="/styles/Chart.min.js"></script>
<script src="/styles/colors.js"></script>

<link href="/styles/alertify.min.css" rel="stylesheet">
<script src="/styles/alertify.min.js"></script>

<title>Guild Stats</title>

<style>
    .grid-cont {
        padding: 0.5rem;
        justify-items: center;
        width: 100%;
        display: grid;
        grid-template-rows: max-content 1fr;
        height: 100%;
        box-sizing: border-box;
    }

    .main-header {
        box-sizing: border-box;
        padding-bottom: 0.5rem;
    }

    .graph-cont {
        box-sizing: border-box;
        width: 90%;
        height: 90%;
    }
</style>

<div id="forms_graph" class="grid-cont">
    <div class="main-header">
        <b>
            <span class="font-size24 padding-05rem" id="header_main">Activity Information</span>
            <span id="info_display" class="font-size24 padding-05rem"></span>
            <button class="button-primary" onclick="select_type()">Mode</button>
            <button class="button-primary" onclick="set_interval()">Interval</button>
            <button class="button-primary" onclick="select_mode()">Select Guild</button>
            <button class="button-primary" onclick="change_theme()">Change Theme</button>
            <button class="button-primary" onclick="load_login_menu()">Back</button>
        </b>
    </div>

    <div id="form_list_container" align="center" class="graph-cont">
        <canvas id="main_chart" style="position: relative"></canvas>
    </div>
</div>

<script>
    window.mode = "overall";
    window.resolver = {
        "overall": "Overall"
    };

    window.hourly = false;
    window.hourly_limit = 1;
    window.hourly_display = "Day";
    window.hourly_interval = 1;

    prepare_view();

    function load_login_menu() {
        location.href = "/panel/";
    }

    function sendGetRequest(url) {
        return new Promise(function(resolve, reject) {
            fetch(url)
                .then((response) => {
                    return response.json();
                }, () => {
                    reject()
                })
                .then((data) => {
                    resolve(data);
                }, () => {
                    reject()
                });
        });
    }

    prepareGraphingData();

    function loadGuildData() {
        let method = "getWeeklyStats";
        if (window.hourly) {
            method = "getHourlyStats";
        }
        return new Promise(function(resolve, reject) {
            sendGetRequest(`/activity/api.php?method=${method}`).then((activity_info) => {
                try {
                    resolve(activity_info);
                } catch (e) {
                    resolve({});
                }
            }, () => {
                resolve({});
            })
        });
    }

    window.data = [];

    async function prepareGraphingData() {
        let activity_data = await loadGuildData();

        window.data = activity_data;

        for (let guild_object of Object.entries(activity_data?.guilds)) {
            window.resolver[guild_object[0]] = guild_object[1];
        }

        load_graphs(activity_data);
    }

    function load_graphs() {
        let activity_data = window.data;
        let chart_config = prepareData();
        drawChart(chart_config);

        function prepareData() {
            let chart_headers = [];
            let chart_datasets = [];
            let ch_type = 'bar';

            if (window.hourly) {
                let labels = [];
                let datasets_messages = {
                    "total": []
                };
                let datasets_uuids = {
                    "total": []
                };

                let datapoint_interval = parseInt(window.hourly_interval);
                if(datapoint_interval < 1){
                    datapoint_interval = 1;
                }
                if(datapoint_interval > window.hourly_limit*24){
                    datapoint_interval = window.hourly_limit;
                }

                let completed_hrs = 0;
                let start_hrid = false;
                let start_label = "N/A";
                let total_msgs = 0;
                let total_uuids = 0;
                let guild_msgs = {};
                let guild_uuids = {};

                for (let chr = window.hourly_limit * 24; chr >= 0; chr--) {
                    var date = new Date(Date.now() - 60 * 60 * chr * 1000);
                    let hrid = `${date.getUTCFullYear()}${("0" + (date.getUTCMonth()+1)).substr(-2)}${("0" + date.getUTCDate()).substr(-2)}${("0" + date.getUTCHours()).substr(-2)}`;

                    if(start_hrid === false){
                        start_hrid = hrid;
                        weekday = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][date.getDay()]
                        start_label = `${("0" + date.getHours()).substr(-2)}:00 (${("0" + date.getDate()).substr(-2)}.${("0" + (date.getMonth()+1)).substr(-2)} ${weekday})`;
                    }

                    for (let guild of Object.entries(activity_data?.guilds)) {
                        if (guild_msgs?.[guild[0]] == undefined) {
                            guild_msgs[guild[0]] = 0;
                        }
                        if (guild_uuids?.[guild[0]] == undefined) {
                            guild_uuids[guild[0]] = 0;
                        }

                        guild_msgs[guild[0]] += (activity_data?.points?.[guild[0]]?.[hrid]?.messages ?? 0);
                        guild_uuids[guild[0]] += (activity_data?.points?.[guild[0]]?.[hrid]?.uuids ?? 0);

                        total_msgs += activity_data?.points?.[guild[0]]?.[hrid]?.messages ?? 0;
                        total_uuids += activity_data?.points?.[guild[0]]?.[hrid]?.uuids ?? 0;
                    }

                    completed_hrs++;

                    if(completed_hrs >= datapoint_interval || chr == 0){
                        labels.push(start_label);

                        for (let guild of Object.entries(activity_data?.guilds)) {
                            if (datasets_messages?.[guild[0]] == undefined) {
                                datasets_messages[guild[0]] = [];
                            }
                            if (datasets_uuids?.[guild[0]] == undefined) {
                                datasets_uuids[guild[0]] = [];
                            }

                            datasets_messages?.[guild[0]].push(guild_msgs[guild[0]]);
                            datasets_uuids?.[guild[0]].push(guild_uuids[guild[0]]);
                        }

                        datasets_messages?.total.push(total_msgs);
                        datasets_uuids?.total.push(total_uuids);

                        completed_hrs = 0;
                        start_hrid = false;
                        start_label = "N/A";
                        total_msgs = 0;
                        total_uuids = 0;
                        guild_msgs = {};
                        guild_uuids = {};
                    }
                }

                if (window.mode == "overall") {
                    ch_type = 'line';
                    chart_datasets.push({
                        label: "Messages",
                        data: datasets_messages['total'],
                        borderColor: "#ADD8E6",
                        fill: false
                    });

                    chart_datasets.push({
                        label: "Players",
                        data: datasets_uuids['total'],
                        borderColor: "#90EE90",
                        fill: false
                    });

                    chart_headers = labels;
                    info_display.textContent = `| Overall Hourly Stats (${window.hourly_display})`;
                } else {
                    let guild_name = window.mode;

                    ch_type = 'line';
                    chart_datasets.push({
                        label: "Messages",
                        data: datasets_messages[guild_name],
                        borderColor: "#ADD8E6",
                        fill: false
                    });

                    chart_datasets.push({
                        label: "Players",
                        data: datasets_uuids[guild_name],
                        borderColor: "#90EE90",
                        fill: false
                    });

                    chart_headers = labels;
                    info_display.textContent = `| ${window.resolver[window.mode] ?? "Unknown Guild"} Hourly Stats (${window.hourly_display})`;
                }
            } else {
                if (window.mode == "overall") {
                    let colors = getColors((Object.keys(activity_data?.points).length ?? 0) + 1);
                    let ctr = 0;

                    for (let guild_object of Object.entries(activity_data?.points)) {
                        chart_datasets.push({
                            label: guild_object[0],
                            data: guild_object[1],
                            backgroundColor: colors[ctr]
                        });
                        ctr++;
                    }

                    let average_values = [];
                    for (let i = 0; i < activity_data.weeks.length; i++) {
                        let total_msgs = 0;
                        let counter = 0;
                        for (let guild_object of Object.entries(activity_data?.points)) {
                            counter++;
                            total_msgs += guild_object[1][i];
                        }

                        let average = 0;
                        if (counter != 0) {
                            average = Math.floor(total_msgs / counter);
                            average_values.push(average);
                        }
                    }

                    chart_datasets.push({
                        label: "Average",
                        data: average_values,
                        backgroundColor: colors[ctr]
                    });

                    chart_headers = activity_data.weeks;

                    info_display.textContent = `| Overall Stats`;
                } else {
                    let data = activity_data?.points?.[window.mode] ?? [];
                    let guild_name = window.mode;

                    chart_datasets.push({
                        label: guild_name,
                        data: data,
                        backgroundColor: "#ADD8E6"
                    });

                    let average_values = [];
                    for (let i = 0; i < activity_data.weeks.length; i++) {
                        let total_msgs = 0;
                        let counter = 0;
                        for (let guild_object of Object.entries(activity_data?.points)) {
                            counter++;
                            total_msgs += guild_object[1][i];
                        }

                        let average = 0;
                        if (counter != 0) {
                            average = Math.floor(total_msgs / counter);
                            average_values.push(average);
                        }
                    }

                    chart_datasets.push({
                        label: "Average",
                        data: average_values,
                        backgroundColor: "#90EE90"
                    });

                    chart_headers = activity_data.weeks;

                    info_display.textContent = `| ${window.resolver[window.mode] ?? "Unknown Guild"} Stats`;
                }
            }

            let chart_dta = {
                labels: chart_headers,
                datasets: chart_datasets
            };

            const config = {
                type: ch_type,
                data: chart_dta,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                    },
                    scales: {
                        xAxes: {
                            type: 'linear',
                            display: true,
                            title: {
                                display: true,
                            }
                        },
                        yAxes: [{
                            display: true,
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                },
            };

            return config;
        }

        function drawChart(config) {
            if (window.this_chart != null) {
                window.this_chart.destroy();
            }

            let ctx = document.getElementById('main_chart').getContext('2d');
            ctx.reset();
            window.this_chart = new Chart(ctx, config);

            window.this_chart.canvas.parentNode.style.height = document.getElementById('main_chart').clientHeight;
            window.this_chart.canvas.parentNode.style.width = document.getElementById('main_chart').clientWidth;
        }
    }

    function select_mode() {
        alertify.confirm("Select Mode", "Choose the graph that will be displayed.<br><br><select id=\"tiebreaker_select\" class=\"text-input max-width\"></select>",
            function() {
                window.mode = tiebreaker_select.value;
                load_graphs();
            },
            function() {});

        tiebreaker_select.innerHTML = "";

        let options = [];

        for (let tiebreaker of Object.entries(window.resolver)) {
            options.push({
                "name": tiebreaker[1],
                "value": tiebreaker[0]
            });
        }

        options.forEach((option) => {
            tiebreaker_select.innerHTML += `<option class="colored-option" value="${option.value}">${option.name}</option>`;
        })
    }

    function select_type() {
        alertify.confirm("Select Type", "Choose the graph that will be displayed.<br><br><select id=\"tiebreaker_select\" class=\"text-input max-width\"></select>",
            function() {
                let mode = tiebreaker_select.value;

                if(mode == "weekly"){
                    window.hourly = false;
                }
                
                if(mode == "hourly_day"){
                    window.hourly = true;
                    window.hourly_limit = 1;
                    window.hourly_interval = 1;
                    window.hourly_display = "Day";
                }

                if(mode == "hourly_week"){
                    window.hourly = true;
                    window.hourly_limit = 7;
                    window.hourly_interval = 4;
                    window.hourly_display = "Week";
                }

                if(mode == "hourly_month"){
                    window.hourly = true;
                    window.hourly_limit = 28;
                    window.hourly_interval = 8;
                    window.hourly_display = "Month";
                }

                prepareGraphingData();
            },
            function() {});

        tiebreaker_select.innerHTML = "";

        let options = [];

        options.push({
            "name": "Weekly (Last 8 weeks)",
            "value": "weekly"
        });

        options.push({
            "name": "Hourly (Last day)",
            "value": "hourly_day"
        });

        options.push({
            "name": "Hourly (Last week)",
            "value": "hourly_week"
        });

        options.push({
            "name": "Hourly (Last 28 days)",
            "value": "hourly_month"
        });        

        options.forEach((option) => {
            tiebreaker_select.innerHTML += `<option class="colored-option" value="${option.value}">${option.name}</option>`;
        })
    }

    function set_interval(){
	alertify.prompt( 'Set Interval', 'Sets interval between datapoints. Works only for "Hourly" mode.', window.hourly_interval, function(evt, value) {
		let new_value = parseInt(value);
		if(new_value < 1){
			new_value = 1;
		}
		window.hourly_interval = new_value;
		prepareGraphingData();

	}, function() {  });
}
</script>