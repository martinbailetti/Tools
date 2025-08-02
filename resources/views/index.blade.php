<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            background: rgba(0,0,0,0.5);
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
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
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

                            <div class="mb-3">
                                <label for="spreadsheetId" class="form-label">
                                    <i class="fab fa-google-drive me-1"></i>
                                    ID de Google Spreadsheet
                                </label>
                                <div class="input-icon-container">
                                    <input type="text" class="form-control" name="spreadsheetId" id="spreadsheetId" required
                                           value="1nYdFCcD5hLjPmz1xmddfNupjItjOI4riFzgpKX9Bq7k"
                                           placeholder="1nYdFCcD5hLjPmz1xmddfNupjItjOI4riFzgpKX9Bq7k">
                                    <i class="fas fa-table form-icon"></i>
                                </div>
                                <div class="form-text">El ID se encuentra en la URL de Google Sheets entre /d/ y /edit</div>
                            </div>

                            <div class="mb-3">
                                <label for="sheetName" class="form-label">
                                    <i class="fas fa-file-alt me-1"></i>
                                    Nombre de la Hoja
                                </label>
                                <div class="input-icon-container">
                                    <input type="text" class="form-control" name="sheetName" id="sheetName" required
                                           placeholder="Chino" value="Chino">
                                    <i class="fas fa-bookmark form-icon"></i>
                                </div>
                                <div class="form-text">El nombre exacto de la pestaña en Google Sheets</div>
                            </div>

                            <div class="mb-3">
                                <label for="numberOfPages" class="form-label">
                                    <i class="fas fa-sort-numeric-up me-1"></i>
                                    Número de Páginas (opcional)
                                </label>
                                <div class="input-icon-container">
                                    <input type="number" class="form-control" name="numberOfPages" id="numberOfPages" value="0" min="0"
                                           placeholder="0">
                                    <i class="fas fa-hashtag form-icon"></i>
                                </div>
                                <div class="form-text">Si es 0 o se deja vacío, se tomarán todas las páginas disponibles</div>
                            </div>

                            <div class="mb-3">
                                <label for="imagesURL" class="form-label">
                                    <i class="fas fa-link me-1"></i>
                                    URL Base de las Imágenes
                                </label>
                                <div class="input-icon-container">
                                    <input type="url" class="form-control" name="imagesURL" id="imagesURL" required
                                           value="https://printables.happycapibara.com/color-books/chinese/"
                                           placeholder="https://printables.happycapibara.com/color-books/chinese/">
                                    <i class="fas fa-images form-icon"></i>
                                </div>
                                <div class="form-text">URL base donde están alojadas las imágenes (debe terminar en /)</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg" id="generateBtn">
                                    <i class="fas fa-cogs me-2"></i>
                                    Generar PDF
                                </button>
                            </div>
                        </form>
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

    <script>
        $(document).ready(function() {
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
                    spreadsheetId: $('#spreadsheetId').val(),
                    sheetName: $('#sheetName').val(),
                    numberOfPages: $('#numberOfPages').val() || 0,
                    imagesURL: $('#imagesURL').val(),
                    _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                };

                // Realizar petición AJAX
                $.ajax({
                    url: '{{ route("pdf.generate") }}',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'blob' // Importante para manejar archivos binarios
                    },
                    success: function(data, status, xhr) {
                        // Crear enlace de descarga
                        const blob = new Blob([data], { type: 'application/pdf' });
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
                    },
                    error: function(xhr) {
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
                    },
                    complete: function() {
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

            // Validación en tiempo real de URL
            $('#imagesURL').on('blur', function() {
                const url = $(this).val();
                if (url && !url.endsWith('/')) {
                    $(this).val(url + '/');
                    showAlert('info', '<i class="fas fa-info-circle me-2"></i>Se añadió "/" al final de la URL automáticamente.');
                }
            });
        });
    </script>

</body>
</html>
