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
                                    Libro
                                </label>
                                <div class="input-icon-container">
                                    <select class="form-select" id="jsonFileSelector" name="jsonFileSelector">
                                        @foreach($books as $book)
                                        <option value="{{ $book->token }}">
                                            {{ $book->token }}
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

    <script src="{{ asset('/js/main.js') }}"></script>

</body>
</html>
