import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { initChart, CHART_COLORS } from './charts';

window.initChart = initChart;
window.CHART_COLORS = CHART_COLORS;
