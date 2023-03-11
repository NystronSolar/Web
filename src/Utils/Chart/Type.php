<?php

namespace App\Utils\Chart;

enum Type: string
{
    case Area = 'area';

    case Bar = 'bar';

    case Bubble = 'bubble';

    case Doughnut = 'doughnut';

    case Pie = 'pie';

    case Line = 'line';

    case Mixed = 'mixed';

    case PolarArea = 'polarArea';

    case Radar = 'radar';

    case Scatter = 'scatter';
}
