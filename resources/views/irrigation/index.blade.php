<!DOCTYPE html>
<html>
<head>
    <title>Smart Irrigation</title>
</head>
<body>

<h1>Data Irigasi</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>Tanggal</th>
        <th>ETo</th>
        <th>ETc</th>
        <th>Curah Hujan</th>
        <th>Kebutuhan Air</th>
    </tr>

    @foreach($data as $item)
    <tr>
        <td>{{ $item->tanggal }}</td>
        <td>{{ $item->eto }}</td>
        <td>{{ $item->etc }}</td>
        <td>{{ $item->curah_hujan }}</td>
        <td>{{ $item->kebutuhan_air }}</td>
    </tr>
    @endforeach

</table>

<h2>Grafik Kebutuhan Air</h2>

<canvas id="chartKebutuhan"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('chartKebutuhan');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Kebutuhan Air',
            data: @json($kebutuhan),
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>
