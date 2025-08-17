// Dashboard Chart.js initialization
export function initUserChart(chartData, chartOptions) {
    // Importer Chart.js dynamiquement
    import('chart.js/auto').then((ChartModule) => {
        const Chart = ChartModule.default;
        
        // Récupérer le canvas
        const canvas = document.getElementById('userChart');
        if (!canvas) {
            console.error('Canvas userChart not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        
        // Créer le graphique
        new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: chartOptions
        });
        
        console.log('Chart.js initialized successfully');
    }).catch((error) => {
        console.error('Error loading Chart.js:', error);
    });
}
