<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Annalytics'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Analytics Dashboard" />
	<!-- End Main Widgets -->

	<div class="p-4 grid grid-cols-1 gap-20">
		<!----------------------------------------------------------------------->
		<div class="p-4 grid grid-cols-4 gap-4 rounded-lg bg-gray-50 dark:bg-neutral-600">

			<div
				class="p-2 grid items-center rounded-lg bg-violet-300 hover:bg-violet-400 dark:bg-violet-500 text-center hover:cursor-pointer">
				<h3>Total Users</h3>
				<h1>{{ $totalUsers ?? 0 }}</h1>
			</div>
			<!----->
			<div
				class="p-2 grid items-center rounded-lg bg-sky-300 hover:bg-sky-400 dark:bg-sky-500 text-center hover:cursor-pointer">
				<h3>Total Projects</h3>
				<h1>{{ $totalProjects ?? 0 }}</h1>
			</div>
			<!----->
			<div
				class="p-2 rounded-lg grid items-center bg-amber-300 hover:bg-amber-400 dark:bg-amber-500 text-center hover:cursor-pointer">
				<h3>Total Deliveries</h3>
				<h1>{{ $totalDeliveries ?? 0 }}</h1>
			</div>
			<!----->
			<div
				class="p-2 rounded-lg grid items-center bg-emerald-300 hover:bg-emerald-400 dark:bg-emerald-500 text-center hover:cursor-pointer">
				<h3>Tools In-Stock</h3>
				<h1>{{ $totalTools ?? 0 }}</h1>
			</div>
			<!----->

		</div>

		<div class="rounded-lg bg-gray-50 dark:bg-neutral-600">
			<h2 class="my-4 text-center">Personnel(s)</h2>

			<div class="my-8 p-4 grid grid-cols-2 gap-4">

				<div class="col-span-1 px-12">
					<canvas id="rolesPieChart"></canvas>
				</div>

				<div class="col-span-1 flex_xy_center">
					<canvas id="rolesBarChart" height="100"></canvas>
				</div>

			</div>
		</div>

		<div class="rounded-lg bg-gray-50 dark:bg-neutral-600">
			<h2 class="my-4 text-center">Project(s) Status</h2>

			<div class="my-8 p-4 grid grid-cols-2 gap-4">

				<div class="col-span-1 px-12">
					<canvas id="projectsYBarChart"></canvas>
				</div>

				<div class="col-span-1 flex_xy_center">
					<canvas id="projectsXBarChart" height="100"></canvas>
				</div>

			</div>
		</div>

		<div class="rounded-lg bg-gray-50 dark:bg-neutral-600">
			<h2 class="my-4 text-center">Project(s) Deadline</h2>

			<div class="my-8 p-4">

				<div class="px-12">
					<canvas id="projectsTargetLineChart" height="100"></canvas>
				</div>
			</div>
		</div>

		<div class="rounded-lg bg-gray-50 dark:bg-neutral-600">
			<h2 class="my-4 text-center">Number of Delivery-Schedules</h2>

			<div class="my-8 p-4">

				<div class="px-12">
					<canvas id="deliveryScheduledLineChart" height="100"></canvas>
				</div>
			</div>
		</div>

		<div class="rounded-lg bg-gray-50 dark:bg-neutral-600">
			<h2 class="my-4 text-center">Inventory Usage</h2>

			<div class="my-8 p-4">

				<div class="px-12">
					<canvas id="toolsMixLineChart" height="100"></canvas>
				</div>
			</div>
		</div>

		<!----------------------------------------------------------------------->
	</div>


	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!--------------- USERS::DOUGHNUT CHART --------------->
<script>
	fetch('/admin/charts/users/roles')
		.then(response => response.json())
		.then(res => {
			const labels = res.data.labels;
			const values = res.data.values;

			const ctx = document.getElementById('rolesPieChart');

			new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: labels,
					datasets: [{
						label: 'Users by Role',
						data: values,
						backgroundColor: [
							'rgb(75, 192, 192)',
							'rgb(115, 115, 195)',
							'rgb(255, 99, 132)',
							'rgb(255, 205, 86)',
						],
						hoverOffset: 4
					}]
				}
			});
		});
</script>

<!--------------- USERS::HORIZONTAL BAR CHART --------------->
<script>
	fetch('/admin/charts/users/roles')
		.then(response => response.json())
		.then(res => {
			const labels = res.data.labels;
			const values = res.data.values;
			const barCtx = document.getElementById('rolesBarChart');

			const colors = [
				'rgba(75, 192, 192, 0.75)',
				'rgba(115, 115, 195, 0.75)',
				'rgba(255, 99, 132, 0.75)',
				'rgba(255, 205, 86, 0.75)',
				// 'rgba(54, 162, 235, 0.75)',
				// 'rgba(153, 102, 255, 0.75)',
				// 'rgba(255, 159, 64, 0.75)',
			];

			const datasets = [];

			labels.forEach((role, index) => {
				let row = Array(labels.length).fill(0);
				row[index] = values[index];

				datasets.push({
					label: role,
					data: row,
					backgroundColor: colors[index % colors.length],
					borderColor: colors[index % colors.length],
					borderWidth: 2
				});
			});

			new Chart(barCtx, {
				type: 'bar',
				data: {
					labels: labels,
					datasets: datasets
				},
				options: {
					indexAxis: 'y',
					responsive: true,
					scales: {
						x: { beginAtZero: true }
					}
				}
			});
		});
</script>

<!--------------- PROJECTS::VERTICAL BAR CHART --------------->
<script>
	fetch('/admin/charts/projects/phases')
		.then(response => response.json())
		.then(res => {
			const labels = res.data.labels;    // ['on-track','completed','cancelled']
			const values = res.data.values;

			const ctx = document.getElementById('projectsYBarChart');

			// COLORS (reuse same palette you used before)
			const colors = [
				'rgba(75, 192, 192, 0.75)',
				'rgba(115, 115, 195, 0.75)',
				'rgba(255, 99, 132, 0.75)',
				'rgba(255, 205, 86, 0.75)',
				'rgba(153, 102, 255, 0.75)',
				'rgba(255, 159, 64, 0.75)',
			];

			// Build stacked dataset (ChartJS expects each label inside a separate dataset)
			let datasets = [];

			labels.forEach((phase, index) => {
				let row = Array(labels.length).fill(0);
				row[index] = values[index];

				datasets.push({
					label: phase,
					data: row,
					backgroundColor: colors[index % colors.length],
					borderColor: colors[index % colors.length],
					borderWidth: 2,
				});
			});

			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: labels,
					datasets: datasets
				},
				options: {
					responsive: true,
					plugins: {
						title: {
							display: true,
							text: 'Projects by Phases'
						}
					},
					interaction: {
						intersect: false
					},
					scales: {
						x: { stacked: true },
						y: { stacked: true }
					}
				}
			});
		});
</script>

<!--------------- PROJECTS::HORIZONTAL BAR CHART --------------->
<script>
	fetch('/admin/charts/projects/phases')
		.then(response => response.json())
		.then(res => {
			const labels = res.data.labels;
			const values = res.data.values;
			const barCtx = document.getElementById('projectsXBarChart');

			const colors = [
				'rgba(75, 192, 192, 0.75)',
				'rgba(255, 99, 132, 0.75)',
				'rgba(255, 205, 86, 0.75)',
				'rgba(54, 162, 235, 0.75)',
				'rgba(153, 102, 255, 0.75)',
				'rgba(255, 159, 64, 0.75)',
			];

			const datasets = [];

			labels.forEach((role, index) => {
				let row = Array(labels.length).fill(0);
				row[index] = values[index];

				datasets.push({
					label: role,
					data: row,
					backgroundColor: colors[index % colors.length],
					borderColor: colors[index % colors.length],
					borderWidth: 2
				});
			});

			new Chart(barCtx, {
				type: 'bar',
				data: {
					labels: labels,
					datasets: datasets
				},
				options: {
					indexAxis: 'y',
					responsive: true,
					scales: {
						x: { beginAtZero: true }
					}
				}
			});
		});
</script>

<!--------------- PROJECTS DEADLINE::LINED CHART --------------->
<script>
	fetch('/admin/charts/projects/closing-date')
		.then(response => response.json())
		.then(res => {

			const labels = res.data.labels;
			const values = res.data.values;
			const names = res.data.names;  // each month has list of projects
			const maxValue = Math.max(...values) + 2;

			const ctx = document.getElementById('projectsTargetLineChart');

			new Chart(ctx, {
				type: 'line',
				data: {
					labels: labels,
					datasets: [{
						label: 'Projects Closing',
						data: values,
						borderColor: 'rgb(54, 162, 235)',
						backgroundColor: 'rgba(54, 162, 235, 0.5)',
						tension: 0.4,
						borderWidth: 3,
						pointRadius: 5,
						pointHoverRadius: 7,
					}]
				},
				options: {
					responsive: true,
					interaction: { mode: 'nearest', intersect: true },

					plugins: {
						tooltip: {
							callbacks: {
								label: function (ctx) {
									const month = ctx.label;
									const projectList = names[month];

									let output = `Projects: ${projectList.length}`;

									projectList.forEach((p, i) => {
										output += `\n${i + 1}. ${p}`;
									});

									return output;
								}
							}
						},
						title: {
							display: true,
							text: 'Project Target Dates (Monthly Closings)'
						}
					},
					scales: {
						x: {
							ticks: {
								callback: function (value) {
									const monthMap = {
										"01": "Jan", "02": "Feb", "03": "Mar", "04": "Apr",
										"05": "May", "06": "Jun", "07": "Jul", "08": "Aug",
										"09": "Sep", "10": "Oct", "11": "Nov", "12": "Dec"
									};
									const raw = this.getLabelForValue(value);
									const [year, month] = raw.split('-');
									return monthMap[month] + " " + year;
								}
							}
						},
						y: {
							beginAtZero: true,
							ticks: { stepSize: 1, precision: 0 },
							suggestedMax: maxValue
						},
						y1: {
							type: 'linear',
							position: 'right',
							beginAtZero: true,
							min: 0,
							max: Math.max(...values),
							ticks: { stepSize: 1, precision: 0 },
							grid: { drawOnChartArea: false },
						}
					}
				}
			});

		});
</script>

<!--------------- DELIVERY DEADLINE::LINED CHART --------------->
<script>
	fetch('/admin/charts/deliveries/schedule-date')
		.then(response => response.json())
		.then(res => {

			const labels = res.data.labels;
			const values = res.data.values;
			const names = res.data.names;

			const maxValue = Math.max(...values) + 2;

			const ctx = document.getElementById('deliveryScheduledLineChart');

			new Chart(ctx, {
				type: 'line',
				data: {
					labels: labels,
					datasets: [{
						label: 'Delivery Closing',
						data: values,
						borderColor: 'rgba(75, 192, 192, 0.75)',
						backgroundColor: 'rgba(75, 192, 192, 0.25)',
						tension: 0.4,
						borderWidth: 3,
						pointRadius: 5,
						pointHoverRadius: 7,
					}]
				},

				options: {
					responsive: true,
					interaction: { mode: 'nearest', intersect: true },

					plugins: {
						tooltip: {
							callbacks: {
								label: function (ctx) {
									const month = ctx.label;
									const list = names[month] ?? [];
									let out = `Projects: ${list.length}`;
									list.forEach((p, i) => out += `\n${i + 1}. ${p}`);
									return out;
								}
							}
						},
						title: {
							display: true,
							text: 'Delivery Scheduled Dates (Monthly Closings)'
						}
					},

					scales: {
						x: {
							ticks: {
								callback: function (value) {
									const m = {
										"01": "Jan", "02": "Feb", "03": "Mar", "04": "Apr",
										"05": "May", "06": "Jun", "07": "Jul", "08": "Aug",
										"09": "Sep", "10": "Oct", "11": "Nov", "12": "Dec"
									};
									const raw = this.getLabelForValue(value);
									const [year, month] = raw.split('-');
									return m[month] + " " + year;
								}
							}
						},
						y: {
							beginAtZero: true,
							ticks: { stepSize: 1, precision: 0 },
							suggestedMax: maxValue
						},
						y1: {
							type: 'linear',
							position: 'right',
							beginAtZero: true,
							min: 0,
							max: Math.max(...values),
							ticks: { stepSize: 1, precision: 0 },
							grid: { drawOnChartArea: false },
						}
					}
				}
			});

		});
</script>

<!--------------- INVENTORY TOOLS::MIX-LINED CHART --------------->
<script>
	fetch('/admin/charts/inventory/tools/usage')
		.then(res => res.json())
		.then(res => {
			const labels = res.data.labels;
			const usageData = res.data.usage;
			const maintenanceData = res.data.maintenance;

			const maxValue = Math.max(
				...usageData,
				...maintenanceData
			);

			const ctx = document.getElementById('toolsMixLineChart');

			new Chart(ctx, {
				type: 'line',
				data: {
					labels: labels.map(date => {
						const [year, month] = date.split('-');
						const monthMap = {
							"01": "Jan", "02": "Feb", "03": "Mar", "04": "Apr",
							"05": "May", "06": "Jun", "07": "Jul", "08": "Aug",
							"09": "Sep", "10": "Oct", "11": "Nov", "12": "Dec"
						};
						return monthMap[month] + " " + year;
					}),
					datasets: [
						{
							label: 'Tools used',
							data: usageData,
							borderColor: 'rgb(255, 99, 132)',
							backgroundColor: 'rgba(255, 99, 132, 0.25)',
							borderWidth: 3,
							tension: 0.4,
							pointRadius: 5,
							pointHoverRadius: 7,
						},
						{
							label: 'Sent to Maintenance',
							data: maintenanceData,
							borderColor: 'rgb(54, 162, 235)',
							backgroundColor: 'rgba(54, 162, 235, 0.25)',
							borderWidth: 3,
							tension: 0.4,
							pointRadius: 5,
							pointHoverRadius: 7,
						}
					]
				},
				options: {
					responsive: true,
					plugins: {
						legend: { position: 'top' },
						title: {
							display: true,
							text: 'Tools Usage & Maintenance (Monthly)'
						}
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: { stepSize: 1, precision: 0 },
							suggestedMax: maxValue + 2
						}
					}
				}
			});
		});
</script>