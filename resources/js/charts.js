import { Chart, ArcElement, BarController, BarElement, CategoryScale, DoughnutController, Legend, LinearScale, LineController, LineElement, PieController, PointElement, Tooltip } from 'chart.js';

Chart.register(ArcElement, BarController, BarElement, CategoryScale, DoughnutController, Legend, LinearScale, LineController, LineElement, PieController, PointElement, Tooltip);

export const CHART_COLORS = {
    secondary: '#0058be',
    secondaryLight: '#2170e4',
    navy: '#131b2e',
    green: '#10b981',
    amber: '#f59e0b',
    red: '#ba1a1a',
    gray: '#c6c6cd',
};

export function initChart(canvasId, config) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        return null;
    }

    const chart = new Chart(canvas.getContext('2d'), config);

    return chart;
}
