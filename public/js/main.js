// Variables globales para el manejo de fuentes
let availableFonts = [];
let fontsLoaded = false;
let editorsInitialized = false;
let quillEditors = {};

// Variables globales para el preview
let previewTimeout = null;
let previewUpdateDelay = 1000; // 1 segundo de delay para evitar muchas llamadas (reducido de 2)
let currentPreviewXHR = null; // Variable para almacenar la llamada AJAX actual del preview

// Función para obtener las páginas seleccionadas de los checkboxes
function getSelectedPages() {
    const selectedPages = [];
    $('input[name="includePages[]"]:checked').each(function() {
        selectedPages.push($(this).val());
    });
    return selectedPages;
}

// Función para obtener contenido de editores Quill (definida temprano para usar en updatePreview)
function getQuillContent(hiddenId) {
    const sourceElement = document.getElementById(hiddenId + "_source");
    const editor = quillEditors[hiddenId];

    let content = "";

    // Si el textarea de código está visible, usar su contenido
    if (sourceElement && sourceElement.style.display !== "none") {
        content = sourceElement.value;
    }
    // Si no, usar el contenido del editor Quill si existe
    else if (editor && editor.root) {
        content = editor.root.innerHTML;
    }
    // Fallback: usar el valor del input hidden
    else {
        const hiddenElement = document.getElementById(hiddenId);
        content = hiddenElement ? hiddenElement.value : "";
    }

    // Limpiar caracteres problemáticos
    if (content) {
        // Remover BOM (Byte Order Mark) y caracteres de espacio sin ancho
        content = content.replace(/\ufeff/g, ""); // BOM
        content = content.replace(/\u200b/g, ""); // Zero Width Space
        content = content.replace(/\u200c/g, ""); // Zero Width Non-Joiner
        content = content.replace(/\u200d/g, ""); // Zero Width Joiner
        content = content.replace(/\u2060/g, ""); // Word Joiner

        // Limpiar múltiples espacios en blanco consecutivos dentro del HTML pero preservar estructura
        content = content.replace(/\s{2,}/g, " ");

        // Limpiar espacios al inicio y final pero solo del contenido de texto, no del HTML
        content = content.trim();
    }

    return content;
}

// Función para limpiar caracteres problemáticos del contenido
function cleanContent(content) {
    if (!content) return content;

    // Remover BOM (Byte Order Mark) y caracteres de espacio sin ancho
    content = content.replace(/\ufeff/g, ""); // BOM
    content = content.replace(/\u200b/g, ""); // Zero Width Space
    content = content.replace(/\u200c/g, ""); // Zero Width Non-Joiner
    content = content.replace(/\u200d/g, ""); // Zero Width Joiner
    content = content.replace(/\u2060/g, ""); // Word Joiner

    return content;
}

// Función robusta para ocultar el loading del preview
function hidePreviewLoading() {
    console.log("Intentando ocultar loading preview...");

    const $loading = $("#previewLoading");

    // Múltiples métodos para asegurar que se oculte
    $loading.hide();
    $loading.css({
        display: "none",
        visibility: "hidden",
        opacity: "0",
    });
    $loading.addClass("force-hide");

    // Verificar que efectivamente se ocultó
    setTimeout(() => {
        if ($loading.is(":visible") || $loading.css("display") !== "none") {
            console.warn(
                "Loading aún visible, forzando ocultación adicional..."
            );
            $loading.attr(
                "style",
                "display: none !important; visibility: hidden !important; opacity: 0 !important;"
            );
            $loading[0].style.setProperty("display", "none", "important");
        } else {
            console.log("Loading ocultado exitosamente");
        }
    }, 100);
}

// Función para detectar cuando el PDF está realmente listo
function waitForPdfReady(iframe, callback, maxAttempts = 15) {
    let attempts = 0;

    function checkPdfStatus() {
        attempts++;
        console.log(
            `Verificando estado del PDF... intento ${attempts}/${maxAttempts}`
        );

        try {
            // Verificar si el iframe tiene el src correcto y es un PDF
            if (
                iframe.src &&
                iframe.src !== "about:blank" &&
                iframe.src.includes("preview-pdf")
            ) {
                // Para PDFs embebidos, verificar que el contenido esté disponible
                if (iframe.contentDocument || iframe.contentWindow) {
                    console.log("PDF parece estar listo");
                    callback();
                    return;
                }
            }
        } catch (error) {
            // Los errores de CORS son normales con PDFs embebidos
            console.log(
                "Error de acceso al contenido (normal para PDFs):",
                error.message
            );

            // Si hay error de acceso pero el src es correcto, asumir que está listo
            if (iframe.src && iframe.src.includes("preview-pdf")) {
                console.log("PDF con CORS - asumiendo que está listo");
                callback();
                return;
            }
        }

        // Si no está listo y no hemos alcanzado el máximo de intentos, reintentamos
        if (attempts < maxAttempts) {
            setTimeout(checkPdfStatus, 400); // Verificar cada 400ms
        } else {
            console.log("Máximo de intentos alcanzado - continuando");
            callback();
        }
    }

    // Iniciar verificación
    checkPdfStatus();
}

// Función para detectar cuando todas las imágenes del iframe hayan cargado
function waitForIframeImages(iframe, callback) {
    try {
        const iframeDoc =
            iframe.contentDocument || iframe.contentWindow.document;
        const images = iframeDoc.querySelectorAll("img");

        if (images.length === 0) {
            console.log(
                "No hay imágenes en el iframe, ejecutando callback inmediatamente"
            );
            callback();
            return;
        }

        let loadedImages = 0;
        const totalImages = images.length;
        console.log(
            `Esperando que carguen ${totalImages} imágenes en el iframe...`
        );

        const imageLoadHandler = () => {
            loadedImages++;
            console.log(`Imagen ${loadedImages}/${totalImages} cargada`);

            if (loadedImages === totalImages) {
                console.log("Todas las imágenes del iframe han cargado");
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
                    console.warn(
                        `Error cargando imagen ${index + 1}, continuando...`
                    );
                    imageLoadHandler(); // Contar como cargada aunque haya error
                };
            }
        });

        // Timeout de seguridad para imágenes
        setTimeout(() => {
            if (loadedImages < totalImages) {
                console.warn(
                    `Timeout: Solo ${loadedImages}/${totalImages} imágenes cargaron, continuando...`
                );
                callback();
            }
        }, 8000); // 8 segundos máximo para imágenes
    } catch (error) {
        console.warn("Error accediendo al contenido del iframe:", error);
        callback(); // Continuar aunque haya error
    }
}

// Función para actualizar el preview
function updatePreview(force = false) {
    console.log("=== updatePreview llamado ===");
    console.log("Force:", force);
    console.log("PreviewTimeout existe:", !!previewTimeout);

    // Cancelar timeout anterior si existe
    if (previewTimeout) {
        clearTimeout(previewTimeout);
        console.log("Timeout anterior cancelado");
    }

    // Si no es forzado, esperar un poco antes de actualizar
    if (!force) {
        console.log(`Programando preview en ${previewUpdateDelay}ms...`);
        previewTimeout = setTimeout(
            () => updatePreview(true),
            previewUpdateDelay
        );
        return;
    }

    console.log("Ejecutando actualización de preview...");

    // Mostrar loading y ocultar placeholder
    console.log("Mostrando loading preview...");
    const $loading = $("#previewLoading");

    // Remover clase force-hide y mostrar loading
    $loading.removeClass("force-hide");
    $loading
        .css({
            display: "flex",
            visibility: "visible",
            opacity: "1",
        })
        .show();

    // Obtener configuración del JSON seleccionado
    const selectedJsonFile = $("#jsonFileSelector").val() || "chinese";

    // Leer configuración desde el JSON para obtener parámetros básicos
    $.get("/book?token=" + selectedJsonFile)
        .done(function (response) {
            console.log("Configuración cargada para preview:", response);

            const config = response.config;
            // Obtener valores actuales del formulario y sobrescribir la configuración
            const updatedConfig = {
                ...config,
            }; // Copiar configuración base

            // Actualizar con valores del formulario si existen
            if ($("#config_width").length && $("#config_width").val()) {
                updatedConfig.width = parseFloat($("#config_width").val());
            }
            if ($("#config_height").length && $("#config_height").val()) {
                updatedConfig.height = parseFloat($("#config_height").val());
            }
            if ($("#config_margin_in").length && $("#config_margin_in").val()) {
                updatedConfig["margin-in"] = parseFloat(
                    $("#config_margin_in").val()
                );
            }
            if (
                $("#config_margin_out").length &&
                $("#config_margin_out").val()
            ) {
                updatedConfig["margin-out"] = parseFloat(
                    $("#config_margin_out").val()
                );
            }
            if (
                $("#config_spreadsheet_id").length &&
                $("#config_spreadsheet_id").val()
            ) {
                updatedConfig.spreadsheetId = $("#config_spreadsheet_id").val();
            }
            if (
                $("#config_spreadsheet_sheet_name").length &&
                $("#config_spreadsheet_sheet_name").val()
            ) {
                updatedConfig.spreadsheetSheetName = $(
                    "#config_spreadsheet_sheet_name"
                ).val();
            }
            if (
                $("#config_images_url").length &&
                $("#config_images_url").val()
            ) {
                updatedConfig.imagesURL = $("#config_images_url").val();
            }

            // Actualizar configuraciones de verso
            if (!updatedConfig.verso) updatedConfig.verso = {};
            if (
                $("#config_verso_margin").length &&
                $("#config_verso_margin").val()
            ) {
                updatedConfig.verso.margin = parseFloat(
                    $("#config_verso_margin").val()
                );
            }
            if (
                $("#config_verso_border_margin").length &&
                $("#config_verso_border_margin").val()
            ) {
                updatedConfig.verso["border-margin"] = parseFloat(
                    $("#config_verso_border_margin").val()
                );
            }
            if (
                $("#config_verso_image_margin").length &&
                $("#config_verso_image_margin").val()
            ) {
                updatedConfig.verso["image-margin"] = parseFloat(
                    $("#config_verso_image_margin").val()
                );
            }
            if (
                $("#config_verso_text_1_top").length &&
                $("#config_verso_text_1_top").val()
            ) {
                updatedConfig.verso["text-1-top"] = parseFloat(
                    $("#config_verso_text_1_top").val()
                );
            }
            if (
                $("#config_verso_text_2_top").length &&
                $("#config_verso_text_2_top").val()
            ) {
                updatedConfig.verso["text-2-top"] = parseFloat(
                    $("#config_verso_text_2_top").val()
                );
            }

            if (
                $("#config_verso_primary_font_size").length &&
                $("#config_verso_primary_font_size").val()
            ) {
                updatedConfig.verso["primary-font-size"] = parseFloat(
                    $("#config_verso_primary_font_size").val()
                );
            }
            if (
                $("#config_verso_secondary_font_size").length &&
                $("#config_verso_secondary_font_size").val()
            ) {
                updatedConfig.verso["secondary-font-size"] = parseFloat(
                    $("#config_verso_secondary_font_size").val()
                );
            }
            if (
                $("#config_verso_text_1_font_family").length &&
                $("#config_verso_text_1_font_family").val()
            ) {
                updatedConfig.verso["text-1-font-family"] = $(
                    "#config_verso_text_1_font_family"
                ).val();
            }
            if (
                $("#config_verso_text_2_font_family").length &&
                $("#config_verso_text_2_font_family").val()
            ) {
                updatedConfig.verso["text-2-font-family"] = $(
                    "#config_verso_text_2_font_family"
                ).val();
            }

            // Actualizar configuraciones de recto
            if (!updatedConfig.recto) updatedConfig.recto = {};
            if (
                $("#config_recto_margin").length &&
                $("#config_recto_margin").val()
            ) {
                updatedConfig.recto.margin = parseFloat(
                    $("#config_recto_margin").val()
                );
            }
            if (
                $("#config_recto_image_margin").length &&
                $("#config_recto_image_margin").val()
            ) {
                updatedConfig.recto["image-margin"] = parseFloat(
                    $("#config_recto_image_margin").val()
                );
            }
            if (
                $("#config_recto_font_size").length &&
                $("#config_recto_font_size").val()
            ) {
                updatedConfig.recto["font-size"] = parseFloat(
                    $("#config_recto_font_size").val()
                );
            }
            if (
                $("#config_recto_text_top").length &&
                $("#config_recto_text_top").val()
            ) {
                updatedConfig.recto["text-top"] = parseFloat(
                    $("#config_recto_text_top").val()
                );
            }
            if (
                $("#config_recto_text_font_family").length &&
                $("#config_recto_text_font_family").val()
            ) {
                updatedConfig.recto["text-font-family"] = $(
                    "#config_recto_text_font_family"
                ).val();
            }

            // Actualizar configuraciones de páginas específicas
            // Page 1
            if (!updatedConfig.page1) updatedConfig.page1 = {};
            const textContent = getQuillContent("config_page1_text");
            if (textContent) {
                updatedConfig.page1[
                    "text" + "_" + $("#languageSelector").val().toLowerCase()
                ] = textContent;
            }
            if (
                $("#config_page1_text_top").length &&
                $("#config_page1_text_top").val()
            ) {
                updatedConfig.page1["text-top"] = parseFloat(
                    $("#config_page1_text_top").val()
                );
            }
            if (
                $("#config_page1_text_margin_x").length &&
                $("#config_page1_text_margin_x").val()
            ) {
                updatedConfig.page1["text-margin-x"] = parseFloat(
                    $("#config_page1_text_margin_x").val()
                );
            }
            if (
                $("#config_page1_background_url").length &&
                $("#config_page1_background_url").val()
            ) {
                updatedConfig.page1["background-url"] = $(
                    "#config_page1_background_url"
                ).val();
            }
            if (
                $("#config_page1_image_url").length &&
                $("#config_page1_image_url").val()
            ) {
                updatedConfig.page1["image-url"] = $(
                    "#config_page1_image_url"
                ).val();
            }
            if (
                $("#config_page1_image_top").length &&
                $("#config_page1_image_top").val()
            ) {
                updatedConfig.page1["image-top"] = parseFloat(
                    $("#config_page1_image_top").val()
                );
            }
            if (
                $("#config_page1_image_height").length &&
                $("#config_page1_image_height").val()
            ) {
                updatedConfig.page1["image-height"] = parseFloat(
                    $("#config_page1_image_height").val()
                );
            }
            if (
                $("#config_page1_image_width").length &&
                $("#config_page1_image_width").val()
            ) {
                updatedConfig.page1["image-width"] = $(
                    "#config_page1_image_width"
                ).val();
            }

            // Page 2
            if (!updatedConfig.page2) updatedConfig.page2 = {};
            const page2TextContent = getQuillContent("config_page2_text");
            if (page2TextContent) {
                updatedConfig.page2[
                    "text" + "_" + $("#languageSelector").val().toLowerCase()
                ] = page2TextContent;
            }
            if (
                $("#config_page2_text_top").length &&
                $("#config_page2_text_top").val()
            ) {
                updatedConfig.page2["text-top"] = parseFloat(
                    $("#config_page2_text_top").val()
                );
            }
            if (
                $("#config_page2_text_margin_x").length &&
                $("#config_page2_text_margin_x").val()
            ) {
                updatedConfig.page2["text-margin-x"] = parseFloat(
                    $("#config_page2_text_margin_x").val()
                );
            }
            if (
                $("#config_page2_background_url").length &&
                $("#config_page2_background_url").val()
            ) {
                updatedConfig.page2["background-url"] = $(
                    "#config_page2_background_url"
                ).val();
            }
            if (
                $("#config_page2_image_url").length &&
                $("#config_page2_image_url").val()
            ) {
                updatedConfig.page2["image-url"] = $(
                    "#config_page2_image_url"
                ).val();
            }
            if (
                $("#config_page2_image_top").length &&
                $("#config_page2_image_top").val()
            ) {
                updatedConfig.page2["image-top"] = parseFloat(
                    $("#config_page2_image_top").val()
                );
            }
            if (
                $("#config_page2_image_height").length &&
                $("#config_page2_image_height").val()
            ) {
                updatedConfig.page2["image-height"] = parseFloat(
                    $("#config_page2_image_height").val()
                );
            }
            if (
                $("#config_page2_image_width").length &&
                $("#config_page2_image_width").val()
            ) {
                updatedConfig.page2["image-width"] = $(
                    "#config_page2_image_width"
                ).val();
            }

            // Page 3
            if (!updatedConfig.page3) updatedConfig.page3 = {};

            const page3TextContent = getQuillContent("config_page3_text");
            if (page3TextContent) {
                updatedConfig.page3[
                    "text" + "_" + $("#languageSelector").val().toLowerCase()
                ] = page3TextContent;
            }

            if (
                $("#config_page3_text_top").length &&
                $("#config_page3_text_top").val()
            ) {
                updatedConfig.page3["text-top"] = parseFloat(
                    $("#config_page3_text_top").val()
                );
            }
            if (
                $("#config_page3_text_margin_x").length &&
                $("#config_page3_text_margin_x").val()
            ) {
                updatedConfig.page3["text-margin-x"] = parseFloat(
                    $("#config_page3_text_margin_x").val()
                );
            }
            if (
                $("#config_page3_background_url").length &&
                $("#config_page3_background_url").val()
            ) {
                updatedConfig.page3["background-url"] = $(
                    "#config_page3_background_url"
                ).val();
            }
            if (
                $("#config_page3_image_url").length &&
                $("#config_page3_image_url").val()
            ) {
                updatedConfig.page3["image-url"] = $(
                    "#config_page3_image_url"
                ).val();
            }
            if (
                $("#config_page3_image_top").length &&
                $("#config_page3_image_top").val()
            ) {
                updatedConfig.page3["image-top"] = parseFloat(
                    $("#config_page3_image_top").val()
                );
            }
            if (
                $("#config_page3_image_height").length &&
                $("#config_page3_image_height").val()
            ) {
                updatedConfig.page3["image-height"] = parseFloat(
                    $("#config_page3_image_height").val()
                );
            }
            if (
                $("#config_page3_image_width").length &&
                $("#config_page3_image_width").val()
            ) {
                updatedConfig.page3["image-width"] = $(
                    "#config_page3_image_width"
                ).val();
            }

            // Page 4
            if (!updatedConfig.page4) updatedConfig.page4 = {};

            const page4TextContent = getQuillContent("config_page4_text");
            if (page4TextContent) {
                updatedConfig.page4[
                    "text" + "_" + $("#languageSelector").val().toLowerCase()
                ] = page4TextContent;
            }

            if (
                $("#config_page4_text_top").length &&
                $("#config_page4_text_top").val()
            ) {
                updatedConfig.page4["text-top"] = parseFloat(
                    $("#config_page4_text_top").val()
                );
            }
            if (
                $("#config_page4_text_margin_x").length &&
                $("#config_page4_text_margin_x").val()
            ) {
                updatedConfig.page4["text-margin-x"] = parseFloat(
                    $("#config_page4_text_margin_x").val()
                );
            }
            if (
                $("#config_page4_background_url").length &&
                $("#config_page4_background_url").val()
            ) {
                updatedConfig.page4["background-url"] = $(
                    "#config_page4_background_url"
                ).val();
            }
            if (
                $("#config_page4_image_url").length &&
                $("#config_page4_image_url").val()
            ) {
                updatedConfig.page4["image-url"] = $(
                    "#config_page4_image_url"
                ).val();
            }
            if (
                $("#config_page4_image_top").length &&
                $("#config_page4_image_top").val()
            ) {
                updatedConfig.page4["image-top"] = parseFloat(
                    $("#config_page4_image_top").val()
                );
            }
            if (
                $("#config_page4_image_height").length &&
                $("#config_page4_image_height").val()
            ) {
                updatedConfig.page4["image-height"] = parseFloat(
                    $("#config_page4_image_height").val()
                );
            }
            if (
                $("#config_page4_image_width").length &&
                $("#config_page4_image_width").val()
            ) {
                updatedConfig.page4["image-width"] = $(
                    "#config_page4_image_width"
                ).val();
            }

            // Page 5
            if (!updatedConfig.page5) updatedConfig.page5 = {};

            const page5TextContent = getQuillContent("config_page5_text");
            if (page5TextContent) {
                updatedConfig.page5[
                    "text" + "_" + $("#languageSelector").val().toLowerCase()
                ] = page5TextContent;
            }

            if (
                $("#config_page5_text_top").length &&
                $("#config_page5_text_top").val()
            ) {
                updatedConfig.page5["text-top"] = parseFloat(
                    $("#config_page5_text_top").val()
                );
            }
            if (
                $("#config_page5_text_margin_x").length &&
                $("#config_page5_text_margin_x").val()
            ) {
                updatedConfig.page5["text-margin-x"] = parseFloat(
                    $("#config_page5_text_margin_x").val()
                );
            }
            if (
                $("#config_page5_background_url").length &&
                $("#config_page5_background_url").val()
            ) {
                updatedConfig.page5["background-url"] = $(
                    "#config_page5_background_url"
                ).val();
            }
            if (
                $("#config_page5_image_url").length &&
                $("#config_page5_image_url").val()
            ) {
                updatedConfig.page5["image-url"] = $(
                    "#config_page5_image_url"
                ).val();
            }
            if (
                $("#config_page5_image_top").length &&
                $("#config_page5_image_top").val()
            ) {
                updatedConfig.page5["image-top"] = parseFloat(
                    $("#config_page5_image_top").val()
                );
            }
            if (
                $("#config_page5_image_height").length &&
                $("#config_page5_image_height").val()
            ) {
                updatedConfig.page5["image-height"] = parseFloat(
                    $("#config_page5_image_height").val()
                );
            }
            if (
                $("#config_page5_image_width").length &&
                $("#config_page5_image_width").val()
            ) {
                updatedConfig.page5["image-width"] = $(
                    "#config_page5_image_width"
                ).val();
            }

            console.log(
                "Configuración actualizada con valores del formulario:",
                updatedConfig
            );
            console.log("Page 1 configuración:", updatedConfig.page1);
            console.log("Page 2 configuración:", updatedConfig.page2);
            console.log("Page 3 configuración:", updatedConfig.page3);
            console.log("Page 4 configuración:", updatedConfig.page4);
            console.log("Page 5 configuración:", updatedConfig.page5);

            // Preparar datos para el preview usando el método getPreview
            const previewData = {
                numberOfPages: $("#numberOfPages").val() || 5, // Limitar a 5 páginas para preview
                spreadsheetId: updatedConfig.spreadsheetId,
                sheetName: updatedConfig.spreadsheetSheetName,
                imagesURL: updatedConfig.imagesURL,
                languageSelector: $("#languageSelector").val() || "ES",
                preview_pages: getSelectedPages(), // Agregar páginas seleccionadas
                layout: updatedConfig, // Enviar la configuración actualizada con valores del formulario
                _token:
                    $('meta[name="csrf-token"]').attr("content") ||
                    $('input[name="_token"]').val(),
            };

            console.log("Datos para preview:", previewData);

            // Construir URL para preview PDF con parámetros
            const urlParams = new URLSearchParams();

            // Agregar parámetros básicos
            urlParams.append("numberOfPages", previewData.numberOfPages);
            if (previewData.spreadsheetId)
                urlParams.append("spreadsheetId", previewData.spreadsheetId);
            if (previewData.sheetName)
                urlParams.append("sheetName", previewData.sheetName);
            if (previewData.imagesURL)
                urlParams.append("imagesURL", previewData.imagesURL);
            if (previewData.languageSelector)
                urlParams.append(
                    "languageSelector",
                    previewData.languageSelector
                );
            if (previewData._token)
                urlParams.append("_token", previewData._token);

            // Agregar fuentes seleccionadas
            if (
                previewData.selectedFonts &&
                previewData.selectedFonts.length > 0
            ) {
                previewData.selectedFonts.forEach((font) => {
                    urlParams.append("selectedFonts[]", font);
                });
            }

            // Agregar configuración como JSON
            if (previewData.layout) {
                urlParams.append("layout", JSON.stringify(previewData.layout));
            }

            // Construir URL final para PDF preview con parámetros de visualización
            // Add cache busting parameter to prevent loading from cache
            urlParams.append("_t", Date.now().toString());

            // Preparar datos para llamada POST

            // Hacer llamada POST al nuevo endpoint que genera PDF temporal
            console.log(
                "Llamando al endpoint POST para generar PDF temporal..."
            );

            // Mostrar botón cancelar cuando inicie la llamada AJAX
            $("#cancelPreviewBtn").show();

            currentPreviewXHR = $.ajax({
                url: "/preview-pdf",
                type: "POST",
                data: previewData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    currentPreviewXHR = null; // Limpiar la referencia XHR
                    $("#cancelPreviewBtn").hide(); // Ocultar botón cancelar
                    if (response.success && response.pdf_url) {
                        console.log("PDF temporal generado:", response.pdf_url);

                        // Cargar el PDF en el iframe con parámetros de visualización
                        const pdfUrl =
                            response.pdf_url +
                            "#toolbar=0&navpanes=0&view=FitH&zoom=page-width";
                        console.log("URL de preview PDF:", pdfUrl);

                        const iframe = document.getElementById("previewFrame");

                        // Configurar listeners para detectar carga exitosa del PDF
                        iframe.onload = function () {
                            console.log(
                                "Iframe onload evento disparado - verificando estado del PDF..."
                            );
                            waitForPdfReady(iframe, function () {
                                console.log("PDF está listo en el iframe");
                                hidePreviewLoading();
                            });
                        };

                        iframe.onerror = function () {
                            console.log("Error cargando PDF en iframe");
                            hidePreviewLoading();
                        };

                        // Cargar PDF en iframe
                        console.log("Cargando PDF generado en iframe...");
                        iframe.src = pdfUrl;
                    } else {
                        throw new Error(
                            response.error || "Error desconocido generando PDF"
                        );
                    }
                },
                error: function (xhr, status, error) {
                    currentPreviewXHR = null; // Limpiar la referencia XHR
                    $("#cancelPreviewBtn").hide(); // Ocultar botón cancelar
                    console.error("Error generando PDF temporal:", error);
                    let errorMessage = "Error generando vista previa";

                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }

                    // Mostrar error en iframe
                    const iframe = document.getElementById("previewFrame");
                    const errorUrl =
                        "data:text/html;charset=utf-8," +
                        encodeURIComponent(`
                                <html>
                                    <body style="font-family: Arial, sans-serif; padding: 20px; text-align: center; color: #666;">
                                        <div style="border: 2px dashed #dc3545; padding: 40px; border-radius: 10px;">
                                            <i style="font-size: 48px; color: #dc3545;">⚠️</i>
                                            <h3 style="color: #dc3545; margin: 20px 0;">Error generando PDF</h3>
                                            <p>${errorMessage}</p>
                                        </div>
                                    </body>
                                </html>
                            `);

                    iframe.onload = function () {
                        console.log("Iframe de error cargado");
                        hidePreviewLoading();
                    };

                    iframe.src = errorUrl;

                    setTimeout(function () {
                        hidePreviewLoading();
                    }, 1000);
                },
            });
            // toolbar=0: oculta toolbar superior
            // navpanes=0: oculta panel de navegación lateral
            // scrollbar=0: oculta scrollbar (opcional)
            // view=FitH: ajusta horizontalmente
            // zoom=page-width: ajusta al ancho de página

            // Cargar PDF directamente en el iframe
            const iframe = document.getElementById("previewFrame");

            // Limpiar listeners previos
            iframe.onload = null;
            iframe.onerror = null;

            // Configurar listeners para detectar carga exitosa del PDF
            let loadingTimerPdf = null;
            let loadingHidden = false;

            function hideLoadingOnce() {
                if (!loadingHidden) {
                    console.log("PDF cargado - ocultando loading");
                    hidePreviewLoading();
                    loadingHidden = true;
                    if (loadingTimerPdf) {
                        clearTimeout(loadingTimerPdf);
                        loadingTimerPdf = null;
                    }
                }
            }

            // Estrategia principal: usar la función de detección inteligente
            iframe.onload = function () {
                console.log(
                    "Iframe onload evento disparado - verificando estado del PDF..."
                );

                // Usar función avanzada para detectar cuando el PDF está realmente listo
                waitForPdfReady(iframe, function () {
                    console.log("PDF está listo en el iframe");
                    // Solo ocultar loading cuando el PDF esté realmente listo
                    hideLoadingOnce();
                });
            };

            // Evento de error
            iframe.onerror = function () {
                console.log("Error cargando PDF en iframe");
                hideLoadingOnce();
            };

            // Cargar PDF directamente en iframe
        })
        .fail(function (xhr, status, error) {
            console.error("Error cargando configuración JSON:", error);

            // En caso de error de configuración, mostrar mensaje directamente en iframe
            const iframe = document.getElementById("previewFrame");
            const errorUrl =
                "data:text/html;charset=utf-8," +
                encodeURIComponent(`
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

            iframe.onload = function () {
                console.log("Iframe de error de configuración cargado");
                hidePreviewLoading();
            };

            iframe.src = errorUrl;

            setTimeout(function () {
                console.log(
                    "Timeout de error de configuración: ocultando loading"
                );
                hidePreviewLoading();
            }, 1000);
        });
}

// Función para refrescar preview manualmente
function refreshPreview() {
    console.log("Refresh preview manual");
    updatePreview(true);
}

// Función para abrir preview en nueva pestaña
function openPreviewInNewTab() {
    const iframe = document.getElementById("previewFrame");
    if (iframe.src && iframe.src !== "about:blank") {
        window.open(iframe.src, "_blank");
    } else {
        alert("No hay preview disponible para abrir.");
    }
}

// Función para cargar fuentes desde el servidor
function loadAvailableFonts(
    shouldUpdateSelector = false,
    selectedFonts = null
) {
    console.log("Cargando fuentes disponibles desde el servidor...");

    return $.ajax({
        url: "/generate/fonts",
        type: "GET",
        success: function (fonts) {
            console.log("Fuentes cargadas desde servidor:", fonts);
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

                console.log("Proceso de carga de fuentes completado");
            }, 200);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar fuentes:", error);
            console.log("Usando fuentes por defecto debido al error");

            // Usar fuentes por defecto
            availableFonts = [
                {
                    displayName: "Sans Serif",
                    cssName: "sans-serif",
                    familyName: "sans-serif",
                },
                {
                    displayName: "Serif",
                    cssName: "serif",
                    familyName: "serif",
                },
                {
                    displayName: "Monospace",
                    cssName: "monospace",
                    familyName: "monospace",
                },
            ];

            fontsLoaded = true;

            // Poblar selectores de fuentes en el formulario con fuentes por defecto
            populateFontSelectors();
        },
    });
}

// Función para poblar los selectores de fuentes en el formulario
function populateFontSelectors() {
    console.log("Poblando selectores de fuentes...");

    const fontSelectors = [
        "config_verso_text_1_font_family",
        "config_verso_text_2_font_family",
        "config_recto_text_font_family",
    ];

    fontSelectors.forEach((selectorId) => {
        const $selector = $("#" + selectorId);
        if ($selector.length) {
            // Limpiar opciones existentes excepto la primera
            $selector.find("option:not(:first)").remove();

            // Añadir opciones de fuentes
            availableFonts.forEach((font) => {
                $selector.append(
                    `<option value="${font.cssName}">${font.displayName}</option>`
                );
            });

            console.log(
                `Selector ${selectorId} poblado con ${availableFonts.length} fuentes`
            );
        }
    });
}



// Función mejorada para establecer valores de fuentes desde configuración
function setFontSelectorValuesImproved(config) {
    console.log(
        "Estableciendo valores de selectores de fuentes (versión mejorada)..."
    );
    console.log("Config recibido:", config);
    console.log("Fuentes disponibles:", availableFonts);

    // Función helper para encontrar fuente compatible
    function findCompatibleFont(fontValue) {
        if (!fontValue) return "";

        console.log(`Buscando fuente para: ${fontValue}`);

        // Buscar por coincidencia exacta en cssName
        let found = availableFonts.find((font) => font.cssName === fontValue);
        if (found) {
            console.log(`Encontrada por cssName: ${found.cssName}`);
            return found.cssName;
        }

        // Buscar por coincidencia exacta en filename (sin extensión)
        found = availableFonts.find(
            (font) =>
                font.filename.replace(/\.(ttf|otf|woff|woff2)$/i, "") ===
                fontValue
        );
        if (found) {
            console.log(
                `Encontrada por filename: ${found.filename} -> ${found.cssName}`
            );
            return found.cssName;
        }

        // Buscar por coincidencia parcial en displayName
        found = availableFonts.find(
            (font) =>
                font.displayName
                    .toLowerCase()
                    .includes(fontValue.toLowerCase()) ||
                fontValue.toLowerCase().includes(font.displayName.toLowerCase())
        );
        if (found) {
            console.log(
                `Encontrada por displayName: ${found.displayName} -> ${found.cssName}`
            );
            return found.cssName;
        }

        console.warn(`No se encontró fuente compatible para: ${fontValue}`);
        return "";
    }

    // Esperar a que los selectores estén poblados
    setTimeout(() => {
        if (config.verso) {
            const text1Font = findCompatibleFont(
                config.verso["text-1-font-family"]
            );
            const text2Font = findCompatibleFont(
                config.verso["text-2-font-family"]
            );

            const $selector1 = $("#config_verso_text_1_font_family");
            const $selector2 = $("#config_verso_text_2_font_family");

            if ($selector1.length) {
                $selector1.val(text1Font);
                console.log(
                    `Verso text-1 font: ${
                        config.verso["text-1-font-family"]
                    } -> ${text1Font} (selector value: ${$selector1.val()})`
                );
            }

            if ($selector2.length) {
                $selector2.val(text2Font);
                console.log(
                    `Verso text-2 font: ${
                        config.verso["text-2-font-family"]
                    } -> ${text2Font} (selector value: ${$selector2.val()})`
                );
            }
        }

        if (config.recto) {
            const textFont = findCompatibleFont(
                config.recto["text-font-family"]
            );
            const $selectorRecto = $("#config_recto_text_font_family");

            if ($selectorRecto.length) {
                $selectorRecto.val(textFont);
                console.log(
                    `Recto text font: ${
                        config.recto["text-font-family"]
                    } -> ${textFont} (selector value: ${$selectorRecto.val()})`
                );
            }
        }

        console.log("Valores de fuentes establecidos completados");
    }, 200);
} // Función moderna para cargar fuentes usando FontFace API
function loadFontsWithFontFaceAPI(fonts) {
    console.log("Cargando fuentes con FontFace API...");

    if (!("FontFace" in window)) {
        console.log("FontFace API no soportada, usando método tradicional");
        return;
    }

    const loadPromises = [];

    fonts.forEach(function (font) {
        try {
            // Crear FontFace dinámicamente
            const fontFace = new FontFace(
                font.familyName,
                `url('/fonts/${font.filename}')`,
                {
                    style: "normal",
                    weight: "normal",
                    display: "swap", // Mejor rendimiento
                }
            );

            // Cargar la fuente
            const loadPromise = fontFace
                .load()
                .then(function (loadedFont) {
                    // Añadir al document fonts
                    document.fonts.add(loadedFont);
                    console.log(`Fuente cargada: ${font.familyName}`);
                    return loadedFont;
                })
                .catch(function (error) {
                    console.warn(
                        `Error cargando fuente ${font.familyName}:`,
                        error
                    );
                });

            loadPromises.push(loadPromise);
        } catch (error) {
            console.warn(
                `Error creando FontFace para ${font.familyName}:`,
                error
            );
        }
    });

    // Esperar a que todas las fuentes se carguen
    Promise.allSettled(loadPromises).then(function (results) {
        const successCount = results.filter(
            (r) => r.status === "fulfilled"
        ).length;
        console.log(
            `FontFace API: ${successCount}/${fonts.length} fuentes cargadas exitosamente`
        );

        // Forzar re-render de elementos que usan fuentes
        document.fonts.ready.then(function () {
            console.log("Todas las fuentes están listas");
            // Trigger re-render si es necesario
            document.body.style.fontDisplay = "swap";
        });
    });
}

// Función para crear CSS dinámico para las fuentes
function createDynamicFontCSS(fonts) {
    console.log("Creando CSS dinámico para", fonts.length, "fuentes");
    let css = "";

    fonts.forEach(function (font) {
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
                    .ql-picker.ql-font .ql-picker-item[data-value="${
                        font.cssName
                    }"]::before,
                    .ql-picker.ql-font .ql-picker-label[data-value="${
                        font.cssName
                    }"]::before {
                        content: '${font.displayName}';
                        font-family: '${font.familyName}', serif;
                    }

                    /* Mostrar nombre en el dropdown */
                    .ql-picker.ql-font .ql-picker-item[data-value="${
                        font.cssName
                    }"] {
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
    let styleElement = document.getElementById("dynamic-fonts");
    if (!styleElement) {
        console.error(
            "Elemento dynamic-fonts no encontrado, creando uno nuevo"
        );
        styleElement = document.createElement("style");
        styleElement.id = "dynamic-fonts";
        styleElement.type = "text/css";
        document.head.appendChild(styleElement);
    }

    // Inyectar CSS en el documento
    styleElement.innerHTML = css;

    // Forzar recalculación de estilos
    document.body.offsetHeight; // Trigger reflow

    console.log("CSS de fuentes creado exitosamente");
    console.log(
        "Fuentes CSS creadas:",
        fonts.map((f) => f.cssName)
    );

    // Verificar que los estilos se aplicaron
    setTimeout(function () {
        if (fonts.length > 0) {
            const testElement = document.createElement("div");
            testElement.className = `ql-font-${fonts[0].cssName.toLowerCase()}`;
            document.body.appendChild(testElement);
            const computedStyle = window.getComputedStyle(testElement);
            console.log("Test de estilo aplicado:", computedStyle.fontFamily);
            document.body.removeChild(testElement);
        }
    }, 100);
}

// Función para registrar fuentes en Quill
function registerQuillFonts(fonts) {
    try {
        const Font = Quill.import("formats/font");
        const fontNames = [""].concat(fonts.map((font) => font.cssName)); // Añadir opción por defecto
        Font.whitelist = fontNames;
        Quill.register(Font, true);
        console.log("Fuentes registradas en Quill:", fontNames);
        console.log(
            "Fuentes disponibles:",
            fonts.map((f) => `${f.cssName} -> ${f.displayName}`)
        );

        // Refrescar editores existentes si ya están inicializados
        refreshQuillFontOptions(fontNames);
    } catch (error) {
        console.error("Error al registrar fuentes en Quill:", error);
    }
}

// Función para refrescar las opciones de fuente en editores existentes
function refreshQuillFontOptions(fontNames) {
    Object.keys(quillEditors).forEach((editorKey) => {
        const editor = quillEditors[editorKey];
        if (editor && editor.getModule) {
            try {
                const toolbar = editor.getModule("toolbar");
                if (toolbar && toolbar.container) {
                    // Buscar el selector de fuentes
                    const fontPicker =
                        toolbar.container.querySelector(".ql-font");
                    if (fontPicker) {
                        console.log(
                            `Refrescando opciones de fuente para editor: ${editorKey}`
                        );

                        // Forzar reconstrucción del picker
                        const picker = fontPicker.__quill_picker;
                        if (picker) {
                            picker.buildItems();
                        }
                    }
                }
            } catch (error) {
                console.warn(
                    `Error refrescando fuentes para editor ${editorKey}:`,
                    error
                );
            }
        }
    });
}

// Función para obtener las fuentes seleccionadas (ya no necesaria, retorna array vacío)
function getSelectedFonts() {
    return [];
}

$(document).ready(function () {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Aplicar automáticamente tooltips a todos los labels que no los tengan
    $(".form-label").each(function () {
        var $label = $(this);

        // Si ya tiene tooltip, saltar
        if ($label.attr("data-bs-toggle")) {
            return;
        }

        // Obtener el texto del label (sin iconos)
        var labelText = $label.text().trim();

        // Si tiene texto, agregar tooltip y clase
        if (labelText) {
            $label.addClass("form-label-tooltip");
            $label.attr("data-bs-toggle", "tooltip");
            $label.attr("title", labelText);

            // Inicializar el tooltip inmediatamente
            new bootstrap.Tooltip($label[0]);
        }
    });

    // Las fuentes se cargarán automáticamente en initializeSystem()
    $("#pdfGeneratorForm").on("submit", function (e) {
        e.preventDefault();

        // Mostrar loading overlay
        $("#loadingOverlay").fadeIn();

        // Cambiar estado del botón
        const $btn = $("#generateBtn");
        const originalBtnText = $btn.html();
        $btn.prop("disabled", true).html(
            '<i class="fas fa-spinner fa-spin me-2"></i>Generando...'
        );

        // Limpiar alertas anteriores
        $("#alertContainer").empty();

        // Preparar datos del formulario (incluir numberOfPages del formulario)
        const formData = {
            numberOfPages: $("#numberOfPages").val() || 1,
            selectedJsonFile: $("#jsonFileSelector").val(),
            languageSelector: $("#languageSelector").val() || "ES",
            includePages: getSelectedPages(), // Agregar páginas seleccionadas
            _token:
                $('meta[name="csrf-token"]').attr("content") ||
                $('input[name="_token"]').val(),
        };

        // Realizar petición AJAX
        $.ajax({
            url: '/generate',
            type: "POST",
            data: formData,
            xhrFields: {
                responseType: "blob", // Importante para manejar archivos binarios
            },
            success: function (data, status, xhr) {
                // Crear enlace de descarga
                const blob = new Blob([data], {
                    type: "application/pdf",
                });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.href = url;

                // Obtener nombre del archivo desde headers o usar uno por defecto
                const contentDisposition = xhr.getResponseHeader(
                    "Content-Disposition"
                );
                let filename = "color-book.pdf";
                if (contentDisposition) {
                    const matches = /filename="([^"]*)"/.exec(
                        contentDisposition
                    );
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
                showAlert(
                    "success",
                    '<i class="fas fa-check-circle me-2"></i>PDF generado exitosamente!'
                );
            },
            error: function (xhr) {
                let errorMessage = "Error al generar el PDF";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 0) {
                    errorMessage =
                        "Error de conexión. Verifica tu conexión a internet.";
                } else if (xhr.status >= 500) {
                    errorMessage = "Error del servidor. Inténtalo más tarde.";
                } else if (xhr.status === 422) {
                    errorMessage =
                        "Datos del formulario inválidos. Verifica la información.";
                }

                showAlert(
                    "danger",
                    '<i class="fas fa-exclamation-triangle me-2"></i>' +
                        errorMessage
                );
            },
            complete: function () {
                // Ocultar loading overlay
                $("#loadingOverlay").fadeOut();

                // Restaurar botón
                $btn.prop("disabled", false).html(originalBtnText);
            },
        });
    });

    function showAlert(type, message) {
        const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
        $("#alertContainer").html(alertHtml);

        // Auto-hide success alerts after 5 seconds
        if (type === "success") {
            setTimeout(() => {
                $(".alert-success").fadeOut();
            }, 5000);
        }
    }

    // Event listeners para el preview
    $("#refreshPreviewBtn").on("click", function () {
        refreshPreview();
    });

    $("#openPreviewNewTab").on("click", function () {
        openPreviewInNewTab();
    });

    // Auto-load configuration se manejará después de inicializar editores

    // Función para extraer fuentes de la configuración JSON (similar al PHP)
    function extractFontsFromConfig(config) {
        const fonts = [];

        function extractFromValue(value) {
            if (typeof value === "string") {
                // Buscar patrones de clases ql-font- en el texto
                const matches = value.match(/ql-font-([a-zA-Z0-9_-]+)/g);
                if (matches) {
                    matches.forEach((match) => {
                        const fontName = match.replace("ql-font-", "");
                        if (fontName && !fonts.includes(fontName)) {
                            fonts.push(fontName);
                        }
                    });
                }
            } else if (Array.isArray(value)) {
                value.forEach((item) => extractFromValue(item));
            } else if (typeof value === "object" && value !== null) {
                Object.values(value).forEach((item) => extractFromValue(item));
            }
        }

        extractFromValue(config);
        return fonts.sort();
    }

    // Función para obtener el texto apropiado según el idioma seleccionado
    function getTextForLanguage(pageConfig, languageCode) {
        console.log("getTextForLanguage llamado con:", {
            pageConfig,
            languageCode,
        });

        if (!pageConfig) {
            return "";
        }

        // Convertir código de idioma a minúsculas para búsqueda
        const langKey = languageCode.toLowerCase();

        // Primera prioridad: texto específico del idioma (text_es, text_en, etc.)
        const languageTextKey = `text_${langKey}`;
        if (
            pageConfig[languageTextKey] &&
            pageConfig[languageTextKey].trim() !== ""
        ) {
            console.log(
                `Usando texto específico del idioma: ${languageTextKey}`
            );
            return pageConfig[languageTextKey];
        }

        // Segunda prioridad: texto genérico (text)
        if (pageConfig["text"] && pageConfig["text"].trim() !== "") {
            console.log("Usando texto genérico");
            return pageConfig["text"];
        }

        // Si no hay ningún texto disponible
        console.log("No se encontró texto para el idioma", languageCode);
        return "";
    }

    // Función para recargar todos los textos cuando cambia el idioma
    function reloadTextsForLanguage(languageCode) {
        console.log("Recargando textos para idioma:", languageCode);

        const selectedJsonFile = $("#jsonFileSelector").val() || "chinese";

        $.get("/book?token=" + selectedJsonFile)
            .done(function (response) {
                console.log(
                    "Configuración cargada para cambio de idioma:",
                    response
                );
                const config = response.config;
                // Recargar texto de cada página
                for (let pageNum = 1; pageNum <= 5; pageNum++) {
                    const pageKey = `page${pageNum}`;
                    if (config[pageKey]) {
                        const pageText = getTextForLanguage(
                            config[pageKey],
                            languageCode
                        );
                        if (pageText) {
                            setTimeout(function () {
                                setQuillContent(
                                    `config_${pageKey}_text`,
                                    pageText
                                );
                                console.log(
                                    `Texto actualizado para ${pageKey} en idioma ${languageCode}`
                                );
                            }, pageNum * 50); // Delay escalonado
                        }
                    }
                }
            })
            .fail(function () {
                console.error(
                    "Error recargando configuración para cambio de idioma"
                );
            });
    }

    // Función para cargar configuración desde archivo seleccionado
    function loadSelectedConfig() {
        console.log("loadSelectedConfig iniciado");

        // Asegurar que los editores estén inicializados
        if (!editorsInitialized) {
            console.log("Editores no inicializados, esperando...");
            setTimeout(loadSelectedConfig, 500);
            return;
        }

        const selectedJsonFile = $("#jsonFileSelector").val() || "chinese";
        console.log("Cargando configuración desde:", selectedJsonFile);

        $.get("/book?token=" + selectedJsonFile)
            .done(function (response) {
                console.log("Configuración cargada:", response);

                const config = response.config;
                // General
                $("#config_width").val(config.width);
                $("#config_height").val(config.height);
                $("#config_margin_in").val(config["margin-in"]);
                $("#config_margin_out").val(config["margin-out"]);
                $("#config_spreadsheet_id").val(config.spreadsheetId);
                $("#config_spreadsheet_sheet_name").val(
                    config.spreadsheetSheetName
                );
                $("#config_images_url").val(config.imagesURL);

                // Verso
                if (config.verso) {
                    $("#config_verso_margin").val(config.verso.margin);
                    $("#config_verso_border_margin").val(
                        config.verso["border-margin"]
                    );
                    $("#config_verso_image_margin").val(
                        config.verso["image-margin"]
                    );
                    $("#config_verso_text_1_top").val(
                        config.verso["text-1-top"]
                    );
                    $("#config_verso_text_2_top").val(
                        config.verso["text-2-top"]
                    );
                    $("#config_verso_primary_font_size").val(
                        config.verso["primary-font-size"]
                    );
                    $("#config_verso_secondary_font_size").val(
                        config.verso["secondary-font-size"]
                    );
                    // Valores de fuentes se establecerán después de cargar las fuentes
                }

                // Recto
                if (config.recto) {
                    $("#config_recto_margin").val(config.recto.margin);
                    $("#config_recto_image_margin").val(
                        config.recto["image-margin"]
                    );
                    $("#config_recto_font_size").val(config.recto["font-size"]);
                    $("#config_recto_text_top").val(config.recto["text-top"]);
                    // Valor de fuente se establecerá después de cargar las fuentes
                }

                // Page 1
                if (config.page1) {
                    $("#config_page1_text_top").val(config.page1["text-top"]);
                    $("#config_page1_text_margin_x").val(
                        config.page1["text-margin-x"] || 0
                    );
                    $("#config_page1_background_url").val(
                        config.page1["background-url"] || ""
                    );
                    $("#config_page1_image_url").val(config.page1["image-url"]);
                    $("#config_page1_image_top").val(config.page1["image-top"]);
                    $("#config_page1_image_height").val(
                        config.page1["image-height"]
                    );
                    $("#config_page1_image_width").val(
                        config.page1["image-width"] || "auto"
                    );

                    // Cargar textos en editores Quill con delay para asegurar que están listos
                    setTimeout(function () {
                        console.log("Cargando contenido de página 1...");
                        const pageText = getTextForLanguage(
                            config.page1,
                            $("#languageSelector").val() || "ES"
                        );
                        if (pageText) {
                            setQuillContent("config_page1_text", pageText);
                        } else {
                            setQuillContent("config_page1_text", "El secreto");
                        }
                    }, 100);
                }

                // Page 2
                if (config.page2) {
                    $("#config_page2_text_top").val(config.page2["text-top"]);
                    $("#config_page2_text_margin_x").val(
                        config.page2["text-margin-x"] || 0.5
                    );
                    $("#config_page2_background_url").val(
                        config.page2["background-url"] || ""
                    );
                    $("#config_page2_image_url").val(
                        config.page2["image-url"] || ""
                    );
                    $("#config_page2_image_top").val(
                        config.page2["image-top"] || 0
                    );
                    $("#config_page2_image_height").val(
                        config.page2["image-height"] || 0
                    );
                    $("#config_page2_image_width").val(
                        config.page2["image-width"] || "auto"
                    );
                    $("#config_page2_text_top").val(config.page2["text-top"]);

                    // Cargar textos en editores Quill con delay para asegurar que están listos
                    setTimeout(function () {
                        console.log("Cargando contenido de página 2...");
                        const pageText = getTextForLanguage(
                            config.page2,
                            $("#languageSelector").val() || "ES"
                        );
                        if (pageText) {
                            setQuillContent("config_page2_text", pageText);
                        }
                    }, 200);
                }

                // Page 3
                if (config.page3) {
                    $("#config_page3_text_top").val(config.page3["text-top"]);
                    $("#config_page3_text_margin_x").val(
                        config.page3["text-margin-x"] || 0
                    );
                    $("#config_page3_background_url").val(
                        config.page3["background-url"] || ""
                    );
                    $("#config_page3_image_url").val(
                        config.page3["image-url"] || ""
                    );
                    $("#config_page3_image_top").val(
                        config.page3["image-top"] || 0
                    );
                    $("#config_page3_image_height").val(
                        config.page3["image-height"] || 0
                    );
                    $("#config_page3_image_width").val(
                        config.page3["image-width"] || "auto"
                    );
                    $("#config_page3_text_top").val(config.page3["text-top"]);

                    // Cargar textos en editores Quill con delay para asegurar que están listos
                    setTimeout(function () {
                        console.log("Cargando contenido de página 3...");
                        const pageText = getTextForLanguage(
                            config.page3,
                            $("#languageSelector").val() || "ES"
                        );
                        if (pageText) {
                            setQuillContent("config_page3_text", pageText);
                        }
                    }, 300);
                }

                // Page 4
                if (config.page4) {
                    $("#config_page4_text_top").val(
                        config.page4["text-top"] || 0
                    );
                    $("#config_page4_text_margin_x").val(
                        config.page4["text-margin-x"] || 0
                    );
                    $("#config_page4_background_url").val(
                        config.page4["background-url"] || ""
                    );
                    $("#config_page4_image_url").val(
                        config.page4["image-url"] || ""
                    );
                    $("#config_page4_image_top").val(
                        config.page4["image-top"] || 0
                    );
                    $("#config_page4_image_height").val(
                        config.page4["image-height"] || 0
                    );
                    $("#config_page4_image_width").val(
                        config.page4["image-width"] || "auto"
                    );

                    // Cargar textos en editores Quill con delay para asegurar que están listos
                    setTimeout(function () {
                        console.log("Cargando contenido de página 4...");
                        const pageText = getTextForLanguage(
                            config.page4,
                            $("#languageSelector").val() || "ES"
                        );
                        if (pageText) {
                            setQuillContent("config_page4_text", pageText);
                        }
                    }, 350);
                }

                // Page 5
                if (config.page5) {
                    $("#config_page5_text_top").val(config.page5["text-top"]);
                    $("#config_page5_text_margin_x").val(
                        config.page5["text-margin-x"] || 0
                    );
                    $("#config_page5_background_url").val(
                        config.page5["background-url"] || ""
                    );
                    $("#config_page5_image_url").val(
                        config.page5["image-url"] || ""
                    );
                    $("#config_page5_image_top").val(
                        config.page5["image-top"] || 0
                    );
                    $("#config_page5_image_height").val(
                        config.page5["image-height"] || 0
                    );
                    $("#config_page5_image_width").val(
                        config.page5["image-width"] || "auto"
                    );

                    // Cargar textos en editores Quill con delay para asegurar que están listos
                    setTimeout(function () {
                        console.log("Cargando contenido de página 5...");
                        const pageText = getTextForLanguage(
                            config.page5,
                            $("#languageSelector").val() || "ES"
                        );
                        if (pageText) {
                            setQuillContent("config_page5_text", pageText);
                        }
                    }, 400);
                }

                // Cargar fuentes disponibles después de cargar configuración
                loadAvailableFonts(false).then(function () {
                    console.log(
                        "Fuentes cargadas después de aplicar configuración"
                    );

                    // Establecer valores de selectores de fuentes después de poblarlos
                    setTimeout(function () {
                        setFontSelectorValuesImproved(config);
                    }, 100);
                });

                showAlert(
                    "success",
                    '<i class="fas fa-check-circle me-2"></i>Configuración cargada correctamente'
                );

                // Reconfigurar listeners de cambio después de cargar configuración
                setTimeout(function () {
                    if (
                        typeof window.setupQuillChangeListeners === "function"
                    ) {
                        window.setupQuillChangeListeners();
                        console.log(
                            "Listeners de cambio reconfigurados después de cargar configuración"
                        );
                    }
                }, 500);

                // Actualizar preview después de cargar configuración
                setTimeout(function () {
                    updatePreview(true);
                }, 1000);
            })
            .fail(function () {
                showAlert(
                    "danger",
                    '<i class="fas fa-exclamation-triangle me-2"></i>Error al cargar la configuración'
                );
            });
    }

    // Evento para el botón de cargar configuración
    $("#loadConfigBtn").click(function () {
        loadSelectedConfig();
    });

    // Evento para cuando cambia el selector de archivo JSON
    $("#jsonFileSelector").change(function () {
        loadSelectedConfig();
    });

    // Evento para cuando cambia el selector de idioma
    $("#languageSelector").change(function () {
        const selectedLanguage = $(this).val();
        console.log("Cambio de idioma detectado:", selectedLanguage);

        // Recargar los textos para el nuevo idioma
        reloadTextsForLanguage(selectedLanguage);

        // Actualizar el preview después de cambiar idioma
        setTimeout(function () {
            updatePreview(true);
        }, 500);
    });

    // Save configuration
    $("#saveConfigBtn").click(function () {
        if (
            !confirm(
                "¿Estás seguro de que quieres guardar la configuración actual? Esto sobrescribirá el archivo JSON."
            )
        ) {
            return;
        }

        // Recopilar todos los datos del accordion
        const configData = {
            // Archivo seleccionado
            // Idioma seleccionado para el guardado específico
            language_selector: $("#languageSelector").val() || "ES",
            selected_json_file: $("#jsonFileSelector").val(),

            // General
            width: $("#config_width").val(),
            height: $("#config_height").val(),
            margin_in: $("#config_margin_in").val(),
            margin_out: $("#config_margin_out").val(),
            spreadsheet_id: $("#config_spreadsheet_id").val(),
            spreadsheet_sheet_name: $("#config_spreadsheet_sheet_name").val(),
            images_url: $("#config_images_url").val(),

            // Verso
            verso_margin: $("#config_verso_margin").val(),
            verso_border_margin: $("#config_verso_border_margin").val(),
            verso_image_margin: $("#config_verso_image_margin").val(),
            verso_text_1_top: $("#config_verso_text_1_top").val(),
            verso_text_2_top: $("#config_verso_text_2_top").val(),
            verso_primary_font_size: $("#config_verso_primary_font_size").val(),
            verso_secondary_font_size: $(
                "#config_verso_secondary_font_size"
            ).val(),
            verso_text_1_font_family: $(
                "#config_verso_text_1_font_family"
            ).val(),
            verso_text_2_font_family: $(
                "#config_verso_text_2_font_family"
            ).val(),

            // Recto
            recto_margin: $("#config_recto_margin").val(),
            recto_image_margin: $("#config_recto_image_margin").val(),
            recto_font_size: $("#config_recto_font_size").val(),
            recto_text_top: $("#config_recto_text_top").val(),
            recto_text_font_family: $("#config_recto_text_font_family").val(),

            // Page 1
            page1_text: getQuillContent("config_page1_text"),
            page1_text_top: $("#config_page1_text_top").val(),
            page1_text_margin_x: $("#config_page1_text_margin_x").val(),
            page1_background_url: $("#config_page1_background_url").val(),
            page1_image_url: $("#config_page1_image_url").val(),
            page1_image_top: $("#config_page1_image_top").val(),
            page1_image_height: $("#config_page1_image_height").val(),
            page1_image_width: $("#config_page1_image_width").val(),

            // Page 2
            page2_text: getQuillContent("config_page2_text"),
            page2_text_top: $("#config_page2_text_top").val(),
            page2_text_margin_x: $("#config_page2_text_margin_x").val(),
            page2_background_url: $("#config_page2_background_url").val(),
            page2_image_url: $("#config_page2_image_url").val(),
            page2_image_top: $("#config_page2_image_top").val(),
            page2_image_height: $("#config_page2_image_height").val(),
            page2_image_width: $("#config_page2_image_width").val(),

            // Page 3
            page3_text: getQuillContent("config_page3_text"),
            page3_text_top: $("#config_page3_text_top").val(),
            page3_text_margin_x: $("#config_page3_text_margin_x").val(),
            page3_background_url: $("#config_page3_background_url").val(),
            page3_image_url: $("#config_page3_image_url").val(),
            page3_image_top: $("#config_page3_image_top").val(),
            page3_image_height: $("#config_page3_image_height").val(),
            page3_image_width: $("#config_page3_image_width").val(),

            // Page 4
            page4_text: getQuillContent("config_page4_text"),
            page4_text_top: $("#config_page4_text_top").val(),
            page4_text_margin_x: $("#config_page4_text_margin_x").val(),
            page4_background_url: $("#config_page4_background_url").val(),
            page4_image_url: $("#config_page4_image_url").val(),
            page4_image_top: $("#config_page4_image_top").val(),
            page4_image_height: $("#config_page4_image_height").val(),
            page4_image_width: $("#config_page4_image_width").val(),

            // Page 5
            page5_text: getQuillContent("config_page5_text"),
            page5_text_top: $("#config_page5_text_top").val(),
            page5_text_margin_x: $("#config_page5_text_margin_x").val(),
            page5_background_url: $("#config_page5_background_url").val(),
            page5_image_url: $("#config_page5_image_url").val(),
            page5_image_top: $("#config_page5_image_top").val(),
            page5_image_height: $("#config_page5_image_height").val(),
            page5_image_width: $("#config_page5_image_width").val(),
        };

        // Mostrar indicador de carga
        const originalText = $("#saveConfigBtn").html();
        $("#saveConfigBtn")
            .html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...')
            .prop("disabled", true);

        // Enviar datos al servidor
        $.ajax({
            url: "/save-config",
            method: "POST",
            data: configData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    showAlert(
                        "success",
                        '<i class="fas fa-check-circle me-2"></i>Configuración guardada exitosamente'
                    );
                    // Recargar preview automáticamente después de guardar
                    console.log(
                        "Recargando preview automáticamente después de guardar configuración"
                    );
                    updatePreview(true);
                } else {
                    showAlert(
                        "danger",
                        '<i class="fas fa-exclamation-triangle me-2"></i>Error: ' +
                            response.message
                    );
                }
            },
            error: function (xhr) {
                let errorMessage = "Error al guardar la configuración";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(
                    "danger",
                    '<i class="fas fa-exclamation-triangle me-2"></i>' +
                        errorMessage
                );
            },
            complete: function () {
                // Restaurar botón
                $("#saveConfigBtn").html(originalText).prop("disabled", false);
            },
        });
    });

    // Preview configuration button
    $("#previewConfigBtn").click(function () {
        console.log("Preview manual solicitado");
        updatePreview(true); // Forzar actualización inmediata del preview
    });

    // Event handler para cancelar preview
    $("#cancelPreviewBtn").on("click", function () {
        if (currentPreviewXHR) {
            console.log("Cancelando llamada de preview...");
            currentPreviewXHR.abort(); // Cancelar la llamada AJAX
            currentPreviewXHR = null; // Limpiar la referencia

            // Ocultar botón cancelar y loading
            $("#cancelPreviewBtn").hide();
            hidePreviewLoading();

            // Mostrar mensaje de cancelación en el iframe
            const iframe = document.getElementById("previewFrame");
            const cancelUrl = "data:text/html;charset=utf-8," + encodeURIComponent(`
                <html>
                    <body style="font-family: Arial, sans-serif; padding: 20px; text-align: center; color: #666;">
                        <div style="border: 2px dashed #ffc107; padding: 40px; border-radius: 10px;">
                            <i style="font-size: 48px; color: #ffc107;">⚠️</i>
                            <h3 style="color: #ffc107; margin: 20px 0;">Preview cancelado</h3>
                            <p>La generación del preview fue cancelada por el usuario.</p>
                        </div>
                    </body>
                </html>
            `);
            iframe.src = cancelUrl;
        }
    });

    // Función para cargar CSS de fuentes dinámico
    function loadDynamicFontCSS() {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.type = "text/css";
        link.href = "/fonts.css";
        document.head.appendChild(link);
        console.log("CSS de fuentes dinámico cargado");
    }

    // Función para agregar control de tamaño personalizado a un editor
    function addCustomSizeControl(quill, editorId) {
        const toolbar = quill.getModule("toolbar");
        const container = toolbar.container;

        // Variable para guardar la selección
        let savedRange = null;

        // Crear elemento personalizado para tamaño
        const sizeGroup = document.createElement("span");
        sizeGroup.className = "ql-formats";

        const sizeLabel = document.createElement("label");
        sizeLabel.textContent = "Tamaño: ";

        const sizeInput = document.createElement("input");
        sizeInput.type = "text";
        sizeInput.placeholder = "1.2cm";
        sizeInput.title = "Introduce el tamaño (ej: 1.2cm)";

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
                    quill.formatText(
                        currentRange.index,
                        currentRange.length,
                        "size",
                        size
                    );
                    console.log(
                        "Tamaño aplicado a selección:",
                        size,
                        currentRange
                    );
                } else {
                    // Aplicar al cursor (próximo texto que se escriba)
                    quill.format("size", size);
                    console.log("Tamaño aplicado al cursor:", size);
                }

                // Limpiar la selección guardada
                savedRange = null;
            }
        }

        // Guardar selección cuando el input obtiene foco
        sizeInput.addEventListener("mousedown", function (e) {
            // Guardar la selección actual antes de que se pierda
            savedRange = quill.getSelection();
            console.log("Selección guardada:", savedRange);
        });

        sizeInput.addEventListener("focus", function (e) {
            // También guardar cuando obtiene foco por teclado
            if (!savedRange) {
                savedRange = quill.getSelection();
                console.log("Selección guardada en focus:", savedRange);
            }
        });

        // Event listener para aplicar el tamaño al presionar Enter
        sizeInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                applySize();
                sizeInput.blur(); // Quitar foco del input
            }
        });

        // Event listener para aplicar el tamaño al perder foco
        sizeInput.addEventListener("blur", function () {
            // Pequeño delay para permitir que se complete la acción
            setTimeout(applySize, 50);
        });

        // Mostrar tamaño actual cuando se selecciona texto
        quill.on("selection-change", function (range) {
            // Solo actualizar si el input no tiene foco
            if (range && document.activeElement !== sizeInput) {
                const format = quill.getFormat(range);
                if (format.size) {
                    sizeInput.value = format.size;
                } else {
                    sizeInput.value = "";
                }
            }
        });

        console.log("Control de tamaño personalizado agregado a:", editorId);
    }

    // Función para agregar control de line-height personalizado a un editor
    function addCustomLineHeightControl(quill, editorId) {
        const toolbar = quill.getModule("toolbar");
        const container = toolbar.container;

        // Variable para guardar la selección
        let savedRange = null;

        // Crear elemento personalizado para line-height
        const lineHeightGroup = document.createElement("span");
        lineHeightGroup.className = "ql-formats";

        const lineHeightLabel = document.createElement("label");
        lineHeightLabel.textContent = "Interlineado: ";

        const lineHeightInput = document.createElement("input");
        lineHeightInput.type = "text";
        lineHeightInput.placeholder = "1.2cm";
        lineHeightInput.title = "Introduce el interlineado (ej: 1.2cm)";

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
                    quill.formatText(
                        currentRange.index,
                        currentRange.length,
                        "line-height",
                        lineHeight
                    );
                    console.log(
                        "Line-height aplicado a selección:",
                        lineHeight,
                        currentRange
                    );
                } else {
                    // Aplicar al cursor (próximo texto que se escriba)
                    quill.format("line-height", lineHeight);
                    console.log("Line-height aplicado al cursor:", lineHeight);
                }

                // Limpiar la selección guardada
                savedRange = null;
            }
        }

        // Guardar selección cuando el input obtiene foco
        lineHeightInput.addEventListener("mousedown", function (e) {
            // Guardar la selección actual antes de que se pierda
            savedRange = quill.getSelection();
            console.log("Selección guardada para line-height:", savedRange);
        });

        lineHeightInput.addEventListener("focus", function (e) {
            // También guardar cuando obtiene foco por teclado
            if (!savedRange) {
                savedRange = quill.getSelection();
                console.log(
                    "Selección guardada en focus para line-height:",
                    savedRange
                );
            }
        });

        // Event listener para aplicar el line-height al presionar Enter
        lineHeightInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                applyLineHeight();
                lineHeightInput.blur(); // Quitar foco del input
            }
        });

        // Event listener para aplicar el line-height al perder foco
        lineHeightInput.addEventListener("blur", function () {
            // Pequeño delay para permitir que se complete la acción
            setTimeout(applyLineHeight, 50);
        });

        // Mostrar line-height actual cuando se selecciona texto
        quill.on("selection-change", function (range) {
            // Solo actualizar si el input no tiene foco
            if (range && document.activeElement !== lineHeightInput) {
                const format = quill.getFormat(range);
                if (format["line-height"]) {
                    lineHeightInput.value = format["line-height"];
                } else {
                    lineHeightInput.value = "";
                }
            }
        });

        console.log(
            "Control de line-height personalizado agregado a:",
            editorId
        );
    }

    // Función para agregar botón de pantalla completa a un editor
    function addFullscreenButton(quill, editorId) {
        const toolbar = quill.getModule("toolbar");
        const container = toolbar.container;

        // Crear elemento personalizado para fullscreen
        const fullscreenGroup = document.createElement("span");
        fullscreenGroup.className = "ql-formats";

        const fullscreenBtn = document.createElement("button");
        fullscreenBtn.type = "button";
        fullscreenBtn.className = "ql-fullscreen";
        fullscreenBtn.title = "Alternar pantalla completa";
        fullscreenBtn.innerHTML = "⛶"; // Icono de expand

        fullscreenGroup.appendChild(fullscreenBtn);
        container.appendChild(fullscreenGroup);

        // Manejar click del botón
        fullscreenBtn.addEventListener("click", function (e) {
            e.preventDefault();
            toggleFullscreen(quill, editorId, fullscreenBtn);
        });

        console.log("Botón de pantalla completa agregado a:", editorId);
    }

    // Función para alternar pantalla completa
    function toggleFullscreen(quill, editorId, button) {
        const editorContainer = document
            .getElementById(editorId)
            .closest(".ql-container").parentElement;
        const isFullscreen =
            editorContainer.classList.contains("quill-fullscreen");

        if (isFullscreen) {
            // Salir de pantalla completa
            editorContainer.classList.remove("quill-fullscreen");
            button.innerHTML = "⛶"; // Icono expand
            button.title = "Alternar pantalla completa";
            document.body.style.overflow = "";

            // Remover listener de ESC
            document.removeEventListener(
                "keydown",
                window.currentFullscreenEscListener
            );
        } else {
            // Entrar en pantalla completa
            editorContainer.classList.add("quill-fullscreen");
            button.innerHTML = "⛗"; // Icono compress
            button.title = "Salir de pantalla completa";
            document.body.style.overflow = "hidden";

            // Agregar listener para ESC
            window.currentFullscreenEscListener = function (e) {
                if (e.key === "Escape") {
                    toggleFullscreen(quill, editorId, button);
                }
            };
            document.addEventListener(
                "keydown",
                window.currentFullscreenEscListener
            );
        }

        // Trigger resize para que Quill se ajuste al nuevo tamaño
        setTimeout(() => {
            quill.focus();
        }, 100);
    }

    // Inicializar editores Quill.js
    function initializeQuillEditors() {
        console.log("Inicializando editores Quill...");

        // Verificar que Quill esté disponible
        if (typeof Quill === "undefined") {
            console.error("Quill.js no está cargado. Reintentando...");
            setTimeout(initializeQuillEditors, 500);
            return;
        }

        // Verificar que las fuentes estén cargadas
        if (!fontsLoaded) {
            console.log("Esperando a que se carguen las fuentes...");
            setTimeout(initializeQuillEditors, 500);
            return;
        }

        // Registrar formato de tamaño personalizado que permite cualquier valor
        const SizeStyle = Quill.import("attributors/style/size");
        SizeStyle.whitelist = null; // Permitir cualquier valor
        Quill.register(SizeStyle, true);
        console.log("Formato de tamaño personalizado registrado");

        // Crear y registrar formato de line-height personalizado
        const Parchment = Quill.import("parchment");
        const LineHeightStyle = new Parchment.Attributor.Style(
            "line-height",
            "line-height",
            {
                scope: Parchment.Scope.INLINE,
                whitelist: null, // Permitir cualquier valor
            }
        );
        Quill.register(LineHeightStyle, true);
        console.log("Formato de line-height personalizado registrado");

        console.log("Quill.js detectado correctamente");
        const editorConfigs = [
            // Página 1
            {
                id: "config_page1_text_editor",
                hiddenId: "config_page1_text",
            },
            // Página 2
            {
                id: "config_page2_text_editor",
                hiddenId: "config_page2_text",
            },
            // Página 3
            {
                id: "config_page3_text_editor",
                hiddenId: "config_page3_text",
            },
            // Página 4
            {
                id: "config_page4_text_editor",
                hiddenId: "config_page4_text",
            },
            // Página 5
            {
                id: "config_page5_text_editor",
                hiddenId: "config_page5_text",
            },
        ];

        editorConfigs.forEach((config) => {
            const element = document.getElementById(config.id);
            if (element) {
                console.log("Inicializando editor:", config.id);

                const quill = new Quill("#" + config.id, {
                    theme: "snow",
                    modules: {
                        toolbar: [
                            [
                                {
                                    font: [""].concat(
                                        availableFonts.map((f) => f.cssName)
                                    ),
                                },
                            ],
                            [
                                {
                                    color: [],
                                },
                                {
                                    background: [],
                                },
                            ],
                            [
                                {
                                    list: "ordered",
                                },
                                {
                                    list: "bullet",
                                },
                            ],
                            [
                                {
                                    align: [],
                                },
                            ],
                            ["clean"],
                        ],
                    },
                });

                // Guardar referencia del editor
                quillEditors[config.hiddenId] = quill;
                console.log("Editor guardado para:", config.hiddenId);

                // Agregar control de tamaño personalizado
                addCustomSizeControl(quill, config.id);

                // Agregar control de line-height personalizado
                addCustomLineHeightControl(quill, config.id);

                // Agregar botón de pantalla completa
                addFullscreenButton(quill, config.id);

                // Sincronizar con input hidden cuando cambie el contenido
                quill.on("text-change", function (delta, oldDelta, source) {
                    let content = quill.root.innerHTML;

                    // Limpiar caracteres problemáticos
                    content = cleanContent(content);

                    const sourceElement = document.getElementById(
                        config.hiddenId + "_source"
                    );

                    // Actualizar input hidden con contenido limpio
                    document.getElementById(config.hiddenId).value = content;

                    // Si el textarea de código está visible, actualizarlo también
                    if (
                        sourceElement &&
                        sourceElement.style.display !== "none"
                    ) {
                        sourceElement.value = content;
                    }

                    // Generar preview automáticamente cuando el usuario hace cambios - DESHABILITADO
                    if (source === "user") {
                        console.log(
                            "Cambio en editor Quill (usuario) - preview manual solamente:",
                            config.hiddenId
                        );
                        // updatePreview();  // Deshabilitado
                    }
                });

                // Añadir event listener para el textarea de código
                const sourceElement = document.getElementById(
                    config.hiddenId + "_source"
                );
                if (sourceElement) {
                    sourceElement.addEventListener("input", function () {
                        // Actualizar input hidden cuando cambie el código
                        document.getElementById(config.hiddenId).value =
                            sourceElement.value;

                        // Generar preview cuando se edita código fuente - DESHABILITADO
                        console.log(
                            "Cambio en código fuente - preview manual solamente:",
                            config.hiddenId
                        );
                        // updatePreview();  // Deshabilitado
                    });
                }
            } else {
                console.warn("Elemento no encontrado:", config.id);
            }
        });

        editorsInitialized = true;
        console.log(
            "Editores inicializados. Total:",
            Object.keys(quillEditors).length
        );

        // Configurar listeners para detectar cambios en editores Quill
        if (typeof window.setupQuillChangeListeners === "function") {
            window.setupQuillChangeListeners();
            console.log("Listeners de cambio de Quill configurados");
        }
    }

    // Función para establecer contenido en editor Quill
    function setQuillContent(hiddenId, content) {
        console.log(
            "setQuillContent llamado para:",
            hiddenId,
            "con contenido:",
            content
        );

        const editor = quillEditors[hiddenId];
        if (editor && content) {
            try {
                // Limpiar el editor primero
                editor.setText("");

                // Usar clipboard para insertar HTML de forma segura
                editor.clipboard.dangerouslyPasteHTML(0, content);

                // Actualizar input hidden
                document.getElementById(hiddenId).value = content;

                // Actualizar textarea de código si existe
                const sourceElement = document.getElementById(
                    hiddenId + "_source"
                );
                if (sourceElement) {
                    sourceElement.value = content;
                }

                console.log(
                    "Contenido establecido exitosamente para:",
                    hiddenId
                );
            } catch (error) {
                console.error(
                    "Error al establecer contenido en",
                    hiddenId,
                    ":",
                    error
                );
                // Fallback: usar innerHTML directamente
                editor.root.innerHTML = content || "";
                document.getElementById(hiddenId).value = content || "";
            }
        } else {
            console.warn(
                "Editor no encontrado o contenido vacío para:",
                hiddenId
            );
        }
    }

    // Función para alternar entre vista visual y código HTML
    function toggleSourceView(hiddenId) {
        const editorElement = document.getElementById(hiddenId + "_editor");
        const sourceElement = document.getElementById(hiddenId + "_source");
        const buttonElement = event.target.closest("button");
        const editor = quillEditors[hiddenId];

        if (!editor || !editorElement || !sourceElement || !buttonElement) {
            console.error("Elementos no encontrados para:", hiddenId);
            return;
        }

        // Comprobar si está en modo código
        const isShowingSource = sourceElement.style.display !== "none";

        if (isShowingSource) {
            // Cambiar de código a visual
            const htmlContent = sourceElement.value;
            editor.clipboard.dangerouslyPasteHTML(htmlContent);

            // Mostrar editor, ocultar textarea
            editorElement.style.display = "block";
            sourceElement.style.display = "none";

            // Cambiar texto del botón
            buttonElement.innerHTML = '<i class="fas fa-code"></i> Ver código';
            buttonElement.classList.remove("btn-warning");
            buttonElement.classList.add("btn-outline-secondary");
        } else {
            // Cambiar de visual a código
            const htmlContent = editor.root.innerHTML;
            sourceElement.value = htmlContent;

            // Ocultar editor, mostrar textarea
            editorElement.style.display = "none";
            sourceElement.style.display = "block";

            // Cambiar texto del botón
            buttonElement.innerHTML = '<i class="fas fa-eye"></i> Ver visual';
            buttonElement.classList.remove("btn-outline-secondary");
            buttonElement.classList.add("btn-warning");
        }

        // Sincronizar con input hidden
        const currentContent = isShowingSource
            ? sourceElement.value
            : editor.root.innerHTML;
        document.getElementById(hiddenId).value = currentContent;
    }

    // Hacer la función global para que pueda ser llamada desde el HTML
    window.toggleSourceView = toggleSourceView;

    // Función para inicializar todo el sistema
    function initializeSystem() {
        console.log("Iniciando sistema...");

        // Verificar dependencias
        if (typeof Quill === "undefined") {
            console.error("Quill.js no cargado, reintentando en 500ms...");
            setTimeout(initializeSystem, 500);
            return;
        }

        if (typeof $ === "undefined") {
            console.error("jQuery no cargado, reintentando en 500ms...");
            setTimeout(initializeSystem, 500);
            return;
        }

        console.log("Dependencias verificadas, cargando fuentes...");

        // Cargar fuentes primero (sin actualizar selector automáticamente)
        loadAvailableFonts(false).then(function () {
            console.log("Fuentes cargadas, inicializando editores...");

            // Inicializar editores después de cargar fuentes
            setTimeout(function () {
                initializeQuillEditors();

                // Cargar configuración después de que los editores estén listos
                setTimeout(function () {
                    if (editorsInitialized) {
                        console.log("Cargando configuración inicial...");
                        $("#loadConfigBtn").click();

                        // Configurar listeners adicionales después de que todo esté inicializado - DESHABILITADO
                        setTimeout(function () {
                            console.log(
                                "Listeners adicionales para preview automático DESHABILITADOS"
                            );

                            // Preview inicial deshabilitado - solo manual
                            console.log(
                                "Preview inicial DESHABILITADO - usar botón refresh para generar preview"
                            );
                            // updatePreview(true);  // Deshabilitado
                        }, 1000);
                    } else {
                        console.log(
                            "Editores no inicializados, reintentando carga de configuración..."
                        );
                        setTimeout(() => $("#loadConfigBtn").click(), 500);
                    }
                }, 500);
            }, 200);
        });
    }

    // Inicializar sistema después de que el DOM esté listo
    setTimeout(initializeSystem, 200);
});
