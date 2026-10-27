# 🏛️ SETC — Sistema Estatal de Citas, Trámites y Expedientes  
## Design System & UI Documentation

> **Civic Enterprise Portal** — Guía completa del sistema de diseño para todos los módulos del portal ciudadano e institucional de SETC.

---

## 📋 Índice

1. [Filosofía de Diseño](#filosofía-de-diseño)
2. [Colores](#colores)
3. [Tipografía](#tipografía)
4. [Espaciado & Layout](#espaciado--layout)
5. [Elevación & Sombras](#elevación--sombras)
6. [Bordes & Formas](#bordes--formas)
7. [Componentes](#componentes)
8. [Módulos del Sistema](#módulos-del-sistema)
9. [Accesibilidad](#accesibilidad)
10. [Tecnologías](#tecnologías)

---

## Filosofía de Diseño

El sistema de diseño encarna la intersección entre **confiabilidad institucional**, **rigor enterprise moderno** y **administración pública empática**. Reencuadra los flujos de trabajo gubernamentales —frecuentemente percibidos como intimidantes y opacos— en servicios digitales directos, acogedores y empoderadores.

La estética se basa en el **minimalismo cívico moderno** combinado con **precisión de software técnico**, con influencias directas de:

- **gov.uk** — claridad radical en comunicación gubernamental
- **Vercel** — estructura monocromática limpia
- **shadcn/ui** — disciplina de componentes

### Principios Rectores

| Principio | Descripción |
|-----------|-------------|
| **Autoridad sin fricción** | Superficies limpias, tipografía arquitectónica y espacio en blanco meticuloso reemplazan el desorden burocrático |
| **Accesibilidad radical** | Construido para superar WCAG 2.1 AA, garantizando claridad para todos los ciudadanos |
| **Claridad empática** | Microcopy humanizado, validación inequívoca y divulgación progresiva por pasos reducen la carga cognitiva |

---

## Colores

### Paleta Principal (Material Design 3 — Esquema "Civic Enterprise Portal")

#### Tokens de Color

```css
/* Superficies */
--surface:                  #f8f9ff;
--surface-dim:              #cbdbf5;
--surface-bright:           #f8f9ff;
--surface-container-lowest: #ffffff;
--surface-container-low:    #eff4ff;
--surface-container:        #e5eeff;
--surface-container-high:   #dce9ff;
--surface-container-highest:#d3e4fe;
--background:               #f8f9ff;

/* Texto sobre superficies */
--on-surface:               #0b1c30;
--on-surface-variant:       #44474d;
--inverse-surface:          #213145;
--inverse-on-surface:       #eaf1ff;

/* Bordes */
--outline:                  #74777e;
--outline-variant:          #c4c6ce;
--surface-tint:             #4a5f7f;

/* Colores primarios — Deep Navy */
--primary:                  #001229;
--on-primary:               #ffffff;
--primary-container:        #0f2744;
--on-primary-container:     #798fb1;
--inverse-primary:          #b2c8ed;
--primary-fixed:            #d4e3ff;
--primary-fixed-dim:        #b2c8ed;
--on-primary-fixed:         #021c39;
--on-primary-fixed-variant: #324866;

/* Colores secundarios — Civic Royal Blue */
--secondary:                #0051d5;
--on-secondary:             #ffffff;
--secondary-container:      #316bf3;
--on-secondary-container:   #fefcff;
--secondary-fixed:          #dbe1ff;
--secondary-fixed-dim:      #b4c5ff;
--on-secondary-fixed:       #00174b;
--on-secondary-fixed-variant:#003ea8;

/* Colores terciarios — Emerald Gov */
--tertiary:                 #00160c;
--on-tertiary:              #ffffff;
--tertiary-container:       #002d1d;
--on-tertiary-container:    #1fa072;
--tertiary-fixed:           #85f8c4;
--tertiary-fixed-dim:       #68dba9;
--on-tertiary-fixed:        #002114;
--on-tertiary-fixed-variant:#005137;

/* Estados de error */
--error:                    #ba1a1a;
--on-error:                 #ffffff;
--error-container:          #ffdad6;
--on-error-container:       #93000a;
```

### Semántica de Color por Rol

| Rol | Color | Hex | Uso |
|-----|-------|-----|-----|
| **Primary** | Deep Navy | `#0f2744` | Cabeceras, navegación, anclajes visuales |
| **Secondary** | Civic Royal Blue | `#0051d5` / `#2563eb` | Acciones clave, links, estados activos, focus |
| **Background** | Zinc Slate | `#f8f9ff` | Fondo general de página |
| **Surface** | Blanco | `#ffffff` | Cards, paneles, formularios |
| **Success** | Emerald | `#059669` / `#ecfdf5` | Estados aprobados, verificados |
| **Warning** | Amber | `#d97706` / `#fffbeb` | Pendientes, alertas, estados intermedios |
| **Danger** | Rose | `#e11d48` / `#fff1f2` | Errores críticos, acciones destructivas |
| **Text Primary** | Charcoal Slate | `#0f172a` / `#0b1c30` | Cuerpo de texto principal, ratio >7:1 |
| **Text Muted** | Slate | `#64748b` / `#44474d` | Texto auxiliar, labels, hints |
| **Border** | Slate Border | `#e2e8f0` / `#cbd5e1` | Divisores y bordes de inputs |

---

## Tipografía

El sistema tipográfico combina la geometría humana de **Plus Jakarta Sans** para superficies de display con la neutralidad utilitaria de **Inter** para formularios, tablas y entornos de datos densos.

### Fuentes

| Familia | Rol | Importación |
|---------|-----|-------------|
| **Plus Jakarta Sans** | Headlines, títulos de modal, salutaciones de sección | `wght@100..900` |
| **Inter** | Body, labels, formularios, badges, datos | `wght@100..900` |
| **Material Symbols Outlined** | Iconografía de sistema | Variable font |

```html
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
```

### Escala Tipográfica

| Token | Familia | Tamaño | Peso | Line-height | Uso |
|-------|---------|--------|------|-------------|-----|
| `headline-xl` | Plus Jakarta Sans | 36px | 700 | 44px | Títulos de página principales (desktop) |
| `headline-xl-mobile` | Plus Jakarta Sans | 28px | 700 | 36px | Títulos de página (mobile) |
| `headline-lg` | Plus Jakarta Sans | 30px | 600 | 38px | Títulos de sección grandes (desktop) |
| `headline-lg-mobile` | Plus Jakarta Sans | 24px | 600 | 32px | Títulos de sección (mobile) |
| `headline-md` | Plus Jakarta Sans | 22px | 600 | 28px | Subtítulos, cabeceras de card |
| `headline-sm` | Plus Jakarta Sans | 18px | 600 | 24px | Labels de sección, cabeceras secundarias |
| `body-lg` | Inter | 18px | 400 | 28px | Texto introductorio, descripciones |
| `body-md` | Inter | 16px | 400 | 24px | Texto de cuerpo estándar |
| `body-sm` | Inter | 14px | 400 | 20px | Texto secundario, descripciones de campo |
| `label-md` | Inter | 14px | 500 | 20px | Labels de formulario, ítems de navegación |
| `label-sm` | Inter | 12px | 600 | 16px | Etiquetas de estado, breadcrumbs, chips |
| `code-sm` | Inter | 13px | 500 | 18px | Folios, códigos de referencia, IDs técnicos |

> **Ritmo vertical**: Todos los `line-height` se alinean a una grilla base de **4px/8px**.

---

## Espaciado & Layout

### Grid Responsivo

| Breakpoint | Columnas | Rango |
|------------|----------|-------|
| **Mobile** | 4 columnas | `< 640px` |
| **Tablet** | 8 columnas | `640px – 1024px` |
| **Desktop** | 12 columnas | `>= 1024px` |

- **Max-width general**: `1280px` (flujos enterprise)  
- **Max-width formularios**: `800px` (formas transaccionales multi-paso)

### Tokens de Espaciado

```css
--space-xs:    0.25rem;   /*  4px — Micro separaciones */
--space-sm:    0.5rem;    /*  8px — Gap entre íconos y texto */
--space-md:    1rem;      /* 16px — Padding interno de componentes */
--space-lg:    1.5rem;    /* 24px — Gutter entre columnas */
--space-xl:    2rem;      /* 32px — Margen entre secciones */
--gutter:      1.5rem;    /* 24px — Gutter horizontal de página */
--gutter-sm:   1rem;      /* 16px — Gutter en mobile */
--margin:      2rem;      /* 32px — Margen externo de contenedor */
--margin-sm:   1rem;      /* 16px — Margen externo mobile */
```

### Reglas de Layout de Formularios

- **Mobile**: columna única enforced
- **Desktop**: máximo 2 columnas para preservar flujo cognitivo lineal
- Campos relacionados agrupados con separación `space-xs` a `space-sm`
- Paneles administrativos usan `space-lg` a `space-xl`

---

## Elevación & Sombras

El sistema de jerarquía visual usa capas tonales combinadas con bordes de bajo contraste y sombras ambientales de bajo peso.

| Nivel | Contexto | Fondo | Sombra |
|-------|----------|-------|--------|
| **Base (Ground)** | Background de página | `#f8fafc` | Sin sombra |
| **Level 1** | Cards, secciones de datos | `#ffffff` + borde `#e2e8f0` 1px | `0 1px 3px 0 rgba(15,23,42,0.04), 0 1px 2px -1px rgba(15,23,42,0.02)` |
| **Level 2** | Hover states, menús flotantes | Borde nítido | `0 10px 15px -3px rgba(15,23,42,0.06), 0 4px 6px -4px rgba(15,23,42,0.03)` |
| **Level 3** | Modales, drawers | `backdrop-blur` + overlay `rgba(15,39,68,0.4)` | `0 20px 25px -5px rgba(15,23,42,0.1), 0 8px 10px -6px rgba(15,23,42,0.04)` |

---

## Bordes & Formas

```css
--rounded-sm:      0.25rem;   /*  4px */
--rounded-DEFAULT: 0.5rem;    /*  8px */
--rounded-md:      0.75rem;   /* 12px */
--rounded-lg:      1rem;      /* 16px */
--rounded-xl:      1.5rem;    /* 24px */
--rounded-full:    9999px;    /* Pill */
```

| Radio | Aplicación |
|-------|------------|
| `rounded-xl` (12-16px) | Cards principales, paneles, diálogos modales |
| `rounded-lg` (8px) | Inputs, botones, controles atómicos |
| `rounded-full` (pill) | Badges de estado, chips, indicadores de rol |

---

## Componentes

### Botones

#### Primary
```
Fondo:    #0f2744 (Deep Navy) o #2563eb (Civic Royal Blue)
Texto:    #ffffff
Radio:    rounded-lg (8px)
Altura:   40px desktop / 48px mobile
Peso:     font-weight: 500
Focus:    outline 2px en Royal Blue con 2px offset
```

#### Secondary / Outlined
```
Fondo:    #ffffff
Borde:    #e2e8f0 (1px)
Texto:    Deep Navy
Hover:    fondo #f1f5f9
```

#### Destructivo
```
Fondo:    #e11d48 (Rose)
Texto:    #ffffff
Nota:     Siempre acompañado de modal de confirmación explícito
```

---

### Inputs & Selects

```
Borde:      #cbd5e1 (1px)
Fondo:      #ffffff
Radio:      rounded-lg (8px)
Altura:     40px
Label:      persistente, alto contraste (#0f172a), sobre el input
Helper:     texto auxiliar empático en #64748b
Focus:      ring 2px en Civic Blue (#2563eb), latencia cero
```

---

### Badges de Estado (Chips)

| Estado | Fondo | Texto | Dot |
|--------|-------|-------|-----|
| **Verificado / Completado** | `#ecfdf5` | `#065f46` | `#10b981` |
| **En Revisión / Pendiente** | `#fffbeb` | `#92400e` | `#d97706` |
| **Acción Requerida / Bloqueado** | `#fff1f2` | `#9f1239` | `#e11d48` |

Formato: pill `rounded-full`, tinte suave de fondo + texto oscuro semántico.

---

### Steppers & Indicadores de Progreso

- Timeline lineal numerado, horizontal
- **Completado**: ícono check en Emerald Green (`#10b981` / `tertiary-fixed`)
- **Activo actual**: fondo Primary (`#001229`), texto blanco, número en Civic Blue
- **Pendiente**: contorno slate subdued, fondo `surface-container-low`, opacidad reducida

---

### Cards & Contenedores de Datos

```
Fondo:      #ffffff
Radio:      rounded-xl
Borde:      #e2e8f0 (1px)
Estructura: Header / Body / Footer separados por divisores 1px
            o tintes de fondo distintos para toolbars de acción masiva
```

---

### Navegación Principal (Header)

- **Fijo** en top-0, `z-50`, `backdrop-blur-xl`
- Altura: `80px` (h-20)
- Fondo: `surface-container-lowest/90` con `box-shadow` sutil
- Links de navegación: `rounded-lg`, hover con `surface-container-low`
- Link activo: `bg-surface-container`, `font-semibold`

---

### Sidebar (Panel Enterprise Interno)

- Ancho fijo lateral izquierdo
- Fondo: Deep Navy (`#001229`) o superficies oscuras
- Navegación agrupada por categoría operativa:
  - **Operación & Dictamen**
  - **Supervisión & SLA**
  - **Administración GovTech**
- Item activo: resaltado en Civic Blue
- Badge de alertas: número en amber/orange para ítems con observaciones

---

## Módulos del Sistema

### 1. Portal Ciudadano — Inicio de Trámites y Carga de Expedientes
- **Rol**: Punto de entrada ciudadano para inicio de trámites
- **Layout**: Pantalla completa, max-width 1280px, navegación pública superior
- **Componentes clave**: Cards de trámites, buscador de servicios, estado de expediente
- **Audiencia**: Ciudadanos (CURP autenticado)

---

### 2. Agendación de Cita y Pase Digital de Turno
- **Rol**: Flujo multi-paso para agendar citas presenciales o virtuales
- **Layout**: 12-col grid — selector (7 cols) + preview de pase digital (5 cols)
- **Componentes clave**:
  - Stepper de 3 pasos (Tipo > Fecha & Horario > Confirmación)
  - Cards de modalidad (Presencial / Virtual ITSE GovMeet)
  - Calendario mensual con disponibilidad en tiempo real
  - Selector de horarios por turno mañana/tarde
  - **Pase Digital QR** con datos del especialista asignado
  - Exportación a Google Calendar / iCal / WhatsApp / PDF
- **Colores destacados**: Secondary para slots disponibles, tertiary-fixed para horarios seleccionados, error para agotados

---

### 3. Seguimiento del Trámite y Timeline del Expediente
- **Rol**: Vista de seguimiento del estado del expediente ciudadano
- **Layout**: Pantalla full con header de expediente activo + timeline cronológico
- **Componentes clave**:
  - Banner de contexto con folio y estado general
  - Timeline progresivo de fases del trámite
  - Chips de estado (Verificado / En Revisión / Acción Requerida)
  - Botones de acción: Copiar Folio, Imprimir Hoja de Ruta
- **Dato técnico**: Código de expediente formato `SETC-YYYY-NNNNN`

---

### 4. Dashboard Operativo del Revisor y Mesa de Dictamen
- **Rol**: Panel interno para revisores técnicos (L1/L2)
- **Layout**: Sidebar fijo izquierdo + área de contenido principal
- **Componentes clave**:
  - KPI Cards (expedientes asignados, tiempos SLA, pendientes)
  - Tabla de expedientes con prioridad y SLA visible
  - Panel de dictamen con acciones: Aprobar / Observar / Rechazar
  - Filtros por estado, tipo de trámite y jurisdicción
- **Audiencia**: Revisores Jurídicos L1/L2, Supervisores

---

### 5. Configuración de Trámites y Requisitos — Constructor TUPA
- **Rol**: Módulo de configuración normativa de trámites (back-office)
- **Layout**: Workspace de construcción de formularios de requisitos
- **Componentes clave**:
  - Constructor drag-and-drop de requisitos
  - Árbol de trámites con versiones normativas
  - Vista previa de requisitos para el ciudadano
- **Audiencia**: Administradores de Normativa, Configuradores TUPA

---

### 6. Gestión y Administración de Usuarios y Roles
- **Rol**: RBAC completo del sistema (usuarios, roles, permisos, auditoría PKI)
- **Layout**: Sidebar + tabla de usuarios + panel lateral de ficha de usuario
- **Componentes clave**:
  - KPI bar: Total usuarios, Revisores Técnicos, Supervisores, Administradores, Bloqueadas
  - Tabs de filtro por rol: Todos / Ciudadanos / Revisores L1/L2 / Supervisores / Admins
  - Tabla paginada con badge de rol, sede y estado de autenticación
  - **Panel de Ficha RBAC**: Avatar, certificado PKI, matriz de privilegios por módulo
  - Acciones masivas: Reasignar Rol / Forzar 2FA / Suspender
  - Soporte FIDO2, 2FA OTP, Hardware Key
- **Audiencia**: Administradores Centrales

---

### 7. Matriz de Revisores y Distribución de Carga
- **Rol**: Balanceo de carga técnica y asignación pericial de expedientes
- **Layout**: Split view — columna de revisores (izq) + cola de expedientes pendientes (der)
- **Componentes clave**:
  - KPI bar: Revisores activos, expedientes en cola, tiempo promedio, equidad de carga (Gini)
  - Cards de revisor con barra de carga (libre/óptimo/saturado/pausa)
  - Lista de expedientes con prioridad, SLA countdown, sugerencia pericial
  - **Modo de Asignación Inteligente** (algoritmo heurístico L2 v4.2)
  - Botón "Rebalancear Cola Activa" para redistribución automática
- **Métricas**: Equidad 94% Gini 0.08, efectividad de asignación 94%
- **Audiencia**: Supervisores L1/L2, Jefes de Dictamen

---

### 8. Auditoría y Bitácora de Cambios Normativos
- **Rol**: Registro inmutable de cambios en configuración normativa y accesos
- **Layout**: Tabla de eventos con filtros temporales y por actor
- **Componentes clave**:
  - Línea de tiempo de auditoría con actor, acción, timestamp
  - Filtros por módulo, usuario, rango de fechas
  - Exportación de logs para cumplimiento normativo
- **Estándar**: Conforme R.M. 082-2024-PCM y marco de Gobernanza Digital

---

## Accesibilidad

| Estándar | Nivel |
|----------|-------|
| WCAG 2.1 | **AA** (objetivo: superar) |
| Ratio de contraste texto | **>7:1** (APCA/WCAG AAA) |
| Indicadores de focus | Ring 2px en Civic Blue, offset visible |
| Soporte ARIA | `aria-current`, `aria-label` en controles interactivos |
| Navegación por teclado | Tab order lógico en todos los flujos de formulario |
| Autenticación | FIDO2 / 2FA OTP / Hardware Key / PKI |
| Encriptación | SHA-256, TLS 1.3 Red Gubernamental |

---

## Tecnologías

| Tecnología | Uso |
|------------|-----|
| **TailwindCSS** (CDN) | Clases utilitarias en los prototipos HTML |
| **Material Symbols Outlined** | Iconografía (variable font) |
| **Plus Jakarta Sans** | Tipografía de display |
| **Inter** | Tipografía de datos y formularios |
| **Material Design 3** | Sistema de colores (tokens) |
| **Laravel (Blade/Vite)** | Framework backend + bundler frontend |

### Configuración de Tailwind (tokens del sistema)

```js
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "primary":                "#001229",
        "primary-container":      "#0f2744",
        "secondary":              "#0051d5",
        "secondary-container":    "#316bf3",
        "tertiary":               "#00160c",
        "tertiary-container":     "#002d1d",
        "surface":                "#f8f9ff",
        "surface-container-lowest":"#ffffff",
        "surface-container-low":  "#eff4ff",
        "surface-container":      "#e5eeff",
        "surface-container-high": "#dce9ff",
        "on-surface":             "#0b1c30",
        "on-surface-variant":     "#44474d",
        "error":                  "#ba1a1a",
        // ... (ver DESIGN.md para lista completa)
      },
      borderRadius: {
        "DEFAULT": "0.25rem",
        "lg":      "0.5rem",
        "xl":      "0.75rem",
        "full":    "9999px",
      },
      spacing: {
        "space-xs": "0.25rem",
        "space-sm": "0.5rem",
        "space-md": "1rem",
        "space-lg": "1.5rem",
        "space-xl": "2rem",
        "gutter":   "1.5rem",
        "gutter-sm":"1rem",
        "margin":   "2rem",
        "margin-sm":"1rem",
      },
      fontSize: {
        "headline-xl":     ["36px", { lineHeight: "44px", fontWeight: "700" }],
        "headline-lg":     ["30px", { lineHeight: "38px", fontWeight: "600" }],
        "headline-md":     ["22px", { lineHeight: "28px", fontWeight: "600" }],
        "headline-sm":     ["18px", { lineHeight: "24px", fontWeight: "600" }],
        "body-lg":         ["18px", { lineHeight: "28px", fontWeight: "400" }],
        "body-md":         ["16px", { lineHeight: "24px", fontWeight: "400" }],
        "body-sm":         ["14px", { lineHeight: "20px", fontWeight: "400" }],
        "label-md":        ["14px", { lineHeight: "20px", fontWeight: "500" }],
        "label-sm":        ["12px", { lineHeight: "16px", fontWeight: "600" }],
        "code-sm":         ["13px", { lineHeight: "18px", fontWeight: "500" }],
      },
    },
  },
};
```

---

## Estructura de Archivos en `/docs`

```
docs/
└── stitch_setc_enterprise_portal_system (1).zip
    └── stitch_setc_enterprise_portal_system/
        ├── civic_enterprise_portal/
        │   └── DESIGN.md                          <- Sistema de diseño completo (YAML + guía)
        ├── agendaci_n_de_cita_y_pase_digital_de_turno/
        │   ├── code.html                          <- Prototipo HTML (39 KB)
        │   └── screen.png                         <- Captura de pantalla
        ├── auditor_a_y_bit_cora_de_cambios_normativos_enterprise_portal/
        │   ├── code.html                          <- Prototipo HTML (47 KB)
        │   └── screen.png
        ├── configuraci_n_de_tr_mites_y_requisitos_constructor_tupa_enterprise_portal/
        │   ├── code.html                          <- Prototipo HTML (49 KB)
        │   └── screen.png
        ├── dashboard_operativo_del_revisor_y_mesa_de_dictamen/
        │   ├── code.html                          <- Prototipo HTML (36 KB)
        │   └── screen.png
        ├── gesti_n_y_administraci_n_de_usuarios_y_roles_enterprise_portal/
        │   ├── code.html                          <- Prototipo HTML (57 KB)
        │   └── screen.png
        ├── matriz_de_revisores_y_distribuci_n_de_carga_enterprise_portal/
        │   ├── code.html                          <- Prototipo HTML (53 KB)
        │   └── screen.png
        ├── portal_ciudadano_inicio_de_tr_mites_y_carga_de_expedientes/
        │   ├── code.html                          <- Prototipo HTML (35 KB)
        │   └── screen.png
        └── seguimiento_del_tr_mite_y_timeline_del_expediente/
            ├── code.html                          <- Prototipo HTML (41 KB)
            └── screen.png
```

---

*Generado desde `docs/stitch_setc_enterprise_portal_system (1).zip` — Civic Enterprise Portal Design System v1.0*
