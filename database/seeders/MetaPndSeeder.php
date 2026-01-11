<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetaPndSeeder extends Seeder
{
    public function run()
    {
        $metas = [
            // --- EJE SOCIAL ---
            // OBJETIVO 1: Salud, vivienda y bienestar
            [
                'codigo_meta' => 'M-1.1', 'id_objetivo_nacional' => 1,
                'nombre_meta' => 'Reducción de la pobreza extrema por ingresos',
                'nombre_indicador' => 'Tasa de pobreza extrema por ingresos',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 9.81, 'meta_valor' => 9.12,
                'descripcion_meta' => 'Referencia: INEC 2023. Población con ingresos menores a la canasta básica.'
            ],
            [
                'codigo_meta' => 'M-1.4', 'id_objetivo_nacional' => 1,
                'nombre_meta' => 'Reducción de la Desnutrición Crónica Infantil (DCI)',
                'nombre_indicador' => 'Prevalencia de DCI en menores de 2 años',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 20.10, 'meta_valor' => 18.70,
                'descripcion_meta' => 'Referencia: ENDI 2022-2023. Medición del retraso en el crecimiento.'
            ],
            [
                'codigo_meta' => 'M-1.10', 'id_objetivo_nacional' => 1,
                'nombre_meta' => 'Acceso a tratamiento antirretroviral para VIH',
                'nombre_indicador' => 'Personas con VIH que conocen su estado y están en tratamiento',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 84.90, 'meta_valor' => 87.42,
                'descripcion_meta' => 'Garantía de salud integral para grupos de atención prioritaria.'
            ],
            [
                'codigo_meta' => 'M-1.11', 'id_objetivo_nacional' => 1,
                'nombre_meta' => 'Reducción de la mortalidad por suicidio',
                'nombre_indicador' => 'Tasa de mortalidad por suicidio',
                'unidad_medida' => 'Tasa por 100k hab.', 'linea_base' => 6.48, 'meta_valor' => 6.31,
                'descripcion_meta' => 'Basado en el Registro Estadístico de Defunciones del INEC 2022.'
            ],
            [
                'codigo_meta' => 'M-1.14', 'id_objetivo_nacional' => 1,
                'nombre_meta' => 'Reducción del déficit habitacional',
                'nombre_indicador' => 'Déficit habitacional de vivienda',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 56.71, 'meta_valor' => 56.41,
                'descripcion_meta' => 'Meta vinculada al acceso equitativo a vivienda digna.'
            ],

            // OBJETIVO 2: Educación y cultura
            [
                'codigo_meta' => 'M-2.2', 'id_objetivo_nacional' => 2,
                'nombre_meta' => 'Acceso a educación inicial',
                'nombre_indicador' => 'Tasa neta de matrícula de educación inicial',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 56.63, 'meta_valor' => 60.65,
                'descripcion_meta' => 'Fuente: AMIE (Ministerio de Educación). Niños de 3 a 4 años.'
            ],
            [
                'codigo_meta' => 'M-2.4', 'id_objetivo_nacional' => 2,
                'nombre_meta' => 'Finalización del Bachillerato',
                'nombre_indicador' => 'Tasa neta de Bachillerato',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 70.35, 'meta_valor' => 71.39,
                'descripcion_meta' => 'Promoción de la permanencia y culminación de estudios.'
            ],
            [
                'codigo_meta' => 'M-2.8', 'id_objetivo_nacional' => 2,
                'nombre_meta' => 'Becas para educación superior',
                'nombre_indicador' => 'Número de becas y ayudas económicas adjudicadas',
                'unidad_medida' => 'Unidades', 'linea_base' => 20195, 'meta_valor' => 28696,
                'descripcion_meta' => 'Fuente: SENESCYT. Apoyo a grupos históricamente excluidos.'
            ],
            [
                'codigo_meta' => 'M-2.10', 'id_objetivo_nacional' => 2,
                'nombre_meta' => 'Reducción de deserción universitaria',
                'nombre_indicador' => 'Tasa de deserción de primer año en tercer nivel',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 20.98, 'meta_valor' => 17.99,
                'descripcion_meta' => 'Mejora de la permanencia en el Sistema de Educación Superior.'
            ],
            [
                'codigo_meta' => 'M-2.13', 'id_objetivo_nacional' => 2,
                'nombre_meta' => 'Impulso a la investigación científica',
                'nombre_indicador' => 'Investigadores por cada mil integrantes de la PEA',
                'unidad_medida' => 'Relación 1:1000', 'linea_base' => 0.63, 'meta_valor' => 0.75,
                'descripcion_meta' => 'Fomento a la innovación y desarrollo tecnológico (I+D+i).'
            ],

            // OBJETIVO 3: Seguridad y Justicia
            [
                'codigo_meta' => 'M-3.1', 'id_objetivo_nacional' => 3,
                'nombre_meta' => 'Reducción de la violencia criminal',
                'nombre_indicador' => 'Tasa de homicidios intencionales',
                'unidad_medida' => 'Tasa por 100k hab.', 'linea_base' => 45.11, 'meta_valor' => 39.11,
                'descripcion_meta' => 'Prioridad nacional para garantizar la paz ciudadana.'
            ],
            [
                'codigo_meta' => 'M-3.5', 'id_objetivo_nacional' => 3,
                'nombre_meta' => 'Desarticulación de bandas criminales',
                'nombre_indicador' => '% de afectación a estructuras de delincuencia organizada',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 0.00, 'meta_valor' => 85.00,
                'descripcion_meta' => 'Nivel de impacto en las capacidades logísticas de los GDO.'
            ],
            [
                'codigo_meta' => 'M-3.7', 'id_objetivo_nacional' => 3,
                'nombre_meta' => 'Defensa de la soberanía nacional',
                'nombre_indicador' => 'Ataques armados neutralizados que atenten al territorio',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 50.00, 'meta_valor' => 100.00,
                'descripcion_meta' => 'Garantía de la integridad territorial por parte del sector defensa.'
            ],
            [
                'codigo_meta' => 'M-3.10', 'id_objetivo_nacional' => 3,
                'nombre_meta' => 'Control del sistema penitenciario',
                'nombre_indicador' => 'Tasa de hacinamiento en Centros de Privación de Libertad',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 13.45, 'meta_valor' => 5.59,
                'descripcion_meta' => 'Mejora en las condiciones de rehabilitación social.'
            ],
            [
                'codigo_meta' => 'M-3.15', 'id_objetivo_nacional' => 3,
                'nombre_meta' => 'Identificación de riesgos cantonales',
                'nombre_indicador' => 'Índice de identificación del riesgo cantonal',
                'unidad_medida' => 'Índice (0-100)', 'linea_base' => 41.98, 'meta_valor' => 59.22,
                'descripcion_meta' => 'Preparación ante desastres de origen natural o antrópico.'
            ],

            // --- EJE DESARROLLO ECONÓMICO ---
            // OBJETIVO 4: Sistema económico e inversión
            [
                'codigo_meta' => 'M-4.1', 'id_objetivo_nacional' => 4,
                'nombre_meta' => 'Diversificación de exportaciones',
                'nombre_indicador' => '% de exportaciones no tradicionales en no petroleras',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 42.73, 'meta_valor' => 46.90,
                'descripcion_meta' => 'Fortalecimiento de las relaciones comerciales internacionales.'
            ],
            [
                'codigo_meta' => 'M-4.3', 'id_objetivo_nacional' => 4,
                'nombre_meta' => 'Fomento a la inversión privada',
                'nombre_indicador' => 'Monto de inversión privada registrada',
                'unidad_medida' => 'Millones USD', 'linea_base' => 2317.88, 'meta_valor' => 2423.89,
                'descripcion_meta' => 'Dinamización de la economía a través de capitales privados.'
            ],
            [
                'codigo_meta' => 'M-4.5', 'id_objetivo_nacional' => 4,
                'nombre_meta' => 'Atracción de capital extranjero',
                'nombre_indicador' => 'Inversión extranjera directa (IED)',
                'unidad_medida' => 'Millones USD', 'linea_base' => 845.05, 'meta_valor' => 846.10,
                'descripcion_meta' => 'Generación de empleo y transferencia tecnológica.'
            ],
            [
                'codigo_meta' => 'M-4.7', 'id_objetivo_nacional' => 4,
                'nombre_meta' => 'Sostenibilidad de ingresos estatales',
                'nombre_indicador' => 'Proporción del PGE financiado por tributos internos',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 32.37, 'meta_valor' => 34.16,
                'descripcion_meta' => 'Fortalecimiento del sistema tributario de forma equitativa.'
            ],
            [
                'codigo_meta' => 'M-4.9', 'id_objetivo_nacional' => 4,
                'nombre_meta' => 'Sostenibilidad de la deuda pública',
                'nombre_indicador' => 'Deuda pública consolidada como % del PIB',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 50.90, 'meta_valor' => 57.00,
                'descripcion_meta' => 'Mantener niveles de deuda bajo el techo del 57%.'
            ],

            // OBJETIVO 6: Empleo digno
            [
                'codigo_meta' => 'M-6.1', 'id_objetivo_nacional' => 6,
                'nombre_meta' => 'Incremento del empleo de calidad',
                'nombre_indicador' => 'Tasa de empleo adecuado (15 años y más)',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 34.41, 'meta_valor' => 39.09,
                'descripcion_meta' => 'Fuente: ENEMDU (INEC). Personas con ingresos >= al salario básico.'
            ],
            [
                'codigo_meta' => 'M-6.2', 'id_objetivo_nacional' => 6,
                'nombre_meta' => 'Reducción de la falta de empleo',
                'nombre_indicador' => 'Tasa de desempleo nacional',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 4.35, 'meta_valor' => 3.73,
                'descripcion_meta' => 'Monitoreo trimestral del mercado laboral.'
            ],
            [
                'codigo_meta' => 'M-6.3', 'id_objetivo_nacional' => 6,
                'nombre_meta' => 'Inserción laboral de jóvenes',
                'nombre_indicador' => 'Tasa de desempleo juvenil (18 a 29 años)',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 9.29, 'meta_valor' => 8.00,
                'descripcion_meta' => 'Estrategia para reducir la vulnerabilidad de la juventud ante el delito.'
            ],
            [
                'codigo_meta' => 'M-6.4', 'id_objetivo_nacional' => 6,
                'nombre_meta' => 'Erradicación del trabajo infantil',
                'nombre_indicador' => 'Incidencia de trabajo infantil (5 a 14 años)',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 5.78, 'meta_valor' => 4.90,
                'descripcion_meta' => 'Garantía de derechos y protección de la infancia.'
            ],
            [
                'codigo_meta' => 'M-6.6', 'id_objetivo_nacional' => 6,
                'nombre_meta' => 'Igualdad salarial de género',
                'nombre_indicador' => 'Brecha salarial entre hombres y mujeres',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 19.23, 'meta_valor' => 18.17,
                'descripcion_meta' => 'Hacia la igualdad de retribución por trabajo de igual valor.'
            ],

            // --- EJE INFRAESTRUCTURA Y MEDIO AMBIENTE ---
            // OBJETIVO 7: Recursos naturales y energía
            [
                'codigo_meta' => 'M-7.1', 'id_objetivo_nacional' => 7,
                'nombre_meta' => 'Ampliación de la matriz energética',
                'nombre_indicador' => 'Capacidad instalada de nueva generación eléctrica',
                'unidad_medida' => 'Megavatios (MW)', 'linea_base' => 7154.57, 'meta_valor' => 8584.38,
                'descripcion_meta' => 'Fuente: ARCERNNR. Basado en el Plan Maestro de Electricidad.'
            ],
            [
                'codigo_meta' => 'M-7.8', 'id_objetivo_nacional' => 7,
                'nombre_meta' => 'Crecimiento de exportaciones mineras',
                'nombre_indicador' => 'Monto total de exportaciones mineras',
                'unidad_medida' => 'Millones USD', 'linea_base' => 2775.00, 'meta_valor' => 3515.00,
                'descripcion_meta' => 'Desarrollo responsable y sostenible del sector minero.'
            ],
            [
                'codigo_meta' => 'M-7.11', 'id_objetivo_nacional' => 7,
                'nombre_meta' => 'Acceso universal al agua potable',
                'nombre_indicador' => 'Población con acceso a agua apta para consumo humano',
                'unidad_medida' => 'Número personas', 'linea_base' => 3017778, 'meta_valor' => 4007994,
                'descripcion_meta' => 'Gestión integrada e integrada del recurso hídrico.'
            ],
            [
                'codigo_meta' => 'M-7.12', 'id_objetivo_nacional' => 7,
                'nombre_meta' => 'Gestión de residuos del productor',
                'nombre_indicador' => 'Residuos recuperados bajo responsabilidad extendida',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 44.06, 'meta_valor' => 56.06,
                'descripcion_meta' => 'Implementación de modelos de economía circular.'
            ],
            [
                'codigo_meta' => 'M-7.14', 'id_objetivo_nacional' => 7,
                'nombre_meta' => 'Conservación del patrimonio natural',
                'nombre_indicador' => 'Territorio nacional bajo conservación o manejo ambiental',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 22.16, 'meta_valor' => 22.16,
                'descripcion_meta' => 'Mantenimiento de áreas protegidas (SNAP) y bosques.'
            ],

            // --- EJE INSTITUCIONAL ---
            // OBJETIVO 9: Estado eficiente y transparente
            [
                'codigo_meta' => 'M-9.1', 'id_objetivo_nacional' => 9,
                'nombre_meta' => 'Calidad de servicios públicos',
                'nombre_indicador' => 'Índice de percepción de la calidad de servicios',
                'unidad_medida' => 'Índice (0-10)', 'linea_base' => 6.05, 'meta_valor' => 6.20,
                'descripcion_meta' => 'Percepción ciudadana sobre la eficiencia institucional.'
            ],
            [
                'codigo_meta' => 'M-9.3', 'id_objetivo_nacional' => 9,
                'nombre_meta' => 'Integridad pública y lucha anticorrupción',
                'nombre_indicador' => 'Ranking de percepción de corrupción (Transparency Intl.)',
                'unidad_medida' => 'Puesto Mundial', 'linea_base' => 115, 'meta_valor' => 109,
                'descripcion_meta' => 'Meta de mejora en el posicionamiento internacional.'
            ],
            [
                'codigo_meta' => 'M-9.4', 'id_objetivo_nacional' => 9,
                'nombre_meta' => 'Cooperación internacional para el desarrollo',
                'nombre_indicador' => 'Monto desembolsado de Cooperación No Reembolsable',
                'unidad_medida' => 'Millones USD', 'linea_base' => 261.71, 'meta_valor' => 327.14,
                'descripcion_meta' => 'Articulación con fuentes bilaterales y multilaterales.'
            ],
            [
                'codigo_meta' => 'M-9.7', 'id_objetivo_nacional' => 9,
                'nombre_meta' => 'Implementación de Gobierno Abierto',
                'nombre_indicador' => 'Entidades públicas que implementan modelo Gobierno Abierto',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 40.00, 'meta_valor' => 52.27,
                'descripcion_meta' => 'Fomento a la transparencia y rendición de cuentas.'
            ],
            [
                'codigo_meta' => 'M-9.9', 'id_objetivo_nacional' => 9,
                'nombre_meta' => 'Rendición de cuentas de autoridades',
                'nombre_indicador' => 'Autoridades de elección popular que rinden cuentas',
                'unidad_medida' => 'Porcentaje (%)', 'linea_base' => 63.20, 'meta_valor' => 63.95,
                'descripcion_meta' => 'Control social y vigilancia de la gestión pública.'
            ],
        ];

        foreach ($metas as $meta) {
            DB::table('cat_meta_nacional')->insert(array_merge($meta, [
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
