# FontHelper - Procesador de Fuentes para Vistas Laravel

## Descripción

El `FontHelper` es una utilidad que procesa HTML en vistas Blade para añadir automáticamente estilos `font-family` a elementos que tienen clases con prefijo `ql-font-`.

## Funcionalidades

### 1. Directiva Blade `@processFonts`

Para uso general en vistas web:

```blade
@processFonts('<span class="ql-font-NotoSerifRegular">Texto con fuente</span>')
```

### 2. Directiva Blade `@processFontsForPdf`

Optimizada para generación de PDFs con DomPDF:

```blade
@processFontsForPdf($layout['page5']['text-1-content'])
```

## Ejemplos de Uso

### Ejemplo 1: HTML Simple
```html
<!-- Entrada -->
<span class="ql-font-NotoSerifRegular">Este es un texto</span>

<!-- Salida -->
<span class="ql-font-NotoSerifRegular" style="font-family: 'NotoSerifRegular'">Este es un texto</span>
```

### Ejemplo 2: HTML con Estilos Existentes
```html
<!-- Entrada -->
<span class="ql-font-ZenAntiqueRegular" style="color: red;">美</span>

<!-- Salida -->
<span class="ql-font-ZenAntiqueRegular" style="color: red; font-family: 'ZenAntiqueRegular'">美</span>
```

### Ejemplo 3: HTML Mezclado
```html
<!-- Entrada -->
<p><span class="ql-font-NotoSerifRegular">palabra</span><span class="ql-font-ZenAntiqueRegular">美</span></p>

<!-- Salida -->
<p><span class="ql-font-NotoSerifRegular" style="font-family: 'NotoSerifRegular'">palabra</span><span class="ql-font-ZenAntiqueRegular" style="font-family: 'ZenAntiqueRegular'">美</span></p>
```

## Uso en colorbook_margin.blade.php

Ya implementado en los siguientes lugares:

- `$layout['page2']['text']`
- `$layout['page5']['header-text']`
- `$layout['page5']['text-1-content']`
- `$layout['page5']['text-2-content']`
- `$layout['page5']['text-3-content']`
- `$layout['page5']['footer-text']`

## Método Directo

También puedes usar el helper directamente en PHP:

```php
use App\Helpers\FontHelper;

$html = '<span class="ql-font-NotoSerifRegular">Texto</span>';
$processed = FontHelper::addFontFamilyStylesForPdf($html);
```

## Comportamiento

- **Solo procesa elementos con clases `ql-font-*`**: Ignora otros elementos
- **Preserva estilos existentes**: Añade font-family sin eliminar otros estilos
- **Mapea a fuentes reales**: Usa FontManager para mapear nombres CSS a nombres de fuentes
- **Optimizado para DomPDF**: La versión `ForPdf` maneja mejor las peculiaridades de DomPDF

## Fuentes Soportadas

La función automáticamente mapea todas las fuentes disponibles en la carpeta `public/fonts/`:

- `ql-font-NotoSerifRegular` → `font-family: 'NotoSerifRegular'`
- `ql-font-ZenAntiqueRegular` → `font-family: 'ZenAntiqueRegular'`
- `ql-font-NotoSerifSCBold` → `font-family: 'NotoSerifSCBold'`
- etc.
