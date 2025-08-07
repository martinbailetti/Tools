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
    <style>
        /* Estilos para modo pantalla completa de Quill */
        .quill-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important;
            background: white !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .quill-fullscreen .ql-toolbar {
            flex-shrink: 0 !important;
            border-bottom: 1px solid #ccc !important;
        }

        .quill-fullscreen .ql-container {
            flex: 1 !important;
            border: none !important;
        }

        .quill-fullscreen .ql-editor {
            height: 100% !important;
            min-height: auto !important;
            padding: 20px !important;
            font-size: 16px !important;
        }

        /* Estilo para el botón de pantalla completa */
        .ql-fullscreen {
            padding: 4px 8px !important;
            font-size: 14px !important;
            font-weight: bold !important;
        }
    </style>

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
            html,
            body {
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
            .container-fluid>.row {
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
                height: calc(100% - 60px);
                /* Restar altura del header */
                overflow: hidden;
            }

            /* Iframe del preview debe ajustarse a la altura disponible */
            #previewFrame {
                height: 100% !important;
                min-height: unset !important;
            }
        }

        /* Estilos para labels con tooltip */
        .form-label-tooltip {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: inline-block;
            cursor: help;
        }

        /* Mejorar el estilo del tooltip de Bootstrap */
        .tooltip {
            --bs-tooltip-max-width: 300px;
        }

        .tooltip-inner {
            text-align: left;
            word-wrap: break-word;
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
                                <label for="jsonFileSelector" class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Archivo de Configuración JSON">
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
                                <label for="numberOfPages" class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Número de Páginas (opcional)">
                                    <i class="fas fa-sort-numeric-up me-1"></i>
                                    Número de Páginas (opcional)
                                </label>
                                <div class="input-icon-container">
                                    <input type="number" class="form-control" name="numberOfPages" id="numberOfPages" value="1" min="0" placeholder="0">
                                    <i class="fas fa-hashtag form-icon"></i>
                                </div>
                                <div class="form-text">Si es 0 o se deja vacío, se tomarán todas las páginas disponibles</div>
                            </div>

                            <div class="mb-3">
                                <label for="languageSelector" class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Idioma de los textos">
                                    <i class="fas fa-language me-1"></i>
                                    Idioma
                                </label>
                                <div class="input-icon-container">
                                    <select class="form-select" id="languageSelector" name="languageSelector">
                                        <option value="ES" selected>Español (ES)</option>
                                        <option value="EN">Inglés (EN)</option>
                                    </select>
                                    <i class="fas fa-globe form-icon"></i>
                                </div>
                                <div class="form-text">Selecciona el idioma para los textos del spreadsheet</div>
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
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Ancho (width)">
                                                    <i class="fas fa-arrows-alt-h me-1"></i>
                                                    Ancho (width)
                                                </label>
                                                <input type="number" class="form-control" id="config_width" step="0.01" value="21.59">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Alto (height)">
                                                    <i class="fas fa-arrows-alt-v me-1"></i>
                                                    Alto (height)
                                                </label>
                                                <input type="number" class="form-control" id="config_height" step="0.01" value="27.94">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Margen Interno (margin-in)">
                                                    <i class="fas fa-indent me-1"></i>
                                                    Margen Interno (margin-in)
                                                </label>
                                                <input type="number" class="form-control" id="config_margin_in" step="0.01" value="0.95">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Margen Externo (margin-out)">
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
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="ID de Google Spreadsheet">
                                                    <i class="fas fa-table me-1"></i>
                                                    ID de Google Spreadsheet
                                                </label>
                                                <input type="text" class="form-control" id="config_spreadsheet_id" value="1nYdFCcD5hLjPmz1xmddfNupjItjOI4riFzgpKX9Bq7k">
                                                <div class="form-text">ID de la hoja de cálculo de Google Drive</div>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Nombre de la Hoja">
                                                    <i class="fas fa-file-alt me-1"></i>
                                                    Nombre de la Hoja
                                                </label>
                                                <input type="text" class="form-control" id="config_spreadsheet_sheet_name" value="Chino">
                                                <div class="form-text">Nombre de la pestaña en la hoja de cálculo</div>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="URL Base de Imágenes">
                                                    <i class="fas fa-images me-1"></i>
                                                    URL Base de Imágenes
                                                </label>
                                                <input type="url" class="form-control" id="config_images_url" value="https://printables.happycapibara.com/color-books/chinese/">
                                                <div class="form-text">URL donde se encuentran las imágenes (debe terminar en /)</div>
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
                                                    Texto 1 Y
                                                </label>
                                                <input type="number" class="form-control" id="config_verso_text_1_top" step="0.01">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">

                                                    <i class="fa-solid fa-ruler-vertical me-1"></i>
                                                    Texto 2 Y
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
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-font me-1"></i>
                                                    Fuente Texto 1
                                                </label>
                                                <select class="form-control font-selector" id="config_verso_text_1_font_family">
                                                    <option value="">Seleccionar fuente...</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-font me-1"></i>
                                                    Fuente Texto 2
                                                </label>
                                                <select class="form-control font-selector" id="config_verso_text_2_font_family">
                                                    <option value="">Seleccionar fuente...</option>
                                                </select>
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
                                                    <i class="fa-solid fa-ruler-vertical me-1"></i>
                                                    Texto Y
                                                </label>
                                                <input type="number" class="form-control" id="config_recto_text_top" step="0.01" value="0.53">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">
                                                    <i class="fas fa-font me-1"></i>
                                                    Fuente Texto
                                                </label>
                                                <select class="form-control font-selector" id="config_recto_text_font_family">
                                                    <option value="">Seleccionar fuente...</option>
                                                </select>
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
                                            <div class="col-md-3">
                                                <label class="form-label">Text Y</label>
                                                <input type="number" class="form-control" id="config_page1_text_top" step="0.01" value="0.4">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Margen Horizontal del Texto (text-margin-x)">
                                                    Margen X del Texto:
                                                </label>
                                                <input type="number" class="form-control" id="config_page1_text_margin_x" step="0.01" value="0">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="URL de Imagen de Fondo (background-url)">
                                                    URL de Imagen de Fondo:
                                                </label>
                                                <input type="url" class="form-control" id="config_page1_background_url" value="">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL del Logo
                                                </label>
                                                <input type="url" class="form-control" id="config_page1_image_url" value="https://printables.happycapibara.com/logo.png">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Logo Y</label>
                                                <input type="number" class="form-control" id="config_page1_image_top" step="0.01" value="0.9">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Altura del Logo</label>
                                                <input type="number" class="form-control" id="config_page1_image_height" step="0.1" value="3">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Ancho de la Imagen (image-width)">
                                                    Ancho de Imagen:
                                                </label>
                                                <input type="number" class="form-control" id="config_page1_image_width" step="0.1" value="0">
                                            </div>
                                        </div>

                                        <!-- Editores de Texto con Quill.js -->
                                        <div class="mt-4">
                                            <h5 class="text-primary">
                                                <i class="fas fa-edit me-2"></i>
                                                Títulos de Página
                                            </h5>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold">Título Línea 1</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page1_text')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page1_text_editor" style="height: 120px;"></div>
                                                <textarea id="config_page1_text_source" class="form-control" style="height: 120px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page1_text" />
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
                                                    <i class="fas fa-ruler-vertical me-1"></i>
                                                    Texto Y
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_text_top" step="0.01" value="21">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Margen Horizontal del Texto (text-margin-x)">
                                                    Margen X del Texto:
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_text_margin_x" step="0.01" value="0.5">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="URL de Imagen de Fondo (background-url)">
                                                    URL de Imagen de Fondo:
                                                </label>
                                                <input type="url" class="form-control" id="config_page2_background_url" value="">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL de Imagen
                                                </label>
                                                <input type="url" class="form-control" id="config_page2_image_url" value="">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Imagen Y</label>
                                                <input type="number" class="form-control" id="config_page2_image_top" step="0.01" value="0">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Altura de Imagen</label>
                                                <input type="number" class="form-control" id="config_page2_image_height" step="0.1" value="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Ancho de la Imagen (image-width)">
                                                    Ancho de Imagen:
                                                </label>
                                                <input type="number" class="form-control" id="config_page2_image_width" step="0.1" value="0">
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
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page2_text')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page2_text_editor" style="height: 120px;"></div>
                                                <textarea id="config_page2_text_source" class="form-control" style="height: 120px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page2_text" />
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
                                            <div class="col-md-3">
                                                <label class="form-label">Text Y</label>
                                                <input type="number" class="form-control" id="config_page3_text_top" step="0.01" value="0.4">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Margen Horizontal del Texto (text-margin-x)">
                                                    Margen X del Texto:
                                                </label>
                                                <input type="number" class="form-control" id="config_page3_text_margin_x" step="0.01" value="0">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="URL de Imagen de Fondo (background-url)">
                                                    URL de Imagen de Fondo:
                                                </label>
                                                <input type="url" class="form-control" id="config_page3_background_url" value="">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL de Imagen
                                                </label>
                                                <input type="url" class="form-control" id="config_page3_image_url" value="">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Imagen Y</label>
                                                <input type="number" class="form-control" id="config_page3_image_top" step="0.01" value="0">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Altura de Imagen</label>
                                                <input type="number" class="form-control" id="config_page3_image_height" step="0.1" value="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Ancho de la Imagen (image-width)">
                                                    Ancho de Imagen:
                                                </label>
                                                <input type="number" class="form-control" id="config_page3_image_width" step="0.1" value="0">
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
                                                    <label class="form-label fw-bold">Contenido de Página 3</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page3_text')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page3_text_editor" style="height: 150px;"></div>
                                                <textarea id="config_page3_text_source" class="form-control" style="height: 150px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page3_text" />
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
                                            <div class="col-md-3">
                                                <label class="form-label">Text Y</label>
                                                <input type="number" class="form-control" id="config_page4_text_top" step="0.01" value="0">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Margen Horizontal del Texto (text-margin-x)">
                                                    Margen X del Texto:
                                                </label>
                                                <input type="number" class="form-control" id="config_page4_text_margin_x" step="0.01" value="0">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="URL de Imagen de Fondo (background-url)">
                                                    URL de Imagen de Fondo:
                                                </label>
                                                <input type="url" class="form-control" id="config_page4_background_url" value="https://printables.happycapibara.com/color-books/chinese_landscape.png">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL de Imagen
                                                </label>
                                                <input type="url" class="form-control" id="config_page4_image_url" value="">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Imagen Y</label>
                                                <input type="number" class="form-control" id="config_page4_image_top" step="0.01" value="0">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Altura de Imagen</label>
                                                <input type="number" class="form-control" id="config_page4_image_height" step="0.1" value="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Ancho de la Imagen (image-width)">
                                                    Ancho de Imagen:
                                                </label>
                                                <input type="number" class="form-control" id="config_page4_image_width" step="0.1" value="0">
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
                                                    <label class="form-label fw-bold">Contenido de Página 4</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page4_text')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page4_text_editor" style="height: 200px;"></div>
                                                <textarea id="config_page4_text_source" class="form-control" style="height: 200px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page4_text" />
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
                                            <div class="col-md-3">
                                                <label class="form-label">Text Y</label>
                                                <input type="number" class="form-control" id="config_page5_text_top" step="0.01" value="5">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Margen Horizontal del Texto (text-margin-x)">
                                                    Margen X del Texto:
                                                </label>
                                                <input type="number" class="form-control" id="config_page5_text_margin_x" step="0.01" value="0">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="URL de Imagen de Fondo (background-url)">
                                                    URL de Imagen de Fondo:
                                                </label>
                                                <input type="url" class="form-control" id="config_page5_background_url" value="https://printables.happycapibara.com/color-books/chinese_background.png">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">
                                                    <i class="fas fa-image me-1"></i>
                                                    URL de Imagen
                                                </label>
                                                <input type="url" class="form-control" id="config_page5_image_url" value="">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Imagen Y</label>
                                                <input type="number" class="form-control" id="config_page5_image_top" step="0.01" value="0">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Altura de Imagen</label>
                                                <input type="number" class="form-control" id="config_page5_image_height" step="0.1" value="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-tooltip" data-bs-toggle="tooltip" title="Ancho de la Imagen (image-width)">
                                                    Ancho de Imagen:
                                                </label>
                                                <input type="number" class="form-control" id="config_page5_image_width" step="0.1" value="0">
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
                                                    <label class="form-label fw-bold">Contenido de Página 5</label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSourceView('config_page5_text')">
                                                        <i class="fas fa-code"></i> Ver código
                                                    </button>
                                                </div>
                                                <div id="config_page5_text_editor" style="height: 200px;"></div>
                                                <textarea id="config_page5_text_source" class="form-control" style="height: 200px; display: none; font-family: monospace; font-size: 12px;" placeholder="Código HTML aquí..."></textarea>
                                                <input type="hidden" id="config_page5_text" />
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
                        <iframe id="previewFrame" class="w-100 h-100" style="min-height: 800px; border: none; background: #f5f5f5;" src="about:blank" allow="fullscreen">
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

        /* Estilos para controles personalizados (tamaño y line-height) */
        .ql-toolbar .ql-formats:last-child {
            border-left: 1px solid #ccc;
            padding-left: 8px;
            margin-left: 8px;
        }

        .ql-toolbar .ql-formats:nth-last-child(2) {
            border-left: 1px solid #ccc;
            padding-left: 8px;
            margin-left: 8px;
        }

        .ql-toolbar .ql-formats label {
            display: inline-block;
            vertical-align: middle;
            font-size: 11px;
            color: #444;
            font-weight: normal;
            user-select: none;
        }

        .ql-toolbar .ql-formats input[type="text"] {
            display: inline-block;
            vertical-align: middle;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 3px 6px;
            font-size: 11px;
            width: 90px;
            height: 24px;
            box-sizing: border-box;
            background: white;
        }

        .ql-toolbar .ql-formats input[type="text"]:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 3px rgba(0, 102, 204, 0.3);
            background: #fafafa;
        }

        .ql-toolbar .ql-formats input[type="text"]:hover {
            border-color: #999;
        }

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

            let content = '';

            // Si el textarea de código está visible, usar su contenido
            if (sourceElement && sourceElement.style.display !== 'none') {
                content = sourceElement.value;
            }
            // Si no, usar el contenido del editor Quill si existe
            else if (editor && editor.root) {
                content = editor.root.innerHTML;
            }
            // Fallback: usar el valor del input hidden
            else {
                const hiddenElement = document.getElementById(hiddenId);
                content = hiddenElement ? hiddenElement.value : '';
            }

            // Limpiar caracteres problemáticos
            if (content) {
                // Remover BOM (Byte Order Mark) y caracteres de espacio sin ancho
                content = content.replace(/\ufeff/g, ''); // BOM
                content = content.replace(/\u200b/g, ''); // Zero Width Space
                content = content.replace(/\u200c/g, ''); // Zero Width Non-Joiner
                content = content.replace(/\u200d/g, ''); // Zero Width Joiner
                content = content.replace(/\u2060/g, ''); // Word Joiner

                // Limpiar múltiples espacios en blanco consecutivos dentro del HTML pero preservar estructura
                content = content.replace(/\s{2,}/g, ' ');

                // Limpiar espacios al inicio y final pero solo del contenido de texto, no del HTML
                content = content.trim();
            }

            return content;
        }

        // Función para limpiar caracteres problemáticos del contenido
        function cleanContent(content) {
            if (!content) return content;

            // Remover BOM (Byte Order Mark) y caracteres de espacio sin ancho
            content = content.replace(/\ufeff/g, ''); // BOM
            content = content.replace(/\u200b/g, ''); // Zero Width Space
            content = content.replace(/\u200c/g, ''); // Zero Width Non-Joiner
            content = content.replace(/\u200d/g, ''); // Zero Width Joiner
            content = content.replace(/\u2060/g, ''); // Word Joiner

            return content;
        }

        // Función robusta para ocultar el loading del preview
        function hidePreviewLoading() {
            console.log('Intentando ocultar loading preview...');

            const $loading = $('#previewLoading');

            // Múltiples métodos para asegurar que se oculte
            $loading.hide();
            $loading.css({
                'display': 'none'
                , 'visibility': 'hidden'
                , 'opacity': '0'
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

        // Función para detectar cuando el PDF está realmente listo
        function waitForPdfReady(iframe, callback, maxAttempts = 15) {
            let attempts = 0;

            function checkPdfStatus() {
                attempts++;
                console.log(`Verificando estado del PDF... intento ${attempts}/${maxAttempts}`);

                try {
                    // Verificar si el iframe tiene el src correcto y es un PDF
                    if (iframe.src && iframe.src !== 'about:blank' && iframe.src.includes('preview-pdf')) {
                        // Para PDFs embebidos, verificar que el contenido esté disponible
                        if (iframe.contentDocument || iframe.contentWindow) {
                            console.log('PDF parece estar listo');
                            callback();
                            return;
                        }
                    }
                } catch (error) {
                    // Los errores de CORS son normales con PDFs embebidos
                    console.log('Error de acceso al contenido (normal para PDFs):', error.message);

                    // Si hay error de acceso pero el src es correcto, asumir que está listo
                    if (iframe.src && iframe.src.includes('preview-pdf')) {
                        console.log('PDF con CORS - asumiendo que está listo');
                        callback();
                        return;
                    }
                }

                // Si no está listo y no hemos alcanzado el máximo de intentos, reintentamos
                if (attempts < maxAttempts) {
                    setTimeout(checkPdfStatus, 400); // Verificar cada 400ms
                } else {
                    console.log('Máximo de intentos alcanzado - continuando');
                    callback();
                }
            }

            // Iniciar verificación
            checkPdfStatus();
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
                'display': 'flex'
                , 'visibility': 'visible'
                , 'opacity': '1'
            }).show();

            // Obtener configuración del JSON seleccionado
            const selectedJsonFile = $('#jsonFileSelector').val() || '/json/chinese.json';

            // Leer configuración desde el JSON para obtener parámetros básicos
            $.get(selectedJsonFile)
                .done(function(config) {
                    console.log('Configuración cargada para preview:', config);

                    // Obtener valores actuales del formulario y sobrescribir la configuración
                    const updatedConfig = {
                        ...config
                    }; // Copiar configuración base

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
                    if ($('#config_verso_text_1_font_family').length && $('#config_verso_text_1_font_family').val()) {
                        updatedConfig.verso['text-1-font-family'] = $('#config_verso_text_1_font_family').val();
                    }
                    if ($('#config_verso_text_2_font_family').length && $('#config_verso_text_2_font_family').val()) {
                        updatedConfig.verso['text-2-font-family'] = $('#config_verso_text_2_font_family').val();
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
                    if ($('#config_recto_text_font_family').length && $('#config_recto_text_font_family').val()) {
                        updatedConfig.recto['text-font-family'] = $('#config_recto_text_font_family').val();
                    }

                    // Actualizar configuraciones de páginas específicas
                    // Page 1
                    if (!updatedConfig.page1) updatedConfig.page1 = {};
                    const textContent = getQuillContent('config_page1_text');
                    if (textContent) {
                        updatedConfig.page1['text'] = textContent;
                    }
                    if ($('#config_page1_text_top').length && $('#config_page1_text_top').val()) {
                        updatedConfig.page1['text-top'] = parseFloat($('#config_page1_text_top').val());
                    }
                    if ($('#config_page1_text_margin_x').length && $('#config_page1_text_margin_x').val()) {
                        updatedConfig.page1['text-margin-x'] = parseFloat($('#config_page1_text_margin_x').val());
                    }
                    if ($('#config_page1_background_url').length && $('#config_page1_background_url').val()) {
                        updatedConfig.page1['background-url'] = $('#config_page1_background_url').val();
                    }
                    if ($('#config_page1_image_url').length && $('#config_page1_image_url').val()) {
                        updatedConfig.page1['image-url'] = $('#config_page1_image_url').val();
                    }
                    if ($('#config_page1_image_top').length && $('#config_page1_image_top').val()) {
                        updatedConfig.page1['image-top'] = parseFloat($('#config_page1_image_top').val());
                    }
                    if ($('#config_page1_image_height').length && $('#config_page1_image_height').val()) {
                        updatedConfig.page1['image-height'] = parseFloat($('#config_page1_image_height').val());
                    }
                    if ($('#config_page1_image_width').length && $('#config_page1_image_width').val()) {
                        updatedConfig.page1['image-width'] = $('#config_page1_image_width').val();
                    }

                    // Page 2
                    if (!updatedConfig.page2) updatedConfig.page2 = {};
                    const page2TextContent = getQuillContent('config_page2_text');
                    if (page2TextContent) {
                        updatedConfig.page2['text'] = page2TextContent;
                    }
                    if ($('#config_page2_text_top').length && $('#config_page2_text_top').val()) {
                        updatedConfig.page2['text-top'] = parseFloat($('#config_page2_text_top').val());
                    }
                    if ($('#config_page2_text_margin_x').length && $('#config_page2_text_margin_x').val()) {
                        updatedConfig.page2['text-margin-x'] = parseFloat($('#config_page2_text_margin_x').val());
                    }
                    if ($('#config_page2_background_url').length && $('#config_page2_background_url').val()) {
                        updatedConfig.page2['background-url'] = $('#config_page2_background_url').val();
                    }
                    if ($('#config_page2_image_url').length && $('#config_page2_image_url').val()) {
                        updatedConfig.page2['image-url'] = $('#config_page2_image_url').val();
                    }
                    if ($('#config_page2_image_top').length && $('#config_page2_image_top').val()) {
                        updatedConfig.page2['image-top'] = parseFloat($('#config_page2_image_top').val());
                    }
                    if ($('#config_page2_image_height').length && $('#config_page2_image_height').val()) {
                        updatedConfig.page2['image-height'] = parseFloat($('#config_page2_image_height').val());
                    }
                    if ($('#config_page2_image_width').length && $('#config_page2_image_width').val()) {
                        updatedConfig.page2['image-width'] = $('#config_page2_image_width').val();
                    }

                    // Page 3
                    if (!updatedConfig.page3) updatedConfig.page3 = {};

                    // Obtener contenido Quill
                    if (typeof getQuillContent === 'function') {
                        updatedConfig.page3['text'] = getQuillContent('config_page3_text');
                    }

                    if ($('#config_page3_text_top').length && $('#config_page3_text_top').val()) {
                        updatedConfig.page3['text-top'] = parseFloat($('#config_page3_text_top').val());
                    }
                    if ($('#config_page3_text_margin_x').length && $('#config_page3_text_margin_x').val()) {
                        updatedConfig.page3['text-margin-x'] = parseFloat($('#config_page3_text_margin_x').val());
                    }
                    if ($('#config_page3_background_url').length && $('#config_page3_background_url').val()) {
                        updatedConfig.page3['background-url'] = $('#config_page3_background_url').val();
                    }
                    if ($('#config_page3_image_url').length && $('#config_page3_image_url').val()) {
                        updatedConfig.page3['image-url'] = $('#config_page3_image_url').val();
                    }
                    if ($('#config_page3_image_top').length && $('#config_page3_image_top').val()) {
                        updatedConfig.page3['image-top'] = parseFloat($('#config_page3_image_top').val());
                    }
                    if ($('#config_page3_image_height').length && $('#config_page3_image_height').val()) {
                        updatedConfig.page3['image-height'] = parseFloat($('#config_page3_image_height').val());
                    }
                    if ($('#config_page3_image_width').length && $('#config_page3_image_width').val()) {
                        updatedConfig.page3['image-width'] = $('#config_page3_image_width').val();
                    }

                    // Page 4
                    if (!updatedConfig.page4) updatedConfig.page4 = {};

                    // Obtener contenido Quill
                    if (typeof getQuillContent === 'function') {
                        updatedConfig.page4['text'] = getQuillContent('config_page4_text');
                    }

                    if ($('#config_page4_text_top').length && $('#config_page4_text_top').val()) {
                        updatedConfig.page4['text-top'] = parseFloat($('#config_page4_text_top').val());
                    }
                    if ($('#config_page4_text_margin_x').length && $('#config_page4_text_margin_x').val()) {
                        updatedConfig.page4['text-margin-x'] = parseFloat($('#config_page4_text_margin_x').val());
                    }
                    if ($('#config_page4_background_url').length && $('#config_page4_background_url').val()) {
                        updatedConfig.page4['background-url'] = $('#config_page4_background_url').val();
                    }
                    if ($('#config_page4_image_url').length && $('#config_page4_image_url').val()) {
                        updatedConfig.page4['image-url'] = $('#config_page4_image_url').val();
                    }
                    if ($('#config_page4_image_top').length && $('#config_page4_image_top').val()) {
                        updatedConfig.page4['image-top'] = parseFloat($('#config_page4_image_top').val());
                    }
                    if ($('#config_page4_image_height').length && $('#config_page4_image_height').val()) {
                        updatedConfig.page4['image-height'] = parseFloat($('#config_page4_image_height').val());
                    }
                    if ($('#config_page4_image_width').length && $('#config_page4_image_width').val()) {
                        updatedConfig.page4['image-width'] = $('#config_page4_image_width').val();
                    }

                    // Page 5
                    if (!updatedConfig.page5) updatedConfig.page5 = {};

                    // Obtener contenido Quill
                    if (typeof getQuillContent === 'function') {
                        updatedConfig.page5['text'] = getQuillContent('config_page5_text');
                    }

                    if ($('#config_page5_text_top').length && $('#config_page5_text_top').val()) {
                        updatedConfig.page5['text-top'] = parseFloat($('#config_page5_text_top').val());
                    }
                    if ($('#config_page5_text_margin_x').length && $('#config_page5_text_margin_x').val()) {
                        updatedConfig.page5['text-margin-x'] = parseFloat($('#config_page5_text_margin_x').val());
                    }
                    if ($('#config_page5_background_url').length && $('#config_page5_background_url').val()) {
                        updatedConfig.page5['background-url'] = $('#config_page5_background_url').val();
                    }
                    if ($('#config_page5_image_url').length && $('#config_page5_image_url').val()) {
                        updatedConfig.page5['image-url'] = $('#config_page5_image_url').val();
                    }
                    if ($('#config_page5_image_top').length && $('#config_page5_image_top').val()) {
                        updatedConfig.page5['image-top'] = parseFloat($('#config_page5_image_top').val());
                    }
                    if ($('#config_page5_image_height').length && $('#config_page5_image_height').val()) {
                        updatedConfig.page5['image-height'] = parseFloat($('#config_page5_image_height').val());
                    }
                    if ($('#config_page5_image_width').length && $('#config_page5_image_width').val()) {
                        updatedConfig.page5['image-width'] = $('#config_page5_image_width').val();
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
                        spreadsheetId: updatedConfig.spreadsheetId
                        , sheetName: updatedConfig.spreadsheetSheetName
                        , imagesURL: updatedConfig.imagesURL
                        , languageSelector: $('#languageSelector').val() || 'ES'
                        , layout: updatedConfig // Enviar la configuración actualizada con valores del formulario
                        , _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    };

                    console.log('Datos para preview:', previewData);

                    // Construir URL para preview PDF con parámetros
                    const urlParams = new URLSearchParams();

                    // Agregar parámetros básicos
                    urlParams.append('numberOfPages', previewData.numberOfPages);
                    if (previewData.spreadsheetId) urlParams.append('spreadsheetId', previewData.spreadsheetId);
                    if (previewData.sheetName) urlParams.append('sheetName', previewData.sheetName);
                    if (previewData.imagesURL) urlParams.append('imagesURL', previewData.imagesURL);
                    if (previewData.languageSelector) urlParams.append('languageSelector', previewData.languageSelector);
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
                    // Add cache busting parameter to prevent loading from cache
                    urlParams.append('_t', Date.now().toString());

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
                    iframe.onerror = null;

                    // Configurar listeners para detectar carga exitosa del PDF
                    let loadingTimerPdf = null;
                    let loadingHidden = false;

                    function hideLoadingOnce() {
                        if (!loadingHidden) {
                            console.log('PDF cargado - ocultando loading');
                            hidePreviewLoading();
                            loadingHidden = true;
                            if (loadingTimerPdf) {
                                clearTimeout(loadingTimerPdf);
                                loadingTimerPdf = null;
                            }
                        }
                    }

                    // Estrategia principal: usar la función de detección inteligente
                    iframe.onload = function() {
                        console.log('Iframe onload evento disparado - verificando estado del PDF...');

                        // Usar función avanzada para detectar cuando el PDF está realmente listo
                        waitForPdfReady(iframe, function() {
                            console.log("PDF está listo en el iframe");
                            // Solo ocultar loading cuando el PDF esté realmente listo
                            hideLoadingOnce();
                        });
                    };

                    // Evento de error
                    iframe.onerror = function() {
                        console.log('Error cargando PDF en iframe');
                        hideLoadingOnce();
                    };

                    // Cargar PDF directamente en iframe
                    console.log('Iniciando carga de PDF en iframe...');
                    console.log('=== URL COMPLETA GENERADA ===');
                    console.log('Preview URL:', previewUrl);
                    console.log('Base URL sin fragmento:', basePreviewUrl);
                    console.log('Parámetros URL:', urlParams.toString());
                    console.log('================================');
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
        function loadAvailableFonts(shouldUpdateSelector = false, selectedFonts = null) {
            console.log('Cargando fuentes disponibles desde el servidor...');

            return $.ajax({
                url: '/generate/fonts'
                , type: 'GET'
                , success: function(fonts) {
                    console.log('Fuentes cargadas desde servidor:', fonts);
                    availableFonts = fonts;

                    // 1. Primero crear CSS dinámico
                    createDynamicFontCSS(fonts);

                    // 2. Luego cargar fuentes con FontFace API (en paralelo)
                    loadFontsWithFontFaceAPI(fonts);

                    // 3. Esperar un poco y luego registrar en Quill
                    setTimeout(() => {
                        registerQuillFonts(fonts);

                        fontsLoaded = true;

                        // Poblar selectores de fuentes en el formulario
                        populateFontSelectors();

                        console.log('Proceso de carga de fuentes completado');
                    }, 200);
                }
                , error: function(xhr, status, error) {
                    console.error('Error al cargar fuentes:', error);
                    console.log('Usando fuentes por defecto debido al error');

                    // Usar fuentes por defecto
                    availableFonts = [{
                            displayName: 'Sans Serif'
                            , cssName: 'sans-serif'
                            , familyName: 'sans-serif'
                        }
                        , {
                            displayName: 'Serif'
                            , cssName: 'serif'
                            , familyName: 'serif'
                        }
                        , {
                            displayName: 'Monospace'
                            , cssName: 'monospace'
                            , familyName: 'monospace'
                        }
                    ];

                    fontsLoaded = true;

                    // Poblar selectores de fuentes en el formulario con fuentes por defecto
                    populateFontSelectors();
                }
            });
        }

        // Función para poblar los selectores de fuentes en el formulario
        function populateFontSelectors() {
            console.log('Poblando selectores de fuentes...');

            const fontSelectors = [
                'config_verso_text_1_font_family',
                'config_verso_text_2_font_family',
                'config_recto_text_font_family'
            ];

            fontSelectors.forEach(selectorId => {
                const $selector = $('#' + selectorId);
                if ($selector.length) {
                    // Limpiar opciones existentes excepto la primera
                    $selector.find('option:not(:first)').remove();

                    // Añadir opciones de fuentes
                    availableFonts.forEach(font => {
                        $selector.append(
                            `<option value="${font.cssName}">${font.displayName}</option>`
                        );
                    });

                    console.log(`Selector ${selectorId} poblado con ${availableFonts.length} fuentes`);
                }
            });
        }

        // Función para establecer valores de fuentes desde configuración
        function setFontSelectorValues(config) {
            console.log('Estableciendo valores de selectores de fuentes...');

            if (config.verso) {
                $('#config_verso_text_1_font_family').val(config.verso['text-1-font-family'] || '');
                $('#config_verso_text_2_font_family').val(config.verso['text-2-font-family'] || '');
            }

            if (config.recto) {
                $('#config_recto_text_font_family').val(config.recto['text-font-family'] || '');
            }

            console.log('Valores de fuentes establecidos:', {
                verso_text_1: config.verso?.['text-1-font-family'],
                verso_text_2: config.verso?.['text-2-font-family'],
                recto_text: config.recto?.['text-font-family']
            });
        }

        // Función mejorada para establecer valores de fuentes desde configuración
        function setFontSelectorValuesImproved(config) {
            console.log('Estableciendo valores de selectores de fuentes (versión mejorada)...');
            console.log('Config recibido:', config);
            console.log('Fuentes disponibles:', availableFonts);

            // Función helper para encontrar fuente compatible
            function findCompatibleFont(fontValue) {
                if (!fontValue) return '';

                console.log(`Buscando fuente para: ${fontValue}`);

                // Buscar por coincidencia exacta en cssName
                let found = availableFonts.find(font => font.cssName === fontValue);
                if (found) {
                    console.log(`Encontrada por cssName: ${found.cssName}`);
                    return found.cssName;
                }

                // Buscar por coincidencia exacta en filename (sin extensión)
                found = availableFonts.find(font =>
                    font.filename.replace(/\.(ttf|otf|woff|woff2)$/i, '') === fontValue
                );
                if (found) {
                    console.log(`Encontrada por filename: ${found.filename} -> ${found.cssName}`);
                    return found.cssName;
                }

                // Buscar por coincidencia parcial en displayName
                found = availableFonts.find(font =>
                    font.displayName.toLowerCase().includes(fontValue.toLowerCase()) ||
                    fontValue.toLowerCase().includes(font.displayName.toLowerCase())
                );
                if (found) {
                    console.log(`Encontrada por displayName: ${found.displayName} -> ${found.cssName}`);
                    return found.cssName;
                }

                console.warn(`No se encontró fuente compatible para: ${fontValue}`);
                return '';
            }

            // Esperar a que los selectores estén poblados
            setTimeout(() => {
                if (config.verso) {
                    const text1Font = findCompatibleFont(config.verso['text-1-font-family']);
                    const text2Font = findCompatibleFont(config.verso['text-2-font-family']);

                    const $selector1 = $('#config_verso_text_1_font_family');
                    const $selector2 = $('#config_verso_text_2_font_family');

                    if ($selector1.length) {
                        $selector1.val(text1Font);
                        console.log(`Verso text-1 font: ${config.verso['text-1-font-family']} -> ${text1Font} (selector value: ${$selector1.val()})`);
                    }

                    if ($selector2.length) {
                        $selector2.val(text2Font);
                        console.log(`Verso text-2 font: ${config.verso['text-2-font-family']} -> ${text2Font} (selector value: ${$selector2.val()})`);
                    }
                }

                if (config.recto) {
                    const textFont = findCompatibleFont(config.recto['text-font-family']);
                    const $selectorRecto = $('#config_recto_text_font_family');

                    if ($selectorRecto.length) {
                        $selectorRecto.val(textFont);
                        console.log(`Recto text font: ${config.recto['text-font-family']} -> ${textFont} (selector value: ${$selectorRecto.val()})`);
                    }
                }

                console.log('Valores de fuentes establecidos completados');
            }, 200);
        }        // Función moderna para cargar fuentes usando FontFace API
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
                        font.familyName
                        , `url('/fonts/${font.filename}')`, {
                            style: 'normal'
                            , weight: 'normal'
                            , display: 'swap' // Mejor rendimiento
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

                /* Mejorar el dropdown de fuentes */
                .ql-picker.ql-font .ql-picker-options {
                    max-height: 300px;
                    overflow-y: auto;
                    width: auto;
                    min-width: 200px;
                    max-width: 350px;
                }

                .ql-picker.ql-font .ql-picker-item {
                    padding: 8px 12px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    line-height: 1.2;
                    min-height: auto;
                }

                .ql-picker.ql-font .ql-picker-item::before {
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    display: block;
                    width: 100%;
                }

                .ql-picker.ql-font .ql-picker-label {
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 120px;
                }

                .ql-picker.ql-font .ql-picker-label::before {
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    display: block;
                    width: 100%;
                }

                /* Ajustar la toolbar para que no se rompa */
                .ql-toolbar {
                    flex-wrap: wrap;
                    border-bottom: 1px solid #ccc;
                }

                .ql-toolbar .ql-formats {
                    margin-right: 8px;
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

        // Función para obtener las fuentes seleccionadas (ya no necesaria, retorna array vacío)
        function getSelectedFonts() {
            return [];
        }

    </script>

    <script>
        $(document).ready(function() {
            // Inicializar tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Aplicar automáticamente tooltips a todos los labels que no los tengan
            $('.form-label').each(function() {
                var $label = $(this);

                // Si ya tiene tooltip, saltar
                if ($label.attr('data-bs-toggle')) {
                    return;
                }

                // Obtener el texto del label (sin iconos)
                var labelText = $label.text().trim();

                // Si tiene texto, agregar tooltip y clase
                if (labelText) {
                    $label.addClass('form-label-tooltip');
                    $label.attr('data-bs-toggle', 'tooltip');
                    $label.attr('title', labelText);

                    // Inicializar el tooltip inmediatamente
                    new bootstrap.Tooltip($label[0]);
                }
            });

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
                    , languageSelector: $('#languageSelector').val() || 'ES'
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
                            // Valores de fuentes se establecerán después de cargar las fuentes
                        }

                        // Recto
                        if (config.recto) {
                            $('#config_recto_margin').val(config.recto.margin);
                            $('#config_recto_image_margin').val(config.recto['image-margin']);
                            $('#config_recto_font_size').val(config.recto['font-size']);
                            $('#config_recto_text_top').val(config.recto['text-top']);
                            // Valor de fuente se establecerá después de cargar las fuentes
                        }

                        // Page 1
                        if (config.page1) {
                            $('#config_page1_text_top').val(config.page1['text-top']);
                            $('#config_page1_text_margin_x').val(config.page1['text-margin-x'] || 0);
                            $('#config_page1_background_url').val(config.page1['background-url'] || '');
                            $('#config_page1_image_url').val(config.page1['image-url']);
                            $('#config_page1_image_top').val(config.page1['image-top']);
                            $('#config_page1_image_height').val(config.page1['image-height']);
                            $('#config_page1_image_width').val(config.page1['image-width'] || 'auto');

                            // Cargar textos en editores Quill con delay para asegurar que están listos
                            setTimeout(function() {
                                console.log('Cargando contenido de página 1...');
                                if (config.page1['text']) {
                                    setQuillContent('config_page1_text', config.page1['text']);
                                } else {
                                    setQuillContent('config_page1_text', 'El secreto');
                                }
                            }, 100);
                        }

                        // Page 2
                        if (config.page2) {
                            $('#config_page2_text_top').val(config.page2['text-top']);
                            $('#config_page2_text_margin_x').val(config.page2['text-margin-x'] || 0.5);
                            $('#config_page2_background_url').val(config.page2['background-url'] || '');
                            $('#config_page2_image_url').val(config.page2['image-url'] || '');
                            $('#config_page2_image_top').val(config.page2['image-top'] || 0);
                            $('#config_page2_image_height').val(config.page2['image-height'] || 0);
                            $('#config_page2_image_width').val(config.page2['image-width'] || 'auto');
                            $('#config_page2_text_top').val(config.page2['text-top']);

                            // Cargar textos en editores Quill con delay para asegurar que están listos
                            setTimeout(function() {
                                console.log('Cargando contenido de página 2...');
                                if (config.page2['text']) {
                                    setQuillContent('config_page2_text', config.page2['text']);
                                }
                            }, 200);
                        }

                        // Page 3
                        if (config.page3) {
                            $('#config_page3_text_top').val(config.page3['text-top']);
                            $('#config_page3_text_margin_x').val(config.page3['text-margin-x'] || 0);
                            $('#config_page3_background_url').val(config.page3['background-url'] || '');
                            $('#config_page3_image_url').val(config.page3['image-url'] || '');
                            $('#config_page3_image_top').val(config.page3['image-top'] || 0);
                            $('#config_page3_image_height').val(config.page3['image-height'] || 0);
                            $('#config_page3_image_width').val(config.page3['image-width'] || 'auto');
                            $('#config_page3_text_top').val(config.page3['text-top']);

                            // Cargar textos en editores Quill con delay para asegurar que están listos
                            setTimeout(function() {
                                console.log('Cargando contenido de página 3...');
                                if (config.page3['text']) {
                                    setQuillContent('config_page3_text', config.page3['text']);
                                }
                            }, 300);
                        }

                        // Page 4
                        if (config.page4) {
                            $('#config_page4_text_top').val(config.page4['text-top'] || 0);
                            $('#config_page4_text_margin_x').val(config.page4['text-margin-x'] || 0);
                            $('#config_page4_background_url').val(config.page4['background-url'] || '');
                            $('#config_page4_image_url').val(config.page4['image-url'] || '');
                            $('#config_page4_image_top').val(config.page4['image-top'] || 0);
                            $('#config_page4_image_height').val(config.page4['image-height'] || 0);
                            $('#config_page4_image_width').val(config.page4['image-width'] || 'auto');

                            // Cargar textos en editores Quill con delay para asegurar que están listos
                            setTimeout(function() {
                                console.log('Cargando contenido de página 4...');
                                if (config.page4['text']) {
                                    setQuillContent('config_page4_text', config.page4['text']);
                                }
                            }, 350);
                        }

                        // Page 5
                        if (config.page5) {
                            $('#config_page5_text_top').val(config.page5['text-top']);
                            $('#config_page5_text_margin_x').val(config.page5['text-margin-x'] || 0);
                            $('#config_page5_background_url').val(config.page5['background-url'] || '');
                            $('#config_page5_image_url').val(config.page5['image-url'] || '');
                            $('#config_page5_image_top').val(config.page5['image-top'] || 0);
                            $('#config_page5_image_height').val(config.page5['image-height'] || 0);
                            $('#config_page5_image_width').val(config.page5['image-width'] || 'auto');

                            // Cargar textos en editores Quill con delay para asegurar que están listos
                            setTimeout(function() {
                                console.log('Cargando contenido de página 5...');
                                if (config.page5['text']) {
                                    setQuillContent('config_page5_text', config.page5['text']);
                                }
                            }, 400);
                        }

                        // Cargar fuentes disponibles después de cargar configuración
                        loadAvailableFonts(false).then(function() {
                            console.log('Fuentes cargadas después de aplicar configuración');

                            // Establecer valores de selectores de fuentes después de poblarlos
                            setTimeout(function() {
                                setFontSelectorValuesImproved(config);
                            }, 100);
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
                    , verso_secondary_font_size: $('#config_verso_secondary_font_size').val()
                    , verso_text_1_font_family: $('#config_verso_text_1_font_family').val()
                    , verso_text_2_font_family: $('#config_verso_text_2_font_family').val(),

                    // Recto
                    recto_margin: $('#config_recto_margin').val()
                    , recto_image_margin: $('#config_recto_image_margin').val()
                    , recto_font_size: $('#config_recto_font_size').val()
                    , recto_text_top: $('#config_recto_text_top').val()
                    , recto_text_font_family: $('#config_recto_text_font_family').val(),

                    // Page 1
                    page1_text: getQuillContent('config_page1_text')
                    , page1_text_top: $('#config_page1_text_top').val()
                    , page1_text_margin_x: $('#config_page1_text_margin_x').val()
                    , page1_background_url: $('#config_page1_background_url').val()
                    , page1_image_url: $('#config_page1_image_url').val()
                    , page1_image_top: $('#config_page1_image_top').val()
                    , page1_image_height: $('#config_page1_image_height').val()
                    , page1_image_width: $('#config_page1_image_width').val(),

                    // Page 2
                    page2_text: getQuillContent('config_page2_text')
                    , page2_text_top: $('#config_page2_text_top').val()
                    , page2_text_margin_x: $('#config_page2_text_margin_x').val()
                    , page2_background_url: $('#config_page2_background_url').val()
                    , page2_image_url: $('#config_page2_image_url').val()
                    , page2_image_top: $('#config_page2_image_top').val()
                    , page2_image_height: $('#config_page2_image_height').val()
                    , page2_image_width: $('#config_page2_image_width').val(),

                    // Page 3
                    page3_text: getQuillContent('config_page3_text')
                    , page3_text_top: $('#config_page3_text_top').val()
                    , page3_text_margin_x: $('#config_page3_text_margin_x').val()
                    , page3_background_url: $('#config_page3_background_url').val()
                    , page3_image_url: $('#config_page3_image_url').val()
                    , page3_image_top: $('#config_page3_image_top').val()
                    , page3_image_height: $('#config_page3_image_height').val()
                    , page3_image_width: $('#config_page3_image_width').val(),

                    // Page 4
                    page4_text: getQuillContent('config_page4_text')
                    , page4_text_top: $('#config_page4_text_top').val()
                    , page4_text_margin_x: $('#config_page4_text_margin_x').val()
                    , page4_background_url: $('#config_page4_background_url').val()
                    , page4_image_url: $('#config_page4_image_url').val()
                    , page4_image_top: $('#config_page4_image_top').val()
                    , page4_image_height: $('#config_page4_image_height').val()
                    , page4_image_width: $('#config_page4_image_width').val(),

                    // Page 5
                    page5_text: getQuillContent('config_page5_text')
                    , page5_text_top: $('#config_page5_text_top').val()
                    , page5_text_margin_x: $('#config_page5_text_margin_x').val()
                    , page5_background_url: $('#config_page5_background_url').val()
                    , page5_image_url: $('#config_page5_image_url').val()
                    , page5_image_top: $('#config_page5_image_top').val()
                    , page5_image_height: $('#config_page5_image_height').val()
                    , page5_image_width: $('#config_page5_image_width').val()
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
                            // Recargar preview automáticamente después de guardar
                            console.log('Recargando preview automáticamente después de guardar configuración');
                            updatePreview(true);
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

            // Preview configuration button
            $('#previewConfigBtn').click(function() {
                console.log('Preview manual solicitado');
                updatePreview(true); // Forzar actualización inmediata del preview
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

            // Función para agregar control de tamaño personalizado a un editor
            function addCustomSizeControl(quill, editorId) {
                const toolbar = quill.getModule('toolbar');
                const container = toolbar.container;

                // Variable para guardar la selección
                let savedRange = null;

                // Crear elemento personalizado para tamaño
                const sizeGroup = document.createElement('span');
                sizeGroup.className = 'ql-formats';

                const sizeLabel = document.createElement('label');
                sizeLabel.textContent = 'Tamaño: ';

                const sizeInput = document.createElement('input');
                sizeInput.type = 'text';
                sizeInput.placeholder = '1.2cm';
                sizeInput.title = 'Introduce el tamaño (ej: 1.2cm)';

                sizeGroup.appendChild(sizeLabel);
                sizeGroup.appendChild(sizeInput);

                // Agregar al final de la toolbar
                container.appendChild(sizeGroup);

                // Función para aplicar el formato
                function applySize() {
                    const size = sizeInput.value.trim();
                    if (size) {
                        // Restaurar el foco al editor antes de aplicar el formato
                        quill.focus();

                        // Si tenemos una selección guardada, restaurarla
                        if (savedRange) {
                            quill.setSelection(savedRange.index, savedRange.length);
                        }

                        // Obtener la selección actual (después de restaurar)
                        const currentRange = quill.getSelection();

                        if (currentRange && currentRange.length > 0) {
                            // Aplicar a texto seleccionado
                            quill.formatText(currentRange.index, currentRange.length, 'size', size);
                            console.log('Tamaño aplicado a selección:', size, currentRange);
                        } else {
                            // Aplicar al cursor (próximo texto que se escriba)
                            quill.format('size', size);
                            console.log('Tamaño aplicado al cursor:', size);
                        }

                        // Limpiar la selección guardada
                        savedRange = null;
                    }
                }

                // Guardar selección cuando el input obtiene foco
                sizeInput.addEventListener('mousedown', function(e) {
                    // Guardar la selección actual antes de que se pierda
                    savedRange = quill.getSelection();
                    console.log('Selección guardada:', savedRange);
                });

                sizeInput.addEventListener('focus', function(e) {
                    // También guardar cuando obtiene foco por teclado
                    if (!savedRange) {
                        savedRange = quill.getSelection();
                        console.log('Selección guardada en focus:', savedRange);
                    }
                });

                // Event listener para aplicar el tamaño al presionar Enter
                sizeInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applySize();
                        sizeInput.blur(); // Quitar foco del input
                    }
                });

                // Event listener para aplicar el tamaño al perder foco
                sizeInput.addEventListener('blur', function() {
                    // Pequeño delay para permitir que se complete la acción
                    setTimeout(applySize, 50);
                });

                // Mostrar tamaño actual cuando se selecciona texto
                quill.on('selection-change', function(range) {
                    // Solo actualizar si el input no tiene foco
                    if (range && document.activeElement !== sizeInput) {
                        const format = quill.getFormat(range);
                        if (format.size) {
                            sizeInput.value = format.size;
                        } else {
                            sizeInput.value = '';
                        }
                    }
                });

                console.log('Control de tamaño personalizado agregado a:', editorId);
            }

            // Función para agregar control de line-height personalizado a un editor
            function addCustomLineHeightControl(quill, editorId) {
                const toolbar = quill.getModule('toolbar');
                const container = toolbar.container;

                // Variable para guardar la selección
                let savedRange = null;

                // Crear elemento personalizado para line-height
                const lineHeightGroup = document.createElement('span');
                lineHeightGroup.className = 'ql-formats';

                const lineHeightLabel = document.createElement('label');
                lineHeightLabel.textContent = 'Interlineado: ';

                const lineHeightInput = document.createElement('input');
                lineHeightInput.type = 'text';
                lineHeightInput.placeholder = '1.2cm';
                lineHeightInput.title = 'Introduce el interlineado (ej: 1.2cm)';

                lineHeightGroup.appendChild(lineHeightLabel);
                lineHeightGroup.appendChild(lineHeightInput);

                // Agregar al final de la toolbar
                container.appendChild(lineHeightGroup);

                // Función para aplicar el formato
                function applyLineHeight() {
                    const lineHeight = lineHeightInput.value.trim();
                    if (lineHeight) {
                        // Restaurar el foco al editor antes de aplicar el formato
                        quill.focus();

                        // Si tenemos una selección guardada, restaurarla
                        if (savedRange) {
                            quill.setSelection(savedRange.index, savedRange.length);
                        }

                        // Obtener la selección actual (después de restaurar)
                        const currentRange = quill.getSelection();

                        if (currentRange && currentRange.length > 0) {
                            // Aplicar a texto seleccionado
                            quill.formatText(currentRange.index, currentRange.length, 'line-height', lineHeight);
                            console.log('Line-height aplicado a selección:', lineHeight, currentRange);
                        } else {
                            // Aplicar al cursor (próximo texto que se escriba)
                            quill.format('line-height', lineHeight);
                            console.log('Line-height aplicado al cursor:', lineHeight);
                        }

                        // Limpiar la selección guardada
                        savedRange = null;
                    }
                }

                // Guardar selección cuando el input obtiene foco
                lineHeightInput.addEventListener('mousedown', function(e) {
                    // Guardar la selección actual antes de que se pierda
                    savedRange = quill.getSelection();
                    console.log('Selección guardada para line-height:', savedRange);
                });

                lineHeightInput.addEventListener('focus', function(e) {
                    // También guardar cuando obtiene foco por teclado
                    if (!savedRange) {
                        savedRange = quill.getSelection();
                        console.log('Selección guardada en focus para line-height:', savedRange);
                    }
                });

                // Event listener para aplicar el line-height al presionar Enter
                lineHeightInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyLineHeight();
                        lineHeightInput.blur(); // Quitar foco del input
                    }
                });

                // Event listener para aplicar el line-height al perder foco
                lineHeightInput.addEventListener('blur', function() {
                    // Pequeño delay para permitir que se complete la acción
                    setTimeout(applyLineHeight, 50);
                });

                // Mostrar line-height actual cuando se selecciona texto
                quill.on('selection-change', function(range) {
                    // Solo actualizar si el input no tiene foco
                    if (range && document.activeElement !== lineHeightInput) {
                        const format = quill.getFormat(range);
                        if (format['line-height']) {
                            lineHeightInput.value = format['line-height'];
                        } else {
                            lineHeightInput.value = '';
                        }
                    }
                });

                console.log('Control de line-height personalizado agregado a:', editorId);
            }

            // Función para agregar botón de pantalla completa a un editor
            function addFullscreenButton(quill, editorId) {
                const toolbar = quill.getModule('toolbar');
                const container = toolbar.container;

                // Crear elemento personalizado para fullscreen
                const fullscreenGroup = document.createElement('span');
                fullscreenGroup.className = 'ql-formats';

                const fullscreenBtn = document.createElement('button');
                fullscreenBtn.type = 'button';
                fullscreenBtn.className = 'ql-fullscreen';
                fullscreenBtn.title = 'Alternar pantalla completa';
                fullscreenBtn.innerHTML = '⛶'; // Icono de expand

                fullscreenGroup.appendChild(fullscreenBtn);
                container.appendChild(fullscreenGroup);

                // Manejar click del botón
                fullscreenBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleFullscreen(quill, editorId, fullscreenBtn);
                });

                console.log('Botón de pantalla completa agregado a:', editorId);
            }

            // Función para alternar pantalla completa
            function toggleFullscreen(quill, editorId, button) {
                const editorContainer = document.getElementById(editorId).closest('.ql-container').parentElement;
                const isFullscreen = editorContainer.classList.contains('quill-fullscreen');

                if (isFullscreen) {
                    // Salir de pantalla completa
                    editorContainer.classList.remove('quill-fullscreen');
                    button.innerHTML = '⛶'; // Icono expand
                    button.title = 'Alternar pantalla completa';
                    document.body.style.overflow = '';

                    // Remover listener de ESC
                    document.removeEventListener('keydown', window.currentFullscreenEscListener);
                } else {
                    // Entrar en pantalla completa
                    editorContainer.classList.add('quill-fullscreen');
                    button.innerHTML = '⛗'; // Icono compress
                    button.title = 'Salir de pantalla completa';
                    document.body.style.overflow = 'hidden';

                    // Agregar listener para ESC
                    window.currentFullscreenEscListener = function(e) {
                        if (e.key === 'Escape') {
                            toggleFullscreen(quill, editorId, button);
                        }
                    };
                    document.addEventListener('keydown', window.currentFullscreenEscListener);
                }

                // Trigger resize para que Quill se ajuste al nuevo tamaño
                setTimeout(() => {
                    quill.focus();
                }, 100);
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

                // Registrar formato de tamaño personalizado que permite cualquier valor
                const SizeStyle = Quill.import('attributors/style/size');
                SizeStyle.whitelist = null; // Permitir cualquier valor
                Quill.register(SizeStyle, true);
                console.log('Formato de tamaño personalizado registrado');

                // Crear y registrar formato de line-height personalizado
                const Parchment = Quill.import('parchment');
                const LineHeightStyle = new Parchment.Attributor.Style('line-height', 'line-height', {
                    scope: Parchment.Scope.INLINE,
                    whitelist: null // Permitir cualquier valor
                });
                Quill.register(LineHeightStyle, true);
                console.log('Formato de line-height personalizado registrado');

                console.log('Quill.js detectado correctamente');
                const editorConfigs = [
                    // Página 1
                    {
                        id: 'config_page1_text_editor'
                        , hiddenId: 'config_page1_text'
                    },
                    // Página 2
                    {
                        id: 'config_page2_text_editor'
                        , hiddenId: 'config_page2_text'
                    },
                    // Página 3
                    {
                        id: 'config_page3_text_editor'
                        , hiddenId: 'config_page3_text'
                    },
                    // Página 4
                    {
                        id: 'config_page4_text_editor'
                        , hiddenId: 'config_page4_text'
                    },
                    // Página 5
                    {
                        id: 'config_page5_text_editor'
                        , hiddenId: 'config_page5_text'
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
                                        'color': []
                                    }, {
                                        'background': []
                                    }]
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

                        // Agregar control de tamaño personalizado
                        addCustomSizeControl(quill, config.id);

                        // Agregar control de line-height personalizado
                        addCustomLineHeightControl(quill, config.id);

                        // Agregar botón de pantalla completa
                        addFullscreenButton(quill, config.id);

                        // Sincronizar con input hidden cuando cambie el contenido
                        quill.on('text-change', function(delta, oldDelta, source) {
                            let content = quill.root.innerHTML;

                            // Limpiar caracteres problemáticos
                            content = cleanContent(content);

                            const sourceElement = document.getElementById(config.hiddenId + '_source');

                            // Actualizar input hidden con contenido limpio
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

                // Cargar fuentes primero (sin actualizar selector automáticamente)
                loadAvailableFonts(false).then(function() {
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
