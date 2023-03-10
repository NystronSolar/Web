function createChart(elementId, labels, datasets) {
  const ctx = document.getElementById(elementId);

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: datasets
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}