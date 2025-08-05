# Componente Styled-Div

## Descripción
El componente `styled-div` es un componente reutilizable de Blade que permite crear elementos `<div>` con estilos CSS dinámicos basados en parámetros.

## Ubicación
`resources/views/components/styled-div.blade.php`

## Uso Básico

```blade
<x-styled-div 
    width="10cm" 
    height="5cm" 
    backgroundColor="red" 
    position="absolute">
    Contenido del div
</x-styled-div>
```

## Parámetros Disponibles

### Propiedades Básicas
- `width` - Ancho del elemento (ej: "10cm", "100px", "50%")
- `height` - Alto del elemento (ej: "5cm", "200px", "100%")
- `fontSize` - Tamaño de la fuente (ej: "1.5cm", "16px")
- `lineHeight` - Altura de línea (ej: "1.5cm", "20px")
- `color` - Color del texto (ej: "white", "#ffffff", "rgb(255,255,255)")
- `fontFamily` - Familia de fuente (ej: "'Arial'", "'NotoSerif'")

### Propiedades de Fondo
- `backgroundColor` - Color de fondo (ej: "black", "#000000")
- `backgroundImage` - Imagen de fondo (ej: "imagen.jpg")
- `backgroundSize` - Tamaño de la imagen de fondo (ej: "cover", "contain")
- `backgroundPosition` - Posición de la imagen de fondo (ej: "center", "top left")

### Propiedades de Posicionamiento
- `position` - Tipo de posicionamiento (ej: "absolute", "relative", "fixed")
- `top` - Posición desde arriba (ej: "10cm", "50px")
- `left` - Posición desde la izquierda (ej: "5cm", "100px")
- `margin` - Margen (ej: "0cm 0cm", "10px 20px")
- `padding` - Relleno interno (ej: "1cm", "10px 20px")

### Propiedades de Texto
- `textAlign` - Alineación del texto (ej: "center", "left", "right")

### Propiedades Adicionales
- `overflow` - Manejo del desbordamiento (ej: "hidden", "scroll")
- `opacity` - Transparencia (ej: "0.8", "1")
- `transform` - Transformaciones CSS (ej: "scale(1.5)", "rotate(45deg)")
- `zIndex` - Índice de apilamiento (ej: "10", "999")

## Ejemplos de Uso

### Ejemplo 1: Contenedor Básico
```blade
<x-styled-div 
    width="21cm" 
    height="29.7cm" 
    backgroundColor="white" 
    position="relative" 
    margin="0cm">
    Contenido de la página
</x-styled-div>
```

### Ejemplo 2: Texto con Imagen de Fondo
```blade
<x-styled-div 
    width="15cm" 
    height="10cm" 
    backgroundImage="fondo.jpg" 
    backgroundSize="cover" 
    backgroundPosition="center" 
    fontSize="2cm" 
    color="white" 
    textAlign="center" 
    position="absolute" 
    top="5cm" 
    left="3cm">
    Texto sobre imagen
</x-styled-div>
```

### Ejemplo 3: Contenedor con Opacidad
```blade
<x-styled-div 
    width="100%" 
    height="100%" 
    backgroundColor="black" 
    opacity="0.7" 
    position="absolute" 
    top="0" 
    left="0">
    Overlay semi-transparente
</x-styled-div>
```

### Ejemplo 4: Texto Estilizado
```blade
<x-styled-div 
    fontSize="1.5cm" 
    lineHeight="1.8cm" 
    fontFamily="'NotoSerifBold'" 
    color="#333333" 
    textAlign="center" 
    width="18cm" 
    position="absolute" 
    top="2cm" 
    left="1.5cm">
    Título Principal del Documento
</x-styled-div>
```

## Ventajas del Componente

1. **Reutilización**: Evita repetir código de estilos inline
2. **Legibilidad**: Hace el código más limpio y fácil de leer
3. **Mantenimiento**: Cambios centralizados en un solo archivo
4. **Flexibilidad**: Soporta todos los parámetros CSS comunes
5. **Compatibilidad**: Mantiene compatibilidad con atributos adicionales de HTML

## Notas de Implementación

- El componente combina automáticamente todos los estilos en un atributo `style`
- Solo se incluyen las propiedades que tienen valores definidos
- Es compatible con atributos HTML adicionales usando `{{ $attributes }}`
- El contenido se incluye usando `{{ $slot }}`

## Migración de Código Existente

### Antes (div tradicional):
```blade
<div style="width: 10cm; height: 5cm; background-color: red; position: absolute; top: 2cm; left: 3cm;">
    Contenido
</div>
```

### Después (componente styled-div):
```blade
<x-styled-div 
    width="10cm" 
    height="5cm" 
    backgroundColor="red" 
    position="absolute" 
    top="2cm" 
    left="3cm">
    Contenido
</x-styled-div>
```
