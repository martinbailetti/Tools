<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Quill.js WYSIWYG Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.snow.min.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">

    <!-- Dynamic Fonts CSS -->
    <style id="dynamic-fonts">

    </style>

    <style>
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .form-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon-container {
            position: relative;
        }

        .input-icon-container input {
            padding-right: 35px;
        }

        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 9999;
        }

        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        /* Estilos para el selector de fuentes de Quill */
        .ql-font-ZenAntiqueRegular .ql-picker.ql-font .ql-picker-label[data-value="sofia"]::before {
            content: 'Sofia';
            font-family: 'Sofia';
        }

        /* Mejorar visibilidad del dropdown de fuentes */
        .ql-picker-options {
            max-height: 250px;
            overflow-y: auto;
        }

        /* Previsualización de fuentes en el dropdown */
        .ql-picker.ql-font .ql-picker-item {
            padding: 5px 12px;
        }

        /* Estilos para el preview */
        #previewFrame {
            transition: opacity 0.3s ease;
        }

        #previewLoading {
            background: rgba(248, 249, 250, 0.95);
            backdrop-filter: blur(2px);
        }

        /* Clase para forzar ocultación del loading */
        #previewLoading.force-hide {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }

        /* Responsive para pantallas pequeñas */
        @media (max-width: 991.98px) {
            #previewFrame {
                height: 400px !important;
            }
        }

        /* Layout sin scroll para md y superiores */
        @media (min-width: 992px) {
            /* Hacer que body y html ocupen toda la pantalla sin scroll */
            html, body {
                height: 100vh;
                overflow: hidden;
            }

            /* Container fluid debe ocupar toda la altura */
            .container-fluid {
                height: 100vh;
                padding: 0;
                margin: 0;
            }

            /* Row debe ocupar toda la altura */
            .container-fluid > .row {
                height: 100vh;
                margin: 0;
            }

            /* Columna izquierda con scroll propio */
            .col-lg-6.col-xl-7 {
                height: 100vh;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 15px;
            }

            /* Columna derecha con altura fija */
            .col-lg-6.col-xl-5 {
                height: 100vh;
                overflow: hidden;
                padding: 15px;
            }

            /* Card del preview debe ocupar toda la altura disponible */
            .col-lg-6.col-xl-5 .card {
                height: calc(100vh - 30px);
            }

            /* Card body del preview sin scroll adicional */
            .col-lg-6.col-xl-5 .card-body {
                height: calc(100% - 60px); /* Restar altura del header */
                overflow: hidden;
            }

            /* Iframe del preview debe ajustarse a la altura disponible */
            #previewFrame {
                height: 100% !important;
                min-height: unset !important;
            }
        }

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Columna izquierda: Controles -->
            <div class="col-lg-6 col-xl-7">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-file-pdf me-2"></i>
                            Generador de PDFs - Color Book
                        </h1>
                    </div>
                    <div class="card-body">
                        <!-- Alert container for messages -->
                        <div id="alertContainer"></div>

                        <form id="pdfGeneratorForm">
                            @csrf

                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Información:</strong> Puedes modificar los parámetros de generación en la sección <strong>"Configuración de Plantilla"</strong> más abajo.
                            </div>

                            <div class="mb-3">
                                <label for="jsonFileSelector" class="form-label">
                                    <i class="fas fa-file-code me-1"></i>
                                    Archivo de Configuración JSON
                                </label>
                                <div class="input-icon-container">
                                    <select class="form-select" id="jsonFileSelector" name="jsonFileSelector">
                                        @foreach($jsonFiles as $jsonFile)
                                        <option value="{{ $jsonFile['path'] }}" @if($jsonFile['filename']==='chinese.json' ) selected @endif>
                                            {{ $jsonFile['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-file-code form-icon"></i>
                                </div>
                                <div class="form-text">Selecciona el archivo JSON de configuración a usar</div>
                            </div>

                            <div class="mb-3">
                                <label for="numberOfPages" class="form-label">
                                    <i class="fas fa-sort-numeric-up me-1"></i>
                                    Número de Páginas (opcional)
                                </label>
                                <div class="input-icon-container">
                                    <input type="number" class="form-control" name="numberOfPages" id="numberOfPages" value="1" min="0" placeholder="0">
                                    <i class="fas fa-hashtag form-icon"></i>
                                </div>
                                <div class="form-text">Si es 0 o se deja vacío, se tomarán todas las páginas disponibles</div>
                            </div>
                            <button type="submit" class="btn btn-outline-dark btn-lg w-100" id="generateBtn">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        Generación de PDF
                                    </h5>
                                    <p class="text-center">
                                        Genera el PDF con la configuración actual del archivo JSON
                                    </p>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Template Configuration Card -->
                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-palette me-2"></i>
                            Configuración de Plantilla
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="templateAccordion">
                            <!-- General Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingGeneral">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeneral">
                                        <i class="fas fa-cog me-2"></i>
                                        General
                                    </button>
                                </h2>
                                <div id="collapseGeneral" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <i class="fas fa-arrows-alt-h me-1"></i>
                                                    Ancho (width)
                                                </label>
                                                <input type="number" class="form-control" id="config_width" step="0.01" value="21.59">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <i class="fas fa-arrows-alt-v me-1"></i>
                                                    Alto (height)
                                                </label>
                                                <input type="number" class="form-control" id="config_height" step="0.01" value="27.94">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-indent me-1"></i>
                                                    Margen Interno (margin-in)
                                                </label>
                                                <input type="number" class="form-control" id="config_margin_in" step="0.01" value="0.95">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-outdent me-1"></i>
                                                    Margen Externo (margin-out)
                                                </label>
                                                <input type="number" class="form-control" id="config_margin_out" step="0.01" value="0.64">
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <hr>
                                                <h6 class="text-muted">
                                                    <i class="fas fa-database me-2"></i>
                                                    Configuración de Datos
                                                </h6>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-table me-1"></i>
                                                    ID de Google Spreadsheet
                                                </label>
                                                <input type="text" class="form-control" id="config_spreadsheet_id" value="1nYdFCcD5hLjPmz1xmddfNupjItjOI4riFzgpKX9Bq7k">
                                                <div class="form-text">ID de la hoja de cálculo de Google Drive</div>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-file-alt me-1"></i>
                                                    Nombre de la Hoja
                                                </label>
                                                <input type="text" class="form-control" id="config_spreadsheet_sheet_name" value="Chino">
                                                <div class="form-text">Nombre de la pestaña en la hoja de cálculo</div>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-images me-1"></i>
                                                    URL Base de Imágenes
                                                </label>
                                                <input type="url" class="form-control" id="config_images_url" value="https://printables.happycapibara.com/color-books/chinese/">
                                                <div class="form-text">URL donde se encuentran las imágenes (debe terminar en /)</div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <hr>
                                                <h6 class="text-muted">
                                                    <i class="fas fa-font me-2"></i>
                                                    Configuración de Fuentes
                                                </h6>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-font me-1"></i>
                                                    Fuentes a Incrustar en el PDF
                                                </label>
                                                <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                                    <div id="config_fonts_container">
                                                        <div class="text-muted text-center">
                                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                                            Cargando fuentes disponibles...
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text">
                                                    <i class="fas fa-server me-1"></i>
                                                    Las fuentes disponibles se cargan desde el servidor y se guardan en la configuración JSON
                                                </div>
                                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllFonts">
                                                            <i class="fas fa-check-double me-1"></i>
                                                            Seleccionar Todas
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="deselectAllFonts">
                                                            <i class="fas fa-times me-1"></i>
                                                            Deseleccionar Todas
                                                        </button>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted" id="fontCounter">Fuentes: 0</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Verso Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingVerso">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVerso">
                                        <i class="fas fa-file-alt me-2"></i>
                                        Verso
                                    </button>
                                </h2>
                                <div id="collapseVerso" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <i class="fas fa-border-style me-1"></i>
                                                    Margen
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_margin" step="0.01" value="0.5">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <i class="fas fa-square-full me-1"></i>
                                                    Margen del Borde
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_border_margin" step="0.01">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    Margen de Imagen
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_image_margin" step="0.01">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">

                                                    <i class="fa-solid fa-ruler-vertical me-1"></i>
                                                    Posición de Texto 1
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_text_1_top" step="0.01">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">

                                                    <i class="fa-solid fa-ruler-vertical me-1"></i>
                                                    Posición de Texto 2
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_text_2_top" step="0.01">
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-font me-1"></i>
                                                    Tamaño Fuente Principal
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_primary_font_size" step="0.1">
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-text-height me-1"></i>
                                                    Tamaño Fuente Secundaria
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_secondary_font_size" step="0.1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recto Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingRecto">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRecto">
                                        <i class="fas fa-file me-2"></i>
                                        Recto
                                    </button>
                                </h2>
                                <div id="collapseRecto" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-border-style me-1"></i>
                                                    Margen
                                                </label>
                                                <input type="number" class="form-control" id="config_recto_margin" step="0.01" value="0.5">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    Margen de Imagen
                                                </label>
                                                <input type="number" class="form-control" id="config_recto_image_margin" step="0.01" value="0.25">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-font me-1"></i>
                                                    Tamaño de Fuente
                                                </label>
                                                <input type="number" class="form-control" id="config_recto_font_size" step="0.1" value="8">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-percentage me-1"></i>
                                                    Porcentaje Y del Texto
                                                </label>
                                                <input type="number" class="form-control" id="config_recto_text_top" step="0.01" value="0.53">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page 1 Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPage1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePage1">
                                        <i class="fas fa-file-invoice me-2"></i>
                                        Página 1
                                    </button>
                                </h2>
                                <div id="collapsePage1" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <i class="fas fa-heading me-1"></i>
                                                    Título Línea 1
                                                </label>
                                                <input type="text" class="form-control" id="config_page1_title_line_1" value="El secreto">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Y Porcentaje</label>
                                                <input type="number" class="form-control" id="config_page1_title_line_1_y_percentage" step="0.01" value="0.4">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tamaño Fuente</label>
                                                <input type="number" class="form-control" id="config_page1_title_line_1_font_size" step="0.1" value="1.7">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-heading me-1"></i>
                                                    Título Línea 2
                                                </label>
                                                <input type="text" class="form-control" id="config_page1_title_line_2" value="de los">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Tamaño Fuente</label>
                                                <input type="number" class="form-control" id="config_page1_title_line_2_font_size" step="0.1" value="1">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Margen</label>
                                                <input type="number" class="form-control" id="config_page1_title_line_2_margin" step="0.01" value="0.7">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-heading me-1"></i>
                                                    Título Línea 3
                                                </label>
                                                <input type="text" class="form-control" id="config_page1_title_line_3" value="Ideogramas Chinos">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Margen</label>
                                                <input type="number" class="form-control" id="config_page1_title_line_3_margin" step="0.01" value="0.15">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Tamaño Fuente</label>
                                                <input type="number" class="form-control" id="config_page1_title_line_3_font_size" step="0.1" value="1.7">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL del Logo
                                                </label>
                                                <input type="url" class="form-control" id="config_page1_logo_image" value="https://printables.happycapibara.com/logo.png">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Logo Y Porcentaje</label>
                                                <input type="number" class="form-control" id="config_page1_logo_y_percentage" step="0.01" value="0.9">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Altura del Logo</label>
                                                <input type="number" class="form-control" id="config_page1_logo_height" step="0.1" value="3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page 2 Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPage2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePage2">
                                        <i class="fas fa-file-invoice me-2"></i>
                                        Página 2
                                    </button>
                                </h2>
                                <div id="collapsePage2" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-percentage me-1"></i>
                                                    Texto Y Porcentaje
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_text_top" step="0.01" value="0.3">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-arrows-alt-v me-1"></i>
                                                    Espaciado Y del Texto
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_text_y_space" step="0.01" value="0.3">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-font me-1"></i>
                                                    Tamaño de Fuente
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_text_font_size" step="0.01" value="0.3">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-arrows-alt-h me-1"></i>
                                                    Margen X
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_margin_x" step="0.01" value="0.3">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-arrow-down me-1"></i>
                                                    Margen Inferior
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_margin_bottom" step="0.01" value="0.2">
                                            </div>
                                        </div>

                                        <!-- Editores de Texto con Quill.js -->
                                        <div class="mt-4">
                                            <h5 class="text-primary">
                                                <i class="fas fa-edit me-2"></i>
                                                Contenido de Texto
                                            </h5>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Bloque 1 - Título y Subtítulo</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page2_text_block_1')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page2_text_block_1_editor" style="height: 120px;"></div>
                                                <textarea id="config_page2_text_block_1_source" class="form-control" style="height: 120px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page2_text_block_1" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Bloque 2 - Copyright</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page2_text_block_2')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page2_text_block_2_editor" style="height: 120px;"></div>
                                                <textarea id="config_page2_text_block_2_source" class="form-control" style="height: 120px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page2_text_block_2" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Bloque 3 - Términos Legales</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page2_text_block_3')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page2_text_block_3_editor" style="height: 150px;"></div>
                                                <textarea id="config_page2_text_block_3_source" class="form-control" style="height: 150px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page2_text_block_3" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Bloque 4 - Información Editorial</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page2_text_block_4')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page2_text_block_4_editor" style="height: 150px;"></div>
                                                <textarea id="config_page2_text_block_4_source" class="form-control" style="height: 150px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page2_text_block_4" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page 3 Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPage3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePage3">
                                        <i class="fas fa-file-invoice me-2"></i>
                                        Página 3
                                    </button>
                                </h2>
                                <div id="collapsePage3" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL de la Imagen
                                                </label>
                                                <input type="url" class="form-control" id="config_page3_image" value="https://printables.happycapibara.com/color-books/birds.png">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-percentage me-1"></i>
                                                    Imagen Y Porcentaje
                                                </label>
                                                <input type="number" class="form-control" id="config_page3_image_y_percentage" step="0.01" value="0.3">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-border-style me-1"></i>
                                                    Margen
                                                </label>
                                                <input type="number" class="form-control" id="config_page3_margin" step="0.01" value="0.2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page 4 Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPage4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePage4">
                                        <i class="fas fa-file-invoice me-2"></i>
                                        Página 4
                                    </button>
                                </h2>
                                <div id="collapsePage4" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL de la Imagen
                                                </label>
                                                <input type="url" class="form-control" id="config_page4_image" value="https://printables.happycapibara.com/color-books/chinese_landscape.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page 5 Configuration -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPage5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePage5">
                                        <i class="fas fa-file-invoice me-2"></i>
                                        Página 5
                                    </button>
                                </h2>
                                <div id="collapsePage5" class="accordion-collapse collapse" data-bs-parent="#templateAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-12 mb-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL de la Imagen
                                                </label>
                                                <input type="url" class="form-control" id="config_page5_image" value="https://printables.happycapibara.com/color-books/chinese_background.png">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Header Y</label>
                                                <input type="number" class="form-control" id="config_page5_header_y" step="0.1" value="1.5">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Header Font Size</label>
                                                <input type="number" class="form-control" id="config_page5_header_font_size" step="0.1" value="0.5">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Footer Y</label>
                                                <input type="number" class="form-control" id="config_page5_footer_y" step="0.1" value="24">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Footer Font Size</label>
                                                <input type="number" class="form-control" id="config_page5_footer_font_size" step="0.1" value="0.5">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Text Font Size</label>
                                                <input type="number" class="form-control" id="config_page5_text_font_size" step="0.01" value="0.45">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Text 1 Y</label>
                                                <input type="number" class="form-control" id="config_page5_text_1_y" step="0.1" value="6">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Text 2 Y</label>
                                                <input type="number" class="form-control" id="config_page5_text_2_y" step="0.1" value="10.2">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Text 3 Y</label>
                                                <input type="number" class="form-control" id="config_page5_text_3_y" step="0.1" value="17.5">
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-border-style me-1"></i>
                                                    Margen
                                                </label>
                                                <input type="number" class="form-control" id="config_page5_margin" step="0.1" value="1">
                                            </div>
                                        </div>

                                        <!-- Editores de Texto para Página 5 con Quill.js -->
                                        <div class="mt-4">
                                            <h5 class="text-primary">
                                                <i class="fas fa-edit me-2"></i>
                                                Contenido de Texto
                                            </h5>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Texto del Header</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page5_header_text')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page5_header_text_editor" style="height: 120px;"></div>
                                                <textarea id="config_page5_header_text_source" class="form-control" style="height: 120px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page5_header_text" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Texto del Footer</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page5_footer_text')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page5_footer_text_editor" style="height: 120px;"></div>
                                                <textarea id="config_page5_footer_text_source" class="form-control" style="height: 120px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page5_footer_text" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Contenido Texto 1</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page5_text_1_content')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page5_text_1_content_editor" style="height: 150px;"></div>
                                                <textarea id="config_page5_text_1_content_source" class="form-control" style="height: 150px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page5_text_1_content" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Contenido Texto 2</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page5_text_2_content')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page5_text_2_content_editor" style="height: 180px;"></div>
                                                <textarea id="config_page5_text_2_content_source" class="form-control" style="height: 180px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page5_text_2_content" />
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Contenido Texto 3</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page5_text_3_content')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page5_text_3_content_editor" style="height: 180px;"></div>
                                                <textarea id="config_page5_text_3_content_source" class="form-control" style="height: 180px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page5_text_3_content" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-warning me-2" id="saveConfigBtn">
                                <i class="fas fa-save me-1"></i>
                                Guardar Configuración
                            </button>
                            <button type="button" class="btn btn-primary me-2" id="loadConfigBtn">
                                <i class="fas fa-download me-1"></i>
                                Cargar Configuración
                            </button>
                            <button type="button" class="btn btn-secondary" id="resetConfigBtn">
                                <i class="fas fa-undo me-1"></i>
                                Resetear
                            </button>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Los cambios se aplican automáticamente al generar el PDF
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Instructions Card -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Instrucciones
                        </h3>
                    </div>
                    <div class="card-body">
                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item d-flex align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <i class="fab fa-google-drive me-1"></i>
                                        ID de Google Spreadsheet
                                    </div>
                                    Copia el ID desde la URL de tu Google Sheet
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <i class="fas fa-file-alt me-1"></i>
                                        Nombre de la Hoja
                                    </div>
                                    Escribe el nombre exacto de la pestaña
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <i class="fas fa-sort-numeric-up me-1"></i>
                                        Número de Páginas
                                    </div>
                                    Opcional. Si no se especifica, se procesarán todas las filas
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <i class="fas fa-link me-1"></i>
                                        URL de Imágenes
                                    </div>
                                    La URL base donde están las imágenes (debe ser accesible públicamente)
                                </div>
                            </li>
                        </ol>

                        <div class="mt-3">
                            <h5>
                                <i class="fas fa-columns me-2"></i>
                                Columnas requeridas en el Excel:
                            </h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="fas fa-language me-2 text-primary"></i>
                                    <strong>EN:</strong> Texto en inglés (para generar el nombre del archivo de imagen)
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-star me-2 text-warning"></i>
                                    <strong>SYM:</strong> Símbolo principal
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-star-half-alt me-2 text-info"></i>
                                    <strong>SY2:</strong> Símbolo secundario
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-comment me-2 text-success"></i>
                                    <strong>ES:</strong> Texto en español
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Preview -->
            <div class="col-lg-6 col-xl-5">
                <div class="card shadow h-100">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Vista Previa PDF
                        </h3>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-light" id="refreshPreviewBtn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light" id="openPreviewNewTab">
                                <i class="fas fa-external-link-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" id="forceHideLoadingBtn" title="Forzar ocultar loading">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 position-relative">
                        <!-- Loading indicator para el preview -->
                        <div id="previewLoading" class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center bg-light" style="z-index: 10; display: none;">
                            <div class="text-center">
                                <div class="spinner-border text-secondary mb-3" role="status">
                                    <span class="visually-hidden">Cargando preview...</span>
                                </div>
                                <p class="text-muted">Generando vista previa PDF...</p>
                            </div>
                        </div>

                        <!-- Iframe para el preview PDF -->
                        <iframe
                            id="previewFrame"
                            class="w-100 h-100"
                            style="min-height: 800px; border: none; background: #f5f5f5;"
                            src="about:blank"
                            allow="fullscreen">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h4>
                <i class="fas fa-cogs me-2"></i>
                Generando PDF...
            </h4>
            <p class="text-muted">Esto puede tomar varios minutos</p>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Quill.js WYSIWYG Editor -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <!-- Elemento style para fuentes dinámicas (debe existir antes que cualquier JavaScript) -->
    <style id="dynamic-fonts" type="text/css">
        /* Placeholder para fuentes dinámicas */
    </style>

    <script>
        // Fallback para Quill.js desde otra CDN si falla
        if (typeof Quill === 'undefined') {
            console.log('Cargando Quill.js desde CDN alternativo...');
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.min.js"><\/script>');
        }

    </script>

    <script>
        // Variables globales para el manejo de fuentes
        let availableFonts = [];
        let fontsLoaded = false;
        let editorsInitialized = false;
        let quillEditors = {};

        // Variables globales para el preview
        let previewTimeout = null;
        let previewUpdateDelay = 1000; // 1 segundo de delay para evitar muchas llamadas (reducido de 2)

        // Función para obtener contenido de editores Quill (definida temprano para usar en updatePreview)
        function getQuillContent(hiddenId) {
            const sourceElement = document.getElementById(hiddenId + '_source');
            const editor = quillEditors[hiddenId];

            // Si el textarea de código está visible, usar su contenido
            if (sourceElement && sourceElement.style.display !== 'none') {
                return sourceElement.value;
            }

            // Si no, usar el contenido del editor Quill si existe
            if (editor && editor.root) {
                return editor.root.innerHTML;
            }

            // Fallback: usar el valor del input hidden
            const hiddenElement = document.getElementById(hiddenId);
            return hiddenElement ? hiddenElement.value : '';
        }

        // Función robusta para ocultar el loading del preview
        function hidePreviewLoading() {
            console.log('Intentando ocultar loading preview...');

            const $loading = $('#previewLoading');

            // Múltiples métodos para asegurar que se oculte
            $loading.hide();
            $loading.css({
                'display': 'none',
                'visibility': 'hidden',
                'opacity': '0'
            });
            $loading.addClass('force-hide');

            // Verificar que efectivamente se ocultó
            setTimeout(() => {
                if ($loading.is(':visible') || $loading.css('display') !== 'none') {
                    console.warn('Loading aún visible, forzando ocultación adicional...');
                    $loading.attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important;');
                    $loading[0].style.setProperty('display', 'none', 'important');
                } else {
                    console.log('Loading ocultado exitosamente');
                }
            }, 100);
        }

        // Función para detectar cuando todas las imágenes del iframe hayan cargado
        function waitForIframeImages(iframe, callback) {
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const images = iframeDoc.querySelectorAll('img');

                if (images.length === 0) {
                    console.log('No hay imágenes en el iframe, ejecutando callback inmediatamente');
                    callback();
                    return;
                }

                let loadedImages = 0;
                const totalImages = images.length;
                console.log(`Esperando que carguen ${totalImages} imágenes en el iframe...`);

                const imageLoadHandler = () => {
                    loadedImages++;
                    console.log(`Imagen ${loadedImages}/${totalImages} cargada`);

                    if (loadedImages === totalImages) {
                        console.log('Todas las imágenes del iframe han cargado');
                        callback();
                    }
                };

                // Verificar cada imagen
                images.forEach((img, index) => {
                    if (img.complete && img.naturalWidth > 0) {
                        // La imagen ya está cargada
                        imageLoadHandler();
                    } else {
                        // Esperar a que la imagen cargue
                        img.onload = imageLoadHandler;
                        img.onerror = () => {
                            console.warn(`Error cargando imagen ${index + 1}, continuando...`);
                            imageLoadHandler(); // Contar como cargada aunque haya error
                        };
                    }
                });

                // Timeout de seguridad para imágenes
                setTimeout(() => {
                    if (loadedImages < totalImages) {
                        console.warn(`Timeout: Solo ${loadedImages}/${totalImages} imágenes cargaron, continuando...`);
                        callback();
                    }
                }, 8000); // 8 segundos máximo para imágenes

            } catch (error) {
                console.warn('Error accediendo al contenido del iframe:', error);
                callback(); // Continuar aunque haya error
            }
        }

        // Función para actualizar el preview
        function updatePreview(force = false) {
            console.log('=== updatePreview llamado ===');
            console.log('Force:', force);
            console.log('PreviewTimeout existe:', !!previewTimeout);

            // Cancelar timeout anterior si existe
            if (previewTimeout) {
                clearTimeout(previewTimeout);
                console.log('Timeout anterior cancelado');
            }

            // Si no es forzado, esperar un poco antes de actualizar
            if (!force) {
                console.log(`Programando preview en ${previewUpdateDelay}ms...`);
                previewTimeout = setTimeout(() => updatePreview(true), previewUpdateDelay);
                return;
            }

            console.log('Ejecutando actualización de preview...');

            // Mostrar loading y ocultar placeholder
            console.log('Mostrando loading preview...');
            const $loading = $('#previewLoading');

            // Remover clase force-hide y mostrar loading
            $loading.removeClass('force-hide');
            $loading.css({
                'display': 'flex',
                'visibility': 'visible',
                'opacity': '1'
            }).show();

            // Obtener configuración del JSON seleccionado
            const selectedJsonFile = $('#jsonFileSelector').val() || '/json/chinese.json';

            // Leer configuración desde el JSON para obtener parámetros básicos
            $.get(selectedJsonFile)
                .done(function(config) {
                    console.log('Configuración cargada para preview:', config);

                    // Obtener valores actuales del formulario y sobrescribir la configuración
                    const updatedConfig = { ...config }; // Copiar configuración base

                    // Actualizar con valores del formulario si existen
                    if ($('#config_width').length && $('#config_width').val()) {
                        updatedConfig.width = parseFloat($('#config_width').val());
                    }
                    if ($('#config_height').length && $('#config_height').val()) {
                        updatedConfig.height = parseFloat($('#config_height').val());
                    }
                    if ($('#config_margin_in').length && $('#config_margin_in').val()) {
                        updatedConfig['margin-in'] = parseFloat($('#config_margin_in').val());
                    }
                    if ($('#config_margin_out').length && $('#config_margin_out').val()) {
                        updatedConfig['margin-out'] = parseFloat($('#config_margin_out').val());
                    }
                    if ($('#config_spreadsheet_id').length && $('#config_spreadsheet_id').val()) {
                        updatedConfig.spreadsheetId = $('#config_spreadsheet_id').val();
                    }
                    if ($('#config_spreadsheet_sheet_name').length && $('#config_spreadsheet_sheet_name').val()) {
                        updatedConfig.spreadsheetSheetName = $('#config_spreadsheet_sheet_name').val();
                    }
                    if ($('#config_images_url').length && $('#config_images_url').val()) {
                        updatedConfig.imagesURL = $('#config_images_url').val();
                    }

                    // Actualizar configuraciones de verso
                    if (!updatedConfig.verso) updatedConfig.verso = {};
                    if ($('#config_verso_margin').length && $('#config_verso_margin').val()) {
                        updatedConfig.verso.margin = parseFloat($('#config_verso_margin').val());
                    }
                    if ($('#config_verso_border_margin').length && $('#config_verso_border_margin').val()) {
                        updatedConfig.verso['border-margin'] = parseFloat($('#config_verso_border_margin').val());
                    }
                    if ($('#config_verso_image_margin').length && $('#config_verso_image_margin').val()) {
                        updatedConfig.verso['image-margin'] = parseFloat($('#config_verso_image_margin').val());
                    }
                    if ($('#config_verso_text_1_top').length && $('#config_verso_text_1_top').val()) {
                        updatedConfig.verso['text-1-top'] = parseFloat($('#config_verso_text_1_top').val());
                    }
                    if ($('#config_verso_text_2_top').length && $('#config_verso_text_2_top').val()) {
                        updatedConfig.verso['text-2-top'] = parseFloat($('#config_verso_text_2_top').val());
                    }

                    if ($('#config_verso_primary_font_size').length && $('#config_verso_primary_font_size').val()) {
                        updatedConfig.verso['primary-font-size'] = parseFloat($('#config_verso_primary_font_size').val());
                    }
                    if ($('#config_verso_secondary_font_size').length && $('#config_verso_secondary_font_size').val()) {
                        updatedConfig.verso['secondary-font-size'] = parseFloat($('#config_verso_secondary_font_size').val());
                    }

                    // Actualizar configuraciones de recto
                    if (!updatedConfig.recto) updatedConfig.recto = {};
                    if ($('#config_recto_margin').length && $('#config_recto_margin').val()) {
                        updatedConfig.recto.margin = parseFloat($('#config_recto_margin').val());
                    }
                    if ($('#config_recto_image_margin').length && $('#config_recto_image_margin').val()) {
                        updatedConfig.recto['image-margin'] = parseFloat($('#config_recto_image_margin').val());
                    }
                    if ($('#config_recto_font_size').length && $('#config_recto_font_size').val()) {
                        updatedConfig.recto['font-size'] = parseFloat($('#config_recto_font_size').val());
                    }
                    if ($('#config_recto_text_top').length && $('#config_recto_text_top').val()) {
                        updatedConfig.recto['text-top'] = parseFloat($('#config_recto_text_top').val());
                    }

                    // Actualizar configuraciones de páginas específicas
                    // Page 1
                    if (!updatedConfig.page1) updatedConfig.page1 = {};
                    if ($('#config_page1_title_line_1').length && $('#config_page1_title_line_1').val()) {
                        updatedConfig.page1['title-line-1'] = $('#config_page1_title_line_1').val();
                    }
                    if ($('#config_page1_title_line_1_y_percentage').length && $('#config_page1_title_line_1_y_percentage').val()) {
                        updatedConfig.page1['title-line-1-y-percentage'] = parseFloat($('#config_page1_title_line_1_y_percentage').val());
                    }
                    if ($('#config_page1_title_line_1_font_size').length && $('#config_page1_title_line_1_font_size').val()) {
                        updatedConfig.page1['title-line-1-font-size'] = parseFloat($('#config_page1_title_line_1_font_size').val());
                    }
                    if ($('#config_page1_title_line_2').length && $('#config_page1_title_line_2').val()) {
                        updatedConfig.page1['title-line-2'] = $('#config_page1_title_line_2').val();
                    }
                    if ($('#config_page1_title_line_2_font_size').length && $('#config_page1_title_line_2_font_size').val()) {
                        updatedConfig.page1['title-line-2-font-size'] = parseFloat($('#config_page1_title_line_2_font_size').val());
                    }
                    if ($('#config_page1_title_line_2_margin').length && $('#config_page1_title_line_2_margin').val()) {
                        updatedConfig.page1['title-line-2-margin'] = parseFloat($('#config_page1_title_line_2_margin').val());
                    }
                    if ($('#config_page1_title_line_3').length && $('#config_page1_title_line_3').val()) {
                        updatedConfig.page1['title-line-3'] = $('#config_page1_title_line_3').val();
                    }
                    if ($('#config_page1_title_line_3_margin').length && $('#config_page1_title_line_3_margin').val()) {
                        updatedConfig.page1['title-line-3-margin'] = parseFloat($('#config_page1_title_line_3_margin').val());
                    }
                    if ($('#config_page1_title_line_3_font_size').length && $('#config_page1_title_line_3_font_size').val()) {
                        updatedConfig.page1['title-line-3-font-size'] = parseFloat($('#config_page1_title_line_3_font_size').val());
                    }
                    if ($('#config_page1_logo_image').length && $('#config_page1_logo_image').val()) {
                        updatedConfig.page1['logo-image'] = $('#config_page1_logo_image').val();
                    }
                    if ($('#config_page1_logo_y_percentage').length && $('#config_page1_logo_y_percentage').val()) {
                        updatedConfig.page1['logo-y-percentage'] = parseFloat($('#config_page1_logo_y_percentage').val());
                    }
                    if ($('#config_page1_logo_height').length && $('#config_page1_logo_height').val()) {
                        updatedConfig.page1['logo-height'] = parseFloat($('#config_page1_logo_height').val());
                    }

                    // Page 2
                    if (!updatedConfig.page2) updatedConfig.page2 = {};
                    if ($('#config_page2_text_top').length && $('#config_page2_text_top').val()) {
                        updatedConfig.page2['text-top'] = parseFloat($('#config_page2_text_top').val());
                    }
                    if ($('#config_page2_text_y_space').length && $('#config_page2_text_y_space').val()) {
                        updatedConfig.page2['text-y-space'] = parseFloat($('#config_page2_text_y_space').val());
                    }
                    if ($('#config_page2_text_font_size').length && $('#config_page2_text_font_size').val()) {
                        updatedConfig.page2['text-font-size'] = parseFloat($('#config_page2_text_font_size').val());
                    }
                    if ($('#config_page2_margin_x').length && $('#config_page2_margin_x').val()) {
                        updatedConfig.page2['margin-x'] = parseFloat($('#config_page2_margin_x').val());
                    }
                    if ($('#config_page2_margin_bottom').length && $('#config_page2_margin_bottom').val()) {
                        updatedConfig.page2['margin-bottom'] = parseFloat($('#config_page2_margin_bottom').val());
                    }
                    // Text blocks for Page 2
                    if ($('#config_page2_text_block_1').length) {
                        updatedConfig.page2['text-block-1'] = getQuillContent('config_page2_text_block_1');
                    }
                    if ($('#config_page2_text_block_2').length) {
                        updatedConfig.page2['text-block-2'] = getQuillContent('config_page2_text_block_2');
                    }
                    if ($('#config_page2_text_block_3').length) {
                        updatedConfig.page2['text-block-3'] = getQuillContent('config_page2_text_block_3');
                    }
                    if ($('#config_page2_text_block_4').length) {
                        updatedConfig.page2['text-block-4'] = getQuillContent('config_page2_text_block_4');
                    }

                    // Page 3
                    if (!updatedConfig.page3) updatedConfig.page3 = {};
                    if ($('#config_page3_image').length && $('#config_page3_image').val()) {
                        updatedConfig.page3.image = $('#config_page3_image').val();
                    }
                    if ($('#config_page3_image_y_percentage').length && $('#config_page3_image_y_percentage').val()) {
                        updatedConfig.page3['image-y-percentage'] = parseFloat($('#config_page3_image_y_percentage').val());
                    }
                    if ($('#config_page3_margin').length && $('#config_page3_margin').val()) {
                        updatedConfig.page3.margin = parseFloat($('#config_page3_margin').val());
                    }

                    // Page 4
                    if (!updatedConfig.page4) updatedConfig.page4 = {};
                    if ($('#config_page4_image').length && $('#config_page4_image').val()) {
                        updatedConfig.page4.image = $('#config_page4_image').val();
                    }

                    // Page 5
                    if (!updatedConfig.page5) updatedConfig.page5 = {};
                    if ($('#config_page5_image').length && $('#config_page5_image').val()) {
                        updatedConfig.page5.image = $('#config_page5_image').val();
                    }
                    if ($('#config_page5_header_y').length && $('#config_page5_header_y').val()) {
                        updatedConfig.page5['header-y'] = parseFloat($('#config_page5_header_y').val());
                    }
                    if ($('#config_page5_header_font_size').length && $('#config_page5_header_font_size').val()) {
                        updatedConfig.page5['header-font-size'] = parseFloat($('#config_page5_header_font_size').val());
                    }
                    if ($('#config_page5_footer_y').length && $('#config_page5_footer_y').val()) {
                        updatedConfig.page5['footer-y'] = parseFloat($('#config_page5_footer_y').val());
                    }
                    if ($('#config_page5_footer_font_size').length && $('#config_page5_footer_font_size').val()) {
                        updatedConfig.page5['footer-font-size'] = parseFloat($('#config_page5_footer_font_size').val());
                    }
                    if ($('#config_page5_text_font_size').length && $('#config_page5_text_font_size').val()) {
                        updatedConfig.page5['text-font-size'] = parseFloat($('#config_page5_text_font_size').val());
                    }
                    if ($('#config_page5_text_1_y').length && $('#config_page5_text_1_y').val()) {
                        updatedConfig.page5['text-1-y'] = parseFloat($('#config_page5_text_1_y').val());
                    }
                    if ($('#config_page5_text_2_y').length && $('#config_page5_text_2_y').val()) {
                        updatedConfig.page5['text-2-y'] = parseFloat($('#config_page5_text_2_y').val());
                    }
                    if ($('#config_page5_text_3_y').length && $('#config_page5_text_3_y').val()) {
                        updatedConfig.page5['text-3-y'] = parseFloat($('#config_page5_text_3_y').val());
                    }
                    if ($('#config_page5_margin').length && $('#config_page5_margin').val()) {
                        updatedConfig.page5.margin = parseFloat($('#config_page5_margin').val());
                    }
                    // Text blocks for Page 5
                    if ($('#config_page5_header_text').length) {
                        updatedConfig.page5['header-text'] = getQuillContent('config_page5_header_text');
                    }
                    if ($('#config_page5_footer_text').length) {
                        updatedConfig.page5['footer-text'] = getQuillContent('config_page5_footer_text');
                    }
                    if ($('#config_page5_text_1_content').length) {
                        updatedConfig.page5['text-1-content'] = getQuillContent('config_page5_text_1_content');
                    }
                    if ($('#config_page5_text_2_content').length) {
                        updatedConfig.page5['text-2-content'] = getQuillContent('config_page5_text_2_content');
                    }
                    if ($('#config_page5_text_3_content').length) {
                        updatedConfig.page5['text-3-content'] = getQuillContent('config_page5_text_3_content');
                    }

                    console.log('Configuración actualizada con valores del formulario:', updatedConfig);
                    console.log('Page 1 configuración:', updatedConfig.page1);
                    console.log('Page 2 configuración:', updatedConfig.page2);
                    console.log('Page 3 configuración:', updatedConfig.page3);
                    console.log('Page 4 configuración:', updatedConfig.page4);
                    console.log('Page 5 configuración:', updatedConfig.page5);

                    // Preparar datos para el preview usando el método getPreview
                    const previewData = {
                        numberOfPages: $('#numberOfPages').val() || 5, // Limitar a 5 páginas para preview
                        spreadsheetId: updatedConfig.spreadsheetId,
                        sheetName: updatedConfig.spreadsheetSheetName,
                        imagesURL: updatedConfig.imagesURL,
                        layout: updatedConfig, // Enviar la configuración actualizada con valores del formulario
                        selectedFonts: getSelectedFonts(),
                        _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    };

                    console.log('Datos para preview:', previewData);

                    // Construir URL para preview PDF con parámetros
                    const urlParams = new URLSearchParams();

                    // Agregar parámetros básicos
                    urlParams.append('numberOfPages', previewData.numberOfPages);
                    if (previewData.spreadsheetId) urlParams.append('spreadsheetId', previewData.spreadsheetId);
                    if (previewData.sheetName) urlParams.append('sheetName', previewData.sheetName);
                    if (previewData.imagesURL) urlParams.append('imagesURL', previewData.imagesURL);
                    if (previewData._token) urlParams.append('_token', previewData._token);

                    // Agregar fuentes seleccionadas
                    if (previewData.selectedFonts && previewData.selectedFonts.length > 0) {
                        previewData.selectedFonts.forEach(font => {
                            urlParams.append('selectedFonts[]', font);
                        });
                    }

                    // Agregar configuración como JSON
                    if (previewData.layout) {
                        urlParams.append('layout', JSON.stringify(previewData.layout));
                    }

                    // Construir URL final para PDF preview con parámetros de visualización
                    const basePreviewUrl = '/preview-pdf?' + urlParams.toString();

                    // Agregar parámetros para mostrar PDF a pantalla completa sin navegación lateral
                    // toolbar=0: oculta toolbar superior
                    // navpanes=0: oculta panel de navegación lateral
                    // scrollbar=0: oculta scrollbar (opcional)
                    // view=FitH: ajusta horizontalmente
                    // zoom=page-width: ajusta al ancho de página
                    const previewUrl = basePreviewUrl + '#toolbar=0&navpanes=0&view=FitH&zoom=page-width';
                    console.log('URL de preview PDF:', previewUrl);

                    // Cargar PDF directamente en el iframe
                    const iframe = document.getElementById('previewFrame');

                    // Limpiar listeners previos
                    iframe.onload = null;

                    // Para PDFs, el evento onload es más simple
                    iframe.onload = function() {
                        console.log('PDF cargado completamente en iframe');
                        hidePreviewLoading();
                    };

                    // Timeout de seguridad para PDFs (pueden tardar más en cargar)
                    setTimeout(function() {
                        console.log('Timeout de seguridad PDF: ocultando loading (5 segundos)');
                        hidePreviewLoading();
                    }, 5000);

                    // Timeout adicional para casos extremos
                    setTimeout(function() {
                        console.log('Timeout extremo PDF: forzando ocultación (15 segundos)');
                        hidePreviewLoading();
                    }, 15000);

                    // Cargar PDF directamente en iframe
                    iframe.src = previewUrl;
                })
                .fail(function(xhr, status, error) {
                    console.error('Error cargando configuración JSON:', error);

                    // En caso de error de configuración, mostrar mensaje directamente en iframe
                    const iframe = document.getElementById('previewFrame');
                    const errorUrl = 'data:text/html;charset=utf-8,' + encodeURIComponent(`
                        <html>
                            <body style="font-family: Arial, sans-serif; padding: 20px; text-align: center; color: #666;">
                                <div style="border: 2px dashed #ddd; padding: 40px; border-radius: 10px;">
                                    <i style="font-size: 48px; color: #dc3545;">⚠️</i>
                                    <h3 style="color: #dc3545; margin: 20px 0;">Error de configuración</h3>
                                    <p>No se pudo cargar la configuración del archivo JSON.</p>
                                    <small style="color: #999;">Archivo: ${selectedJsonFile}</small>
                                </div>
                            </body>
                        </html>
                    `);

                    iframe.onload = function() {
                        console.log('Iframe de error de configuración cargado');
                        hidePreviewLoading();
                    };

                    iframe.src = errorUrl;

                    setTimeout(function() {
                        console.log('Timeout de error de configuración: ocultando loading');
                        hidePreviewLoading();
                    }, 1000);
                });
        }

        // Función para refrescar preview manualmente
        function refreshPreview() {
            console.log('Refresh preview manual');
            updatePreview(true);
        }

        // Función para abrir preview en nueva pestaña
        function openPreviewInNewTab() {
            const iframe = document.getElementById('previewFrame');
            if (iframe.src && iframe.src !== 'about:blank') {
                window.open(iframe.src, '_blank');
            } else {
                alert('No hay preview disponible para abrir.');
            }
        }

        // Función para cargar fuentes desde el servidor
        function loadAvailableFonts() {
            console.log('Cargando fuentes disponibles desde el servidor...');

            return $.ajax({
                url: '/generate/fonts',
                type: 'GET',
                success: function(fonts) {
                    console.log('Fuentes cargadas desde servidor:', fonts);
                    availableFonts = fonts;

                    // 1. Primero crear CSS dinámico
                    createDynamicFontCSS(fonts);

                    // 2. Luego cargar fuentes con FontFace API (en paralelo)
                    loadFontsWithFontFaceAPI(fonts);

                    // 3. Esperar un poco y luego registrar en Quill
                    setTimeout(() => {
                        registerQuillFonts(fonts);

                        // 4. Actualizar selector de fuentes
                        updateFontsSelector(fonts);

                        fontsLoaded = true;
                        console.log('Proceso de carga de fuentes completado');
                    }, 200);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar fuentes:', error);
                    console.log('Usando fuentes por defecto debido al error');

                    // Usar fuentes por defecto
                    availableFonts = [
                        { displayName: 'Sans Serif', cssName: 'sans-serif', familyName: 'sans-serif' },
                        { displayName: 'Serif', cssName: 'serif', familyName: 'serif' },
                        { displayName: 'Monospace', cssName: 'monospace', familyName: 'monospace' }
                    ];

                    updateFontsSelector(availableFonts);
                    fontsLoaded = true;
                }
            });
        }

        // Función moderna para cargar fuentes usando FontFace API
        function loadFontsWithFontFaceAPI(fonts) {
            console.log('Cargando fuentes con FontFace API...');

            if (!('FontFace' in window)) {
                console.log('FontFace API no soportada, usando método tradicional');
                return;
            }

            const loadPromises = [];

            fonts.forEach(function(font) {
                try {
                    // Crear FontFace dinámicamente
                    const fontFace = new FontFace(
                        font.familyName,
                        `url('/fonts/${font.filename}')`,
                        {
                            style: 'normal',
                            weight: 'normal',
                            display: 'swap' // Mejor rendimiento
                        }
                    );

                    // Cargar la fuente
                    const loadPromise = fontFace.load().then(function(loadedFont) {
                        // Añadir al document fonts
                        document.fonts.add(loadedFont);
                        console.log(`Fuente cargada: ${font.familyName}`);
                        return loadedFont;
                    }).catch(function(error) {
                        console.warn(`Error cargando fuente ${font.familyName}:`, error);
                    });

                    loadPromises.push(loadPromise);
                } catch (error) {
                    console.warn(`Error creando FontFace para ${font.familyName}:`, error);
                }
            });

            // Esperar a que todas las fuentes se carguen
            Promise.allSettled(loadPromises).then(function(results) {
                const successCount = results.filter(r => r.status === 'fulfilled').length;
                console.log(`FontFace API: ${successCount}/${fonts.length} fuentes cargadas exitosamente`);

                // Forzar re-render de elementos que usan fuentes
                document.fonts.ready.then(function() {
                    console.log('Todas las fuentes están listas');
                    // Trigger re-render si es necesario
                    document.body.style.fontDisplay = 'swap';
                });
            });
        }

        // Función para actualizar el selector de fuentes
        function updateFontsSelector(fonts) {
            const container = $('#config_fonts_container');

            if (!fonts || fonts.length === 0) {
                container.html(`
                    <div class="text-muted text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay fuentes disponibles
                    </div>
                `);
                return;
            }

            let html = '<div class="row">';
            fonts.forEach((font, index) => {
                // Usar displayName del objeto fuente, fallback al nombre simple
                const fontName = typeof font === 'object' ? font.displayName : font;
                const fontValue = typeof font === 'object' ? font.filename : font;
                const fontFamily = typeof font === 'object' ? font.familyName : font;

                html += `
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input font-checkbox" type="checkbox"
                                   value="${fontValue}" id="font_${index}" checked>
                            <label class="form-check-label" for="font_${index}"
                                   style="font-family: '${fontFamily}', sans-serif;">
                                ${fontName}
                            </label>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            container.html(html);

            // Actualizar contador
            updateFontCounter();
        }

        // Función para crear CSS dinámico para las fuentes
        function createDynamicFontCSS(fonts) {
            console.log('Creando CSS dinámico para', fonts.length, 'fuentes');
            let css = '';

            fonts.forEach(function(font) {
                // Crear @font-face para cada fuente
                css += `
                    @font-face {
                        font-family: '${font.familyName}';
                        src: url('/fonts/${font.filename}') format('truetype');
                        font-display: swap;
                        font-weight: normal;
                        font-style: normal;
                    }

                    .ql-font-${font.cssName} {
                        font-family: '${font.familyName}', serif !important;
                    }

                    /* Aplicar fuente al contenido del editor */
                    .ql-editor .ql-font-${font.cssName.toLowerCase()} {
                        font-family: '${font.familyName}', serif !important;
                    }

                    /* Mostrar nombre de la fuente en el selector */
                    .ql-picker.ql-font .ql-picker-item[data-value="${font.cssName}"]::before,
                    .ql-picker.ql-font .ql-picker-label[data-value="${font.cssName}"]::before {
                        content: '${font.displayName}';
                        font-family: '${font.familyName}', serif;
                    }

                    /* Mostrar nombre en el dropdown */
                    .ql-picker.ql-font .ql-picker-item[data-value="${font.cssName}"] {
                        font-family: '${font.familyName}', serif !important;
                    }
                `;
            });

            // Añadir estilo para la opción por defecto
            css += `
                .ql-picker.ql-font .ql-picker-item[data-value=""]::before,
                .ql-picker.ql-font .ql-picker-label[data-value=""]::before {
                    content: 'Font Default';
                    font-family: 'Times New Roman', serif;
                }
            `;

            // Buscar elemento de estilo existente
            let styleElement = document.getElementById('dynamic-fonts');
            if (!styleElement) {
                console.error('Elemento dynamic-fonts no encontrado, creando uno nuevo');
                styleElement = document.createElement('style');
                styleElement.id = 'dynamic-fonts';
                styleElement.type = 'text/css';
                document.head.appendChild(styleElement);
            }

            // Inyectar CSS en el documento
            styleElement.innerHTML = css;

            // Forzar recalculación de estilos
            document.body.offsetHeight; // Trigger reflow

            console.log('CSS de fuentes creado exitosamente');
            console.log('Fuentes CSS creadas:', fonts.map(f => f.cssName));

            // Verificar que los estilos se aplicaron
            setTimeout(function() {
                if (fonts.length > 0) {
                    const testElement = document.createElement('div');
                    testElement.className = `ql-font-${fonts[0].cssName.toLowerCase()}`;
                    document.body.appendChild(testElement);
                    const computedStyle = window.getComputedStyle(testElement);
                    console.log('Test de estilo aplicado:', computedStyle.fontFamily);
                    document.body.removeChild(testElement);
                }
            }, 100);
        }

        // Función para registrar fuentes en Quill
        function registerQuillFonts(fonts) {
            try {
                const Font = Quill.import('formats/font');
                const fontNames = [''].concat(fonts.map(font => font.cssName)); // Añadir opción por defecto
                Font.whitelist = fontNames;
                Quill.register(Font, true);
                console.log('Fuentes registradas en Quill:', fontNames);
                console.log('Fuentes disponibles:', fonts.map(f => `${f.cssName} -> ${f.displayName}`));

                // Refrescar editores existentes si ya están inicializados
                refreshQuillFontOptions(fontNames);
            } catch (error) {
                console.error('Error al registrar fuentes en Quill:', error);
            }
        }

        // Función para refrescar las opciones de fuente en editores existentes
        function refreshQuillFontOptions(fontNames) {
            Object.keys(quillEditors).forEach(editorKey => {
                const editor = quillEditors[editorKey];
                if (editor && editor.getModule) {
                    try {
                        const toolbar = editor.getModule('toolbar');
                        if (toolbar && toolbar.container) {
                            // Buscar el selector de fuentes
                            const fontPicker = toolbar.container.querySelector('.ql-font');
                            if (fontPicker) {
                                console.log(`Refrescando opciones de fuente para editor: ${editorKey}`);

                                // Forzar reconstrucción del picker
                                const picker = fontPicker.__quill_picker;
                                if (picker) {
                                    picker.buildItems();
                                }
                            }
                        }
                    } catch (error) {
                        console.warn(`Error refrescando fuentes para editor ${editorKey}:`, error);
                    }
                }
            });
        }

        // Función para obtener las fuentes seleccionadas
        function getSelectedFonts() {
            const selectedFonts = [];
            $('.font-checkbox:checked').each(function() {
                selectedFonts.push($(this).val());
            });
            return selectedFonts;
        }

        // Función para actualizar el contador de fuentes seleccionadas
        function updateFontCounter() {
            const totalFonts = $('.font-checkbox').length;
            const selectedFonts = $('.font-checkbox:checked').length;

            const counterText = `Seleccionadas: ${selectedFonts} de ${totalFonts}`;

            // Buscar si ya existe el contador, si no, crearlo
            let $counter = $('#fontCounter');
            if ($counter.length === 0) {
                $('#selectAllFonts').parent().append(`
                    <div class="mt-2">
                        <small class="text-muted" id="fontCounter">${counterText}</small>
                    </div>
                `);
            } else {
                $counter.text(counterText);
            }
        }

        // Eventos para seleccionar/deseleccionar todas las fuentes
        $(document).on('click', '#selectAllFonts', function() {
            $('.font-checkbox').prop('checked', true);
            updateFontCounter();
        });

        $(document).on('click', '#deselectAllFonts', function() {
            $('.font-checkbox').prop('checked', false);
            updateFontCounter();
        });

        // Evento para actualizar contador cuando cambia selección
        $(document).on('change', '.font-checkbox', function() {
            updateFontCounter();
        });
    </script>

    <script>
        $(document).ready(function() {
            // Las fuentes se cargarán automáticamente en initializeSystem()
            $('#pdfGeneratorForm').on('submit', function(e) {
                e.preventDefault();

                // Mostrar loading overlay
                $('#loadingOverlay').fadeIn();

                // Cambiar estado del botón
                const $btn = $('#generateBtn');
                const originalBtnText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Generando...');

                // Limpiar alertas anteriores
                $('#alertContainer').empty();

                // Preparar datos del formulario (incluir numberOfPages del formulario)
                const formData = {
                    numberOfPages: $('#numberOfPages').val() || 1
                    , selectedJsonFile: $('#jsonFileSelector').val()
                    , selectedFonts: getSelectedFonts()
                    , _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                };

                // Realizar petición AJAX
                $.ajax({
                    url: '{{ route("pdf.generate") }}'
                    , type: 'POST'
                    , data: formData
                    , xhrFields: {
                        responseType: 'blob' // Importante para manejar archivos binarios
                    }
                    , success: function(data, status, xhr) {
                        // Crear enlace de descarga
                        const blob = new Blob([data], {
                            type: 'application/pdf'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;

                        // Obtener nombre del archivo desde headers o usar uno por defecto
                        const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                        let filename = 'color-book.pdf';
                        if (contentDisposition) {
                            const matches = /filename="([^"]*)"/.exec(contentDisposition);
                            if (matches && matches[1]) {
                                filename = matches[1];
                            }
                        }

                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);

                        // Mostrar mensaje de éxito
                        showAlert('success', '<i class="fas fa-check-circle me-2"></i>PDF generado exitosamente!');
                    }
                    , error: function(xhr) {
                        let errorMessage = 'Error al generar el PDF';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 0) {
                            errorMessage = 'Error de conexión. Verifica tu conexión a internet.';
                        } else if (xhr.status >= 500) {
                            errorMessage = 'Error del servidor. Inténtalo más tarde.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Datos del formulario inválidos. Verifica la información.';
                        }

                        showAlert('danger', '<i class="fas fa-exclamation-triangle me-2"></i>' + errorMessage);
                    }
                    , complete: function() {
                        // Ocultar loading overlay
                        $('#loadingOverlay').fadeOut();

                        // Restaurar botón
                        $btn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('#alertContainer').html(alertHtml);

                // Auto-hide success alerts after 5 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        $('.alert-success').fadeOut();
                    }, 5000);
                }
            }

            // Event listeners para el preview
            $('#refreshPreviewBtn').on('click', function() {
                refreshPreview();
            });

            $('#openPreviewNewTab').on('click', function() {
                openPreviewInNewTab();
            });

            // Botón de debug para forzar ocultar loading
            $('#forceHideLoadingBtn').on('click', function() {
                console.log('Forzando ocultación del loading manualmente');
                hidePreviewLoading();
            });

            // Event listeners para detectar cambios y actualizar preview automáticamente - DESHABILITADO
            // $('#jsonFileSelector, #numberOfPages').on('change', function() {
            //     console.log('Cambio detectado en:', this.id);
            //     updatePreview();
            // });

            // Detectar cambios en checkboxes de fuentes - DESHABILITADO
            // $(document).on('change', '.font-checkbox', function() {
            //     console.log('Cambio en selección de fuentes');
            //     updatePreview();
            // });

            // Detectar cambios en inputs de configuración - DESHABILITADO
            // $(document).on('change input keyup paste', 'input[id^="config_"], select[id^="config_"], textarea[id^="config_"]', function() {
            //     console.log('Cambio en configuración:', this.id);
            //     updatePreview();
            // });

            // Detectar cambios específicos en campos principales del formulario - DESHABILITADO
            // $('#spreadsheetId, #sheetName, #imagesURL').on('input change keyup paste', function() {
            //     console.log('Cambio en campo principal:', this.id);
            //     updatePreview();
            // });

            // Detectar cambios en accordions y controles específicos - DESHABILITADO
            // $(document).on('change input keyup paste', 'input[type="number"], input[type="text"], input[type="url"], select, textarea', function() {
            //     // Solo aplicar a elementos dentro de configuración de plantilla
            //     if ($(this).closest('.accordion').length > 0 || $(this).attr('id')?.startsWith('config_')) {
            //         console.log('Cambio en control de plantilla:', this.id || this.name);
            //         updatePreview();
            //     }
            // });

            // Detectar cambios en editores Quill usando eventos nativos de Quill - DESHABILITADO
            // NOTA: Reemplaza el uso de DOMSubtreeModified (deprecated) por eventos nativos de Quill
            // Los eventos de mutación DOM están deprecados y causan warnings en navegadores modernos
            // Se configurará después de inicializar los editores
            window.setupQuillChangeListeners = function() {
                console.log('Listeners de Quill para preview automático DESHABILITADOS');
                // Object.keys(quillEditors).forEach(function(editorKey) {
                //     const editor = quillEditors[editorKey];
                //     if (editor && !editor._previewListenerAdded) {
                //         editor.on('text-change', function(delta, oldDelta, source) {
                //             if (source === 'user') {
                //                 console.log('Cambio en editor Quill detectado para preview:', editorKey);
                //                 updatePreview();
                //             }
                //         });

                //         // También escuchar cambios de selección/formato
                //         editor.on('selection-change', function(range, oldRange, source) {
                //             if (source === 'user' && range && range.length > 0) {
                //                 console.log('Cambio de selección en editor Quill:', editorKey);
                //                 // Pequeño delay para capturar cambios de formato
                //                 setTimeout(() => updatePreview(), 100);
                //             }
                //         });

                //         editor._previewListenerAdded = true;
                //         console.log('Listeners configurados para editor:', editorKey);
                //     }
                // });
                console.log('Listeners de Quill NO configurados (preview manual solamente)');
            };

            // Auto-load configuration se manejará después de inicializar editores

            // Función para extraer fuentes de la configuración JSON (similar al PHP)
            function extractFontsFromConfig(config) {
                const fonts = [];

                function extractFromValue(value) {
                    if (typeof value === 'string') {
                        // Buscar patrones de clases ql-font- en el texto
                        const matches = value.match(/ql-font-([a-zA-Z0-9_-]+)/g);
                        if (matches) {
                            matches.forEach(match => {
                                const fontName = match.replace('ql-font-', '');
                                if (fontName && !fonts.includes(fontName)) {
                                    fonts.push(fontName);
                                }
                            });
                        }
                    } else if (Array.isArray(value)) {
                        value.forEach(item => extractFromValue(item));
                    } else if (typeof value === 'object' && value !== null) {
                        Object.values(value).forEach(item => extractFromValue(item));
                    }
                }

                extractFromValue(config);
                return fonts.sort();
            }

            // Función para cargar configuración desde archivo seleccionado
            function loadSelectedConfig() {
                console.log('loadSelectedConfig iniciado');

                // Asegurar que los editores estén inicializados
                if (!editorsInitialized) {
                    console.log('Editores no inicializados, esperando...');
                    setTimeout(loadSelectedConfig, 500);
                    return;
                }

                const selectedJsonFile = $('#jsonFileSelector').val() || '/json/chinese.json';
                console.log('Cargando configuración desde:', selectedJsonFile);

                $.get(selectedJsonFile)
                    .done(function(config) {
                        console.log('Configuración cargada:', config);

                        // General
                        $('#config_width').val(config.width);
                        $('#config_height').val(config.height);
                        $('#config_margin_in').val(config['margin-in']);
                        $('#config_margin_out').val(config['margin-out']);
                        $('#config_spreadsheet_id').val(config.spreadsheetId);
                        $('#config_spreadsheet_sheet_name').val(config.spreadsheetSheetName);
                        $('#config_images_url').val(config.imagesURL);

                        // Verso
                        if (config.verso) {
                            $('#config_verso_margin').val(config.verso.margin);
                            $('#config_verso_border_margin').val(config.verso['border-margin']);
                            $('#config_verso_image_margin').val(config.verso['image-margin']);
                            $('#config_verso_text_1_top').val(config.verso['text-1-top']);
                            $('#config_verso_text_2_top').val(config.verso['text-2-top']);
                            $('#config_verso_primary_font_size').val(config.verso['primary-font-size']);
                            $('#config_verso_secondary_font_size').val(config.verso['secondary-font-size']);
                        }

                        // Recto
                        if (config.recto) {
                            $('#config_recto_margin').val(config.recto.margin);
                            $('#config_recto_image_margin').val(config.recto['image-margin']);
                            $('#config_recto_font_size').val(config.recto['font-size']);
                            $('#config_recto_text_top').val(config.recto['text-top']);
                        }

                        // Page 1
                        if (config.page1) {
                            $('#config_page1_title_line_1').val(config.page1['title-line-1']);
                            $('#config_page1_title_line_1_y_percentage').val(config.page1['title-line-1-y-percentage']);
                            $('#config_page1_title_line_1_font_size').val(config.page1['title-line-1-font-size']);
                            $('#config_page1_title_line_2').val(config.page1['title-line-2']);
                            $('#config_page1_title_line_2_font_size').val(config.page1['title-line-2-font-size']);
                            $('#config_page1_title_line_2_margin').val(config.page1['title-line-2-margin']);
                            $('#config_page1_title_line_3').val(config.page1['title-line-3']);
                            $('#config_page1_title_line_3_margin').val(config.page1['title-line-3-margin']);
                            $('#config_page1_title_line_3_font_size').val(config.page1['title-line-3-font-size']);
                            $('#config_page1_logo_image').val(config.page1['logo-image']);
                            $('#config_page1_logo_y_percentage').val(config.page1['logo-y-percentage']);
                            $('#config_page1_logo_height').val(config.page1['logo-height']);
                        }

                        // Page 2
                        if (config.page2) {
                            $('#config_page2_text_top').val(config.page2['text-top']);
                            $('#config_page2_text_y_space').val(config.page2['text-y-space']);
                            $('#config_page2_text_font_size').val(config.page2['text-font-size']);
                            $('#config_page2_margin_x').val(config.page2['margin-x']);
                            $('#config_page2_margin_bottom').val(config.page2['margin-bottom']);

                            // Cargar textos en editores Quill con delay para asegurar que están listos
                            setTimeout(function() {
                                console.log('Cargando contenido de página 2...');
                                if (config.page2['text-block-1']) {
                                    setQuillContent('config_page2_text_block_1', config.page2['text-block-1']);
                                }
                                if (config.page2['text-block-2']) {
                                    setQuillContent('config_page2_text_block_2', config.page2['text-block-2']);
                                }
                                if (config.page2['text-block-3']) {
                                    setQuillContent('config_page2_text_block_3', config.page2['text-block-3']);
                                }
                                if (config.page2['text-block-4']) {
                                    setQuillContent('config_page2_text_block_4', config.page2['text-block-4']);
                                }
                            }, 200);
                        }

                        // Page 3
                        if (config.page3) {
                            $('#config_page3_image').val(config.page3.image);
                            $('#config_page3_image_y_percentage').val(config.page3['image-y-percentage']);
                            $('#config_page3_margin').val(config.page3.margin);
                        }

                        // Page 4
                        if (config.page4) {
                            $('#config_page4_image').val(config.page4.image);
                        }

                        // Page 5
                        if (config.page5) {
                            $('#config_page5_image').val(config.page5.image);
                            $('#config_page5_header_y').val(config.page5['header-y']);
                            $('#config_page5_header_font_size').val(config.page5['header-font-size']);
                            $('#config_page5_footer_y').val(config.page5['footer-y']);
                            $('#config_page5_footer_font_size').val(config.page5['footer-font-size']);
                            $('#config_page5_text_font_size').val(config.page5['text-font-size']);
                            $('#config_page5_text_1_y').val(config.page5['text-1-y']);
                            $('#config_page5_text_2_y').val(config.page5['text-2-y']);
                            $('#config_page5_text_3_y').val(config.page5['text-3-y']);
                            $('#config_page5_margin').val(config.page5.margin);

                            // Cargar textos en editores Quill con delay
                            setTimeout(function() {
                                console.log('Cargando contenido de página 5...');
                                if (config.page5['header-text']) {
                                    setQuillContent('config_page5_header_text', config.page5['header-text']);
                                }
                                if (config.page5['footer-text']) {
                                    setQuillContent('config_page5_footer_text', config.page5['footer-text']);
                                }
                                if (config.page5['text-1-content']) {
                                    setQuillContent('config_page5_text_1_content', config.page5['text-1-content']);
                                }
                                if (config.page5['text-2-content']) {
                                    setQuillContent('config_page5_text_2_content', config.page5['text-2-content']);
                                }
                                if (config.page5['text-3-content']) {
                                    setQuillContent('config_page5_text_3_content', config.page5['text-3-content']);
                                }
                            }, 300);
                        }

                        // Cargar fuentes disponibles y aplicar selección desde config
                        loadAvailableFonts().then(function() {
                            // Aplicar selección de fuentes desde config
                            if (config.fonts && Array.isArray(config.fonts)) {
                                $('.font-checkbox').prop('checked', false); // Deseleccionar todas
                                config.fonts.forEach(function(fontName) {
                                    $('.font-checkbox[value="' + fontName + '"]').prop('checked', true);
                                });
                                updateFontCounter();
                            }
                        });

                        showAlert('success', '<i class="fas fa-check-circle me-2"></i>Configuración cargada correctamente');

                        // Reconfigurar listeners de cambio después de cargar configuración
                        setTimeout(function() {
                            if (typeof window.setupQuillChangeListeners === 'function') {
                                window.setupQuillChangeListeners();
                                console.log('Listeners de cambio reconfigurados después de cargar configuración');
                            }
                        }, 500);

                        // Actualizar preview después de cargar configuración
                        setTimeout(function() {
                            updatePreview(true);
                        }, 1000);
                    })
                    .fail(function() {
                        showAlert('danger', '<i class="fas fa-exclamation-triangle me-2"></i>Error al cargar la configuración');
                    });
            }

            // Evento para el botón de cargar configuración
            $('#loadConfigBtn').click(function() {
                loadSelectedConfig();
            });

            // Evento para cuando cambia el selector de archivo JSON
            $('#jsonFileSelector').change(function() {
                loadSelectedConfig();
            });

            // Save configuration
            $('#saveConfigBtn').click(function() {
                if (!confirm('¿Estás seguro de que quieres guardar la configuración actual? Esto sobrescribirá el archivo JSON.')) {
                    return;
                }

                // Recopilar todos los datos del accordion
                const configData = {
                    // Archivo seleccionado
                    selected_json_file: $('#jsonFileSelector').val(),

                    // General
                    width: $('#config_width').val()
                    , height: $('#config_height').val()
                    , margin_in: $('#config_margin_in').val()
                    , margin_out: $('#config_margin_out').val()
                    , spreadsheet_id: $('#config_spreadsheet_id').val()
                    , spreadsheet_sheet_name: $('#config_spreadsheet_sheet_name').val()
                    , images_url: $('#config_images_url').val(),

                    // Verso
                    verso_margin: $('#config_verso_margin').val()
                    , verso_border_margin: $('#config_verso_border_margin').val()
                    , verso_image_margin: $('#config_verso_image_margin').val()
                    , verso_text_1_top: $('#config_verso_text_1_top').val()
                    , verso_text_2_top: $('#config_verso_text_2_top').val()
                    , verso_primary_font_size: $('#config_verso_primary_font_size').val()
                    , verso_secondary_font_size: $('#config_verso_secondary_font_size').val(),

                    // Recto
                    recto_margin: $('#config_recto_margin').val()
                    , recto_image_margin: $('#config_recto_image_margin').val()
                    , recto_font_size: $('#config_recto_font_size').val()
                    , recto_text_top: $('#config_recto_text_top').val(),

                    // Page 1
                    page1_title_line_1: $('#config_page1_title_line_1').val()
                    , page1_title_line_1_y_percentage: $('#config_page1_title_line_1_y_percentage').val()
                    , page1_title_line_1_font_size: $('#config_page1_title_line_1_font_size').val()
                    , page1_title_line_2: $('#config_page1_title_line_2').val()
                    , page1_title_line_2_font_size: $('#config_page1_title_line_2_font_size').val()
                    , page1_title_line_2_margin: $('#config_page1_title_line_2_margin').val()
                    , page1_title_line_3: $('#config_page1_title_line_3').val()
                    , page1_title_line_3_margin: $('#config_page1_title_line_3_margin').val()
                    , page1_title_line_3_font_size: $('#config_page1_title_line_3_font_size').val()
                    , page1_logo_image: $('#config_page1_logo_image').val()
                    , page1_logo_y_percentage: $('#config_page1_logo_y_percentage').val()
                    , page1_logo_height: $('#config_page1_logo_height').val(),

                    // Page 2
                    page2_text_top: $('#config_page2_text_top').val()
                    , page2_text_y_space: $('#config_page2_text_y_space').val()
                    , page2_text_font_size: $('#config_page2_text_font_size').val()
                    , page2_margin_x: $('#config_page2_margin_x').val()
                    , page2_margin_bottom: $('#config_page2_margin_bottom').val()
                    , page2_text_block_1: getQuillContent('config_page2_text_block_1')
                    , page2_text_block_2: getQuillContent('config_page2_text_block_2')
                    , page2_text_block_3: getQuillContent('config_page2_text_block_3')
                    , page2_text_block_4: getQuillContent('config_page2_text_block_4'),

                    // Page 3
                    page3_image: $('#config_page3_image').val()
                    , page3_image_y_percentage: $('#config_page3_image_y_percentage').val()
                    , page3_margin: $('#config_page3_margin').val(),

                    // Page 4
                    page4_image: $('#config_page4_image').val(),

                    // Page 5
                    page5_image: $('#config_page5_image').val()
                    , page5_header_y: $('#config_page5_header_y').val()
                    , page5_header_font_size: $('#config_page5_header_font_size').val()
                    , page5_header_text: getQuillContent('config_page5_header_text')
                    , page5_footer_y: $('#config_page5_footer_y').val()
                    , page5_footer_font_size: $('#config_page5_footer_font_size').val()
                    , page5_footer_text: getQuillContent('config_page5_footer_text')
                    , page5_text_font_size: $('#config_page5_text_font_size').val()
                    , page5_text_1_y: $('#config_page5_text_1_y').val()
                    , page5_text_1_content: getQuillContent('config_page5_text_1_content')
                    , page5_text_2_y: $('#config_page5_text_2_y').val()
                    , page5_text_2_content: getQuillContent('config_page5_text_2_content')
                    , page5_text_3_y: $('#config_page5_text_3_y').val()
                    , page5_text_3_content: getQuillContent('config_page5_text_3_content')
                    , page5_margin: $('#config_page5_margin').val(),

                    // Fuentes seleccionadas
                    selected_fonts: getSelectedFonts()
                };

                // Mostrar indicador de carga
                const originalText = $('#saveConfigBtn').html();
                $('#saveConfigBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...').prop('disabled', true);

                // Enviar datos al servidor
                $.ajax({
                    url: '/save-config'
                    , method: 'POST'
                    , data: configData
                    , headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    , success: function(response) {
                        if (response.success) {
                            showAlert('success', '<i class="fas fa-check-circle me-2"></i>Configuración guardada exitosamente');
                        } else {
                            showAlert('danger', '<i class="fas fa-exclamation-triangle me-2"></i>Error: ' + response.message);
                        }
                    }
                    , error: function(xhr) {
                        let errorMessage = 'Error al guardar la configuración';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert('danger', '<i class="fas fa-exclamation-triangle me-2"></i>' + errorMessage);
                    }
                    , complete: function() {
                        // Restaurar botón
                        $('#saveConfigBtn').html(originalText).prop('disabled', false);
                    }
                });
            });

            // Reset configuration to defaults
            $('#resetConfigBtn').click(function() {
                if (confirm('¿Estás seguro de que quieres resetear toda la configuración a los valores por defecto?')) {
                    $('#loadConfigBtn').click(); // Reload from JSON
                }
            });

            // Función para cargar CSS de fuentes dinámico
            function loadDynamicFontCSS() {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.type = 'text/css';
                link.href = '/fonts.css';
                document.head.appendChild(link);
                console.log('CSS de fuentes dinámico cargado');
            }

            // Inicializar editores Quill.js
            function initializeQuillEditors() {
                console.log('Inicializando editores Quill...');

                // Verificar que Quill esté disponible
                if (typeof Quill === 'undefined') {
                    console.error('Quill.js no está cargado. Reintentando...');
                    setTimeout(initializeQuillEditors, 500);
                    return;
                }

                // Verificar que las fuentes estén cargadas
                if (!fontsLoaded) {
                    console.log('Esperando a que se carguen las fuentes...');
                    setTimeout(initializeQuillEditors, 500);
                    return;
                }

                console.log('Quill.js detectado correctamente');
                const editorConfigs = [
                    // Página 2
                    {
                        id: 'config_page2_text_block_1_editor'
                        , hiddenId: 'config_page2_text_block_1'
                    }
                    , {
                        id: 'config_page2_text_block_2_editor'
                        , hiddenId: 'config_page2_text_block_2'
                    }
                    , {
                        id: 'config_page2_text_block_3_editor'
                        , hiddenId: 'config_page2_text_block_3'
                    }
                    , {
                        id: 'config_page2_text_block_4_editor'
                        , hiddenId: 'config_page2_text_block_4'
                    },
                    // Página 5
                    {
                        id: 'config_page5_header_text_editor'
                        , hiddenId: 'config_page5_header_text'
                    }
                    , {
                        id: 'config_page5_footer_text_editor'
                        , hiddenId: 'config_page5_footer_text'
                    }
                    , {
                        id: 'config_page5_text_1_content_editor'
                        , hiddenId: 'config_page5_text_1_content'
                    }
                    , {
                        id: 'config_page5_text_2_content_editor'
                        , hiddenId: 'config_page5_text_2_content'
                    }
                    , {
                        id: 'config_page5_text_3_content_editor'
                        , hiddenId: 'config_page5_text_3_content'
                    }
                ];

                editorConfigs.forEach(config => {
                    const element = document.getElementById(config.id);
                    if (element) {
                        console.log('Inicializando editor:', config.id);

                        const quill = new Quill('#' + config.id, {
                            theme: 'snow'
                            , modules: {
                                toolbar: [
                                    [{
                                        'font': [''].concat(availableFonts.map(f => f.cssName))
                                    }]
                                    , [{
                                        'size': ['small', false, 'large', 'huge']
                                    }]
                                    , ['bold', 'italic', 'underline']
                                    , [{
                                        'color': []
                                    }, {
                                        'background': []
                                    }]
                                    , ['link']
                                    , [{
                                        'list': 'ordered'
                                    }, {
                                        'list': 'bullet'
                                    }]
                                    , [{
                                        'align': []
                                    }]
                                    , ['clean']
                                ]
                            }
                        });

                        // Guardar referencia del editor
                        quillEditors[config.hiddenId] = quill;
                        console.log('Editor guardado para:', config.hiddenId);

                        // Sincronizar con input hidden cuando cambie el contenido
                        quill.on('text-change', function(delta, oldDelta, source) {
                            const content = quill.root.innerHTML;
                            const sourceElement = document.getElementById(config.hiddenId + '_source');

                            // Actualizar input hidden
                            document.getElementById(config.hiddenId).value = content;

                            // Si el textarea de código está visible, actualizarlo también
                            if (sourceElement && sourceElement.style.display !== 'none') {
                                sourceElement.value = content;
                            }

                            // Generar preview automáticamente cuando el usuario hace cambios - DESHABILITADO
                            if (source === 'user') {
                                console.log('Cambio en editor Quill (usuario) - preview manual solamente:', config.hiddenId);
                                // updatePreview();  // Deshabilitado
                            }
                        });

                        // Añadir event listener para el textarea de código
                        const sourceElement = document.getElementById(config.hiddenId + '_source');
                        if (sourceElement) {
                            sourceElement.addEventListener('input', function() {
                                // Actualizar input hidden cuando cambie el código
                                document.getElementById(config.hiddenId).value = sourceElement.value;

                                // Generar preview cuando se edita código fuente - DESHABILITADO
                                console.log('Cambio en código fuente - preview manual solamente:', config.hiddenId);
                                // updatePreview();  // Deshabilitado
                            });
                        }
                    } else {
                        console.warn('Elemento no encontrado:', config.id);
                    }
                });

                editorsInitialized = true;
                console.log('Editores inicializados. Total:', Object.keys(quillEditors).length);

                // Configurar listeners para detectar cambios en editores Quill
                if (typeof window.setupQuillChangeListeners === 'function') {
                    window.setupQuillChangeListeners();
                    console.log('Listeners de cambio de Quill configurados');
                }
            }

            // Función para establecer contenido en editor Quill
            function setQuillContent(hiddenId, content) {
                console.log('setQuillContent llamado para:', hiddenId, 'con contenido:', content);

                const editor = quillEditors[hiddenId];
                if (editor && content) {
                    try {
                        // Limpiar el editor primero
                        editor.setText('');

                        // Usar clipboard para insertar HTML de forma segura
                        editor.clipboard.dangerouslyPasteHTML(0, content);

                        // Actualizar input hidden
                        document.getElementById(hiddenId).value = content;

                        // Actualizar textarea de código si existe
                        const sourceElement = document.getElementById(hiddenId + '_source');
                        if (sourceElement) {
                            sourceElement.value = content;
                        }

                        console.log('Contenido establecido exitosamente para:', hiddenId);
                    } catch (error) {
                        console.error('Error al establecer contenido en', hiddenId, ':', error);
                        // Fallback: usar innerHTML directamente
                        editor.root.innerHTML = content || '';
                        document.getElementById(hiddenId).value = content || '';
                    }
                } else {
                    console.warn('Editor no encontrado o contenido vacío para:', hiddenId);
                }
            }

            // Función para alternar entre vista visual y código HTML
            function toggleSourceView(hiddenId) {
                const editorElement = document.getElementById(hiddenId + '_editor');
                const sourceElement = document.getElementById(hiddenId + '_source');
                const buttonElement = event.target.closest('button');
                const editor = quillEditors[hiddenId];

                if (!editor || !editorElement || !sourceElement || !buttonElement) {
                    console.error('Elementos no encontrados para:', hiddenId);
                    return;
                }

                // Comprobar si está en modo código
                const isShowingSource = sourceElement.style.display !== 'none';

                if (isShowingSource) {
                    // Cambiar de código a visual
                    const htmlContent = sourceElement.value;
                    editor.clipboard.dangerouslyPasteHTML(htmlContent);

                    // Mostrar editor, ocultar textarea
                    editorElement.style.display = 'block';
                    sourceElement.style.display = 'none';

                    // Cambiar texto del botón
                    buttonElement.innerHTML = '<i class="fas fa-code"></i> Ver código';
                    buttonElement.classList.remove('btn-warning');
                    buttonElement.classList.add('btn-outline-secondary');
                } else {
                    // Cambiar de visual a código
                    const htmlContent = editor.root.innerHTML;
                    sourceElement.value = htmlContent;

                    // Ocultar editor, mostrar textarea
                    editorElement.style.display = 'none';
                    sourceElement.style.display = 'block';

                    // Cambiar texto del botón
                    buttonElement.innerHTML = '<i class="fas fa-eye"></i> Ver visual';
                    buttonElement.classList.remove('btn-outline-secondary');
                    buttonElement.classList.add('btn-warning');
                }

                // Sincronizar con input hidden
                const currentContent = isShowingSource ? sourceElement.value : editor.root.innerHTML;
                document.getElementById(hiddenId).value = currentContent;
            }

            // Hacer la función global para que pueda ser llamada desde el HTML
            window.toggleSourceView = toggleSourceView;

            // Función para inicializar todo el sistema
            function initializeSystem() {
                console.log('Iniciando sistema...');

                // Verificar dependencias
                if (typeof Quill === 'undefined') {
                    console.error('Quill.js no cargado, reintentando en 500ms...');
                    setTimeout(initializeSystem, 500);
                    return;
                }

                if (typeof $ === 'undefined') {
                    console.error('jQuery no cargado, reintentando en 500ms...');
                    setTimeout(initializeSystem, 500);
                    return;
                }

                console.log('Dependencias verificadas, cargando fuentes...');

                // Cargar fuentes primero
                loadAvailableFonts().then(function() {
                    console.log('Fuentes cargadas, inicializando editores...');

                    // Inicializar editores después de cargar fuentes
                    setTimeout(function() {
                        initializeQuillEditors();

                        // Cargar configuración después de que los editores estén listos
                        setTimeout(function() {
                            if (editorsInitialized) {
                                console.log('Cargando configuración inicial...');
                                $('#loadConfigBtn').click();

                                // Configurar listeners adicionales después de que todo esté inicializado - DESHABILITADO
                                setTimeout(function() {
                                    console.log('Listeners adicionales para preview automático DESHABILITADOS');

                                    // Listener global para cambios en el DOM que no hayamos capturado - DESHABILITADO
                                    // $(document).on('input change keyup paste blur focusout', function(e) {
                                    //     const $target = $(e.target);

                                    //     // Solo aplicar a elementos relacionados con la configuración
                                    //     if ($target.is('input, select, textarea') &&
                                    //         ($target.attr('id')?.includes('config') ||
                                    //          $target.closest('.accordion').length > 0 ||
                                    //          $target.hasClass('font-checkbox') ||
                                    //          $target.attr('id') === 'numberOfPages' ||
                                    //          $target.attr('id') === 'jsonFileSelector')) {

                                    //         console.log('Cambio global detectado en:', e.target.id || e.target.name || 'elemento sin id');
                                    //         updatePreview();
                                    //     }
                                    // });

                                    // Preview inicial deshabilitado - solo manual
                                    console.log('Preview inicial DESHABILITADO - usar botón refresh para generar preview');
                                    // updatePreview(true);  // Deshabilitado
                                }, 1000);
                            } else {
                                console.log('Editores no inicializados, reintentando carga de configuración...');
                                setTimeout(() => $('#loadConfigBtn').click(), 500);
                            }
                        }, 500);
                    }, 200);
                });
            }

            // Inicializar sistema después de que el DOM esté listo
            setTimeout(initializeSystem, 200);
        });

    </script>

</body>
</html>
