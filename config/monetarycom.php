<?php
return [
    'datos' => [
        '2022' => [
            'capital_inicial' => [
                0.8, 2.0, 2.3, 4.2, 5.6, 6.9, 7.9, 9.4, 10.7, 11.7, 12.2, 13.3,
            ],
            // Cambiamos los nombres de los meses por índices (0-11)
            0 => [null, 1.2, 1.5, 3.4, 4.8, 6.1, 7.1, 8.5, 9.9, 10.8, 11.4, 12.5], // Enero
            1 => [null, null, 0.3, 2.2, 3.6, 4.8, 5.8, 7.2, 8.6, 9.5, 10.1, 11.1], // Febrero
            2 => [null, null, null, 1.9, 3.3, 4.5, 5.5, 6.9, 8.2, 9.2, 9.7, 10.8], // Marzo
            3 => [null, null, null, null, 1.4, 2.6, 3.6, 5.0, 6.3, 7.2, 7.7, 8.8], // Abril
            4 => [null, null, null, null, null, 1.2, 2.1, 3.5, 4.8, 5.7, 6.3, 7.3], // Mayo
            5 => [null, null, null, null, null, null, 0.9, 2.3, 3.6, 4.4, 5.0, 6.0], // Junio
            6 => [null, null, null, null, null, null, null, 1.4, 2.6, 3.5, 4.0, 5.0], // Julio
            7 => [null, null, null, null, null, null, null, null, 1.2, 2.1, 2.6, 3.6], // Agosto
            8 => [null, null, null, null, null, null, null, null, null, 0.9, 1.4, 2.4], // Septiembre
            9 => [null, null, null, null, null, null, null, null, null, null, 0.5, 1.5], // Octubre
            10 => [null, null, null, null, null, null, null, null, null, null, null, 1.0], // Noviembre
            11 => [null, null, null, null, null, null, null, null, null, null, null, null], // Diciembre
        ],
        '2023' => [
            'capital_inicial' => [
                0.3, 1.1, 1.0, 2.1, 2.4, 2.6, 2.4, 2.8, 2.9, 3.6, 4.0, 4.8,
            ],
            // Nuevos meses con índices (0-11)
            0 => [null, 0.8, 0.7, 1.8, 2.1, 2.3, 2.1, 2.5, 2.6, 3.3, 3.7, 4.5], // Enero
            1 => [null, null, -0.1, 1.0, 1.3, 1.5, 1.3, 1.7, 1.8, 2.5, 2.9, 3.7], // Febrero
            2 => [null, null, null, 1.1, 1.4, 1.5, 1.4, 1.7, 1.8, 2.5, 3.0, 3.7], // Marzo
            3 => [null, null, null, null, 0.3, 0.4, 0.3, 0.6, 0.7, 1.4, 1.9, 2.6], // Abril
            4 => [null, null, null, null, null, 0.1, 0.0, 0.3, 0.4, 1.1, 1.5, 2.3], // Mayo
            5 => [null, null, null, null, null, null, -0.2, 0.2, 0.3, 1.0, 1.4, 2.2], // Junio
            6 => [null, null, null, null, null, null, null, 0.3, 0.5, 1.1, 1.6, 2.3], // Julio
            7 => [null, null, null, null, null, null, null, null, 0.1, 0.8, 1.2, 2.0], // Agosto
            8 => [null, null, null, null, null, null, null, null, null, 0.7, 1.1, 1.9], // Septiembre
            9 => [null, null, null, null, null, null, null, null, null, null, 0.4, 1.2], // Octubre
            10 => [null, null, null, null, null, null, null, null, null, null, null, 0.7], // Noviembre
            11 => [null, null, null, null, null, null, null, null, null, null, null, null], // Diciembre
        ],
        '2024' => [
            'capital_inicial' => [
                -0.5, 0.1, 0.7, 1.1, 1.6, 1.9, 1.8, 2.6, 2.8, 2.9, 3.9, 4.2,
            ],
            // Ahora los meses son índices numéricos (0-11)
            0 => [null, 0.7, 1.3, 1.6, 2.2, 2.5, 2.4, 3.1, 3.4, 3.5, 4.5, 4.7],
            1 => [null, null, 0.6, 1.0, 1.5, 1.8, 1.7, 2.4, 2.7, 2.8, 3.8, 4.0],
            2 => [null, null, null, 0.4, 0.9, 1.2, 1.1, 1.8, 2.1, 2.2, 3.2, 3.4],
            3 => [null, null, null, null, 0.5, 0.8, 0.7, 1.5, 1.7, 1.8, 2.8, 3.0],
            4 => [null, null, null, null, null, 0.3, 0.2, 0.9, 1.2, 1.3, 2.2, 2.5],
            5 => [null, null, null, null, null, null, 0.1, 0.6, 0.9, 1.0, 2.0, 2.2],
            6 => [null, null, null, null, null, null, null, 0.7, 1.0, 1.1, 2.1, 2.3],
            7 => [null, null, null, null, null, null, null, null, 0.2, 0.3, 1.3, 1.6],
            8 => [null, null, null, null, null, null, null, null, null, 0.1, 1.1, 1.3],
            9 => [null, null, null, null, null, null, null, null, null, null, 1.0, 1.2],
            10 => [null, null, null, null, null, null, null, null, null, null, null, 0.3],
            11 => [null, null, null, null, null, null, null, null, null, null, null, null],
        ],
    ],
];
