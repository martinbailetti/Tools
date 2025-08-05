<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Generador de PDF - Color Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style id="dynamic-fonts">
        /* CSS dinámico para fuentes se insertará aquí */
    </style>

    <style>
        .config-section {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            margin-bottom: 2rem;
        }

        .section-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
            margin: -1rem -1rem 1rem -1rem;
        }

        .font-selection-area {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background: white;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-content {
            text-align: center;
            color: white;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .quill-container {
            background: white;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .ql-toolbar {
            border-bottom: 1px solid #ced4da;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .ql-container {
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <div class="mt-3 h5">Generando PDF...</div>
            <div class="text-muted">Por favor espera mientras se procesa tu solicitud</div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-file-pdf me-2"></i>
                            Generador de PDF - Color Book
                        </h1>
                    </div>
                </div>

                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Configuración General -->
                <div class="card config-section">
                    <div class="card-body">
                        <div class="section-header p-3">
                            <h2 class="h4 mb-0">
                                <i class="fas fa-cog me-2"></i>
                                Configuración General del Template
                            </h2>
                        </div>

                        <div class="row">
                            <!-- Configuración básica -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="config_spreadsheet_id" class="form-label fw-bold">
                                        <i class="fas fa-table me-1"></i>
                                        ID de la Hoja de Cálculo
                                    </label>
                                    <input type="text" class="form-control" id="config_spreadsheet_id"
                                           placeholder="ID de Google Sheets">
                                </div>

                                <div class="mb-3">
                                    <label for="config_sheet_name" class="form-label fw-bold">
                                        <i class="fas fa-file-alt me-1"></i>
                                        Nombre de la Hoja
                                    </label>
                                    <input type="text" class="form-control" id="config_sheet_name"
                                           placeholder="Nombre de la pestaña">
                                </div>

                                <div class="mb-3">
                                    <label for="config_image_base_url" class="form-label fw-bold">
                                        <i class="fas fa-link me-1"></i>
                                        URL Base de Imágenes
                                    </label>
                                    <input type="url" class="form-control" id="config_image_base_url"
                                           placeholder="https://ejemplo.com/imagenes/">
                                </div>
                            </div>

                            <!-- Selector de fuentes -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-font me-1"></i>
                                            Fuentes Seleccionadas
                                            <span id="fontCounterBadge" class="badge bg-primary ms-2">0/0</span>
                                        </label>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" id="selectAllFonts" class="btn btn-outline-success">
                                                <i class="fas fa-check-double me-1"></i>
                                                Todas
                                            </button>
                                            <button type="button" id="deselectAllFonts" class="btn btn-outline-danger">
                                                <i class="fas fa-times me-1"></i>
                                                Ninguna
                                            </button>
                                        </div>
                                    </div>
                                    <div id="config_fonts_container" class="font-selection-area">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Cargando fuentes...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de configuración -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <select class="form-select" id="jsonFileSelector" style="max-width: 300px;">
                                        <option value="">Seleccionar archivo de configuración...</option>
                                        @foreach($jsonFiles as $file)
                                            <option value="{{ $file }}">{{ $file }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="loadConfigBtn" class="btn btn-outline-primary">
                                        <i class="fas fa-upload me-1"></i>
                                        Cargar Configuración
                                    </button>
                                    <button type="button" id="saveConfigBtn" class="btn btn-outline-success">
                                        <i class="fas fa-save me-1"></i>
                                        Guardar Configuración
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de generación -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-play me-2"></i>
                            Generar PDF
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="pdfGeneratorForm">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label for="numberOfPages" class="form-label fw-bold">
                                        <i class="fas fa-sort-numeric-up me-1"></i>
                                        Número de Páginas
                                    </label>
                                    <input type="number" class="form-control" id="numberOfPages" name="numberOfPages"
                                           min="1" placeholder="Opcional (todas las filas)">
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Si no especificas un número, se procesarán todas las filas de la hoja de cálculo.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" id="generateBtn" class="btn btn-success w-100">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Generar PDF
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Instrucciones -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Instrucciones de Uso
                        </h3>
                    </div>
                    <div class="card-body">
                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item d-flex align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <i class="fas fa-table me-1"></i>
                                        ID de Google Sheets
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
                                    <i class="fas fa-flag me-2 text-success"></i>
                                    <strong>CN:</strong> Texto en chino (opcional)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        // Fallback para Quill.js desde otra CDN si falla
        if (typeof Quill === 'undefined') {
            console.log('Cargando Quill.js desde CDN alternativo...');
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.min.js"><\/script>');
        }
    </script>

    <script>
        // Variables globales simplificadas
        let availableFonts = [];
        let fontsLoaded = false;
        let loadingFonts = false;

        $(document).ready(function() {
            console.log('Iniciando sistema de gestión de fuentes...');

            // Solo cargar fuentes para el selector
            loadFontsSelector();

            // Manejar envío del formulario
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

                // Preparar datos del formulario
                const formData = {
                    numberOfPages: $('#numberOfPages').val() || 0,
                    selectedJsonFile: $('#jsonFileSelector').val(),
                    selectedFonts: getSelectedFonts(),
                    _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                };

                // Realizar petición AJAX
                $.ajax({
                    url: '{{ route("pdf.generate") }}',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data, status, xhr) {
                        // Crear enlace de descarga
                        const blob = new Blob([data], { type: 'application/pdf' });
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;

                        // Obtener nombre del archivo
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

                        showAlert('success', '<i class="fas fa-check-circle me-2"></i>PDF generado exitosamente!');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error al generar el PDF';

                        if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                errorMessage = response.message || response.error || errorMessage;
                            } catch (e) {
                                errorMessage = xhr.responseText;
                            }
                        }

                        showAlert('danger', '<i class="fas fa-exclamation-triangle me-2"></i>' + errorMessage);
                    },
                    complete: function() {
                        // Ocultar loading overlay
                        $('#loadingOverlay').fadeOut();

                        // Restaurar botón
                        $btn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // Cargar configuración al seleccionar archivo
            $('#loadConfigBtn').on('click', function() {
                const selectedFile = $('#jsonFileSelector').val();
                if (!selectedFile) {
                    showAlert('warning', '<i class="fas fa-exclamation-triangle me-2"></i>Por favor selecciona un archivo JSON');
                    return;
                }

                loadConfig(selectedFile);
            });

            // Guardar configuración
            $('#saveConfigBtn').on('click', function() {
                const selectedFile = $('#jsonFileSelector').val();
                if (!selectedFile) {
                    showAlert('warning', '<i class="fas fa-exclamation-triangle me-2"></i>Por favor selecciona un archivo JSON');
                    return;
                }

                saveConfig(selectedFile);
            });

            // Eventos para seleccionar/deseleccionar fuentes
            $('#selectAllFonts').on('click', function() {
                $('.font-checkbox').prop('checked', true);
                updateFontCounter();
            });

            $('#deselectAllFonts').on('click', function() {
                $('.font-checkbox').prop('checked', false);
                updateFontCounter();
            });

            // Evento para actualizar contador cuando cambia selección
            $(document).on('change', '.font-checkbox', function() {
                updateFontCounter();
            });
        });

        // Función para cargar selector de fuentes
        function loadFontsSelector() {
            if (loadingFonts) {
                console.log('Ya se están cargando las fuentes...');
                return;
            }

            loadingFonts = true;
            console.log('Cargando fuentes para selector...');

            // Mostrar indicador de carga
            $('#config_fonts_container').html(`
                <div class="text-center p-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando fuentes...</span>
                    </div>
                    <div class="mt-2">Cargando fuentes...</div>
                </div>
            `);

            $.get('/fonts')
                .done(function(fonts) {
                    console.log('Fuentes cargadas exitosamente:', fonts);
                    availableFonts = fonts;
                    updateFontsSelector(fonts);
                })
                .fail(function() {
                    console.error('Error al cargar fuentes');
                    $('#config_fonts_container').html(`
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar las fuentes disponibles
                        </div>
                    `);
                })
                .always(function() {
                    loadingFonts = false;
                });
        }

        // Función para actualizar selector de fuentes
        function updateFontsSelector(fonts) {
            const container = $('#config_fonts_container');

            if (fonts.length === 0) {
                container.html(`
                    <div class="text-muted text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No se encontraron fuentes disponibles
                    </div>
                `);
                return;
            }

            let html = '<div class="row">';
            fonts.forEach((font, index) => {
                html += `
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input font-checkbox" type="checkbox"
                                   value="${font}" id="font_${index}" checked>
                            <label class="form-check-label" for="font_${index}"
                                   style="font-family: '${font}', sans-serif;">
                                ${font}
                            </label>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            container.html(html);
            updateFontCounter();
        }

        // Función para obtener fuentes seleccionadas
        function getSelectedFonts() {
            const selectedFonts = [];
            $('.font-checkbox:checked').each(function() {
                selectedFonts.push($(this).val());
            });
            return selectedFonts;
        }

        // Función para actualizar contador de fuentes
        function updateFontCounter() {
            const totalFonts = $('.font-checkbox').length;
            const selectedFonts = $('.font-checkbox:checked').length;

            $('#fontCounterBadge').text(`${selectedFonts}/${totalFonts}`);

            if (selectedFonts === 0) {
                $('#fontCounterBadge').removeClass('bg-primary bg-success').addClass('bg-warning');
            } else if (selectedFonts === totalFonts) {
                $('#fontCounterBadge').removeClass('bg-primary bg-warning').addClass('bg-success');
            } else {
                $('#fontCounterBadge').removeClass('bg-warning bg-success').addClass('bg-primary');
            }
        }

        // Función para cargar configuración
        function loadConfig(filename) {
            console.log('Cargando configuración desde:', filename);

            $.get('{{ route("config.load") }}', { filename: filename })
                .done(function(config) {
                    console.log('Configuración cargada:', config);

                    // Cargar configuración básica
                    $('#config_spreadsheet_id').val(config.spreadsheet_id || '');
                    $('#config_sheet_name').val(config.sheet_name || '');
                    $('#config_image_base_url').val(config.image_base_url || '');

                    // Cargar fuentes seleccionadas si existen
                    if (config.fonts && Array.isArray(config.fonts)) {
                        // Esperar a que las fuentes estén cargadas
                        const waitForFonts = setInterval(() => {
                            if ($('.font-checkbox').length > 0) {
                                clearInterval(waitForFonts);

                                // Deseleccionar todas las fuentes primero
                                $('.font-checkbox').prop('checked', false);

                                // Seleccionar solo las fuentes guardadas
                                config.fonts.forEach(font => {
                                    $(`.font-checkbox[value="${font}"]`).prop('checked', true);
                                });

                                updateFontCounter();
                                console.log('Fuentes cargadas:', config.fonts);
                            }
                        }, 100);
                    }

                    showAlert('success', '<i class="fas fa-check-circle me-2"></i>Configuración cargada exitosamente');
                })
                .fail(function() {
                    showAlert('danger', '<i class="fas fa-exclamation-triangle me-2"></i>Error al cargar la configuración');
                });
        }

        // Función para guardar configuración
        function saveConfig(filename) {
            console.log('Guardando configuración en:', filename);

            const config = {
                spreadsheet_id: $('#config_spreadsheet_id').val(),
                sheet_name: $('#config_sheet_name').val(),
                image_base_url: $('#config_image_base_url').val(),
                fonts: getSelectedFonts()
            };

            $.post('{{ route("config.save") }}', {
                filename: filename,
                config: config,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function() {
                showAlert('success', '<i class="fas fa-check-circle me-2"></i>Configuración guardada exitosamente');
                console.log('Configuración guardada:', config);
            })
            .fail(function() {
                showAlert('danger', '<i class="fas fa-exclamation-triangle me-2"></i>Error al guardar la configuración');
            });
        }

        // Función para mostrar alertas
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            $('#alertContainer').html(alertHtml);

            // Auto-ocultar después de 5 segundos para alertas de éxito
            if (type === 'success') {
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 5000);
            }
        }
    </script>

</body>
</html>
