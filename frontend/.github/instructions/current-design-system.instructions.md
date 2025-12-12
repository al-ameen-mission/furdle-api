---
description: Current Design System for Face Recognition Frontend
alwaysApply: true
applyTo: "**"
downloadedFrom: Custom for project
version: 1.0
---

# Current Design System

This project uses **daisyUI 5** with **Tailwind CSS 4** for a modern, responsive UI. The design system emphasizes clean, accessible components with a focus on face recognition functionality.

## Core Principles

1. **Responsiveness**: All components must be fully responsive using Tailwind CSS breakpoints (sm, md, lg, xl).
2. **Accessibility**: Use semantic HTML, ARIA labels, and keyboard navigation where applicable.
3. **Consistency**: Stick to daisyUI classes and Tailwind utilities; avoid custom CSS unless necessary.
4. **Performance**: Optimize for low-end devices, especially in face detection components.
5. **Theme Support**: Leverage daisyUI's theme system for light/dark modes.

## Installation and Setup

- daisyUI 5 requires Tailwind CSS 4.
- Install via `npm i -D daisyui@latest`.
- In CSS: `@import "tailwindcss"; @plugin "daisyui";`
- For custom themes, use the plugin syntax as shown in daisyUI docs.

## Usage Rules

1. Use daisyUI component classes (e.g., `btn`, `modal`, `navbar`) for primary UI elements.
2. Customize with Tailwind utility classes (e.g., `btn btn-primary w-full`).
3. For specificity issues, use `!` suffix sparingly (e.g., `bg-red-500!`).
4. Prefer daisyUI semantic colors (`primary`, `secondary`, etc.) over Tailwind colors for theme consistency.
5. Ensure responsive layouts with breakpoint prefixes (e.g., `md:flex`, `sm:hidden`).
6. Use `flex` and `grid` for layouts, with responsive prefixes.
7. For images, use placeholders like `https://picsum.photos/200/300` if needed.
8. Avoid custom fonts unless essential; stick to system fonts.
9. Do not add `bg-base-100 text-base-content` to body unless necessary.
10. Follow Refactoring UI best practices for design decisions.

## Color System

Use daisyUI semantic colors:

- `primary`: Main brand color
- `primary-content`: Text on primary
- `secondary`: Secondary brand color
- `secondary-content`: Text on secondary
- `accent`: Accent color
- `accent-content`: Text on accent
- `neutral`: Neutral dark
- `neutral-content`: Text on neutral
- `base-100` to `base-300`: Surface colors
- `base-content`: Text on base
- `info`, `success`, `warning`, `error`: Status colors with content variants

Rules:
- Use semantic names for theme adaptability.
- Avoid Tailwind colors like `red-500` for text to prevent readability issues in dark themes.
- `*-content` colors ensure good contrast.

## Component Guidelines

### Button (`btn`)
- Classes: `btn`, `btn-primary`, `btn-outline`, `btn-ghost`, `btn-link`, `btn-active`, `btn-disabled`, `btn-wide`, `btn-block`, `btn-square`, `btn-circle`
- Sizes: `btn-xs` to `btn-xl`
- Usage: On `<button>`, `<a>`, `<input>`; include icons before/after text.

### Modal (`modal`)
- Classes: `modal`, `modal-box`, `modal-action`, `modal-backdrop`, `modal-toggle`, `modal-open`, `modal-top`, etc.
- Usage: For dialogs; use `<dialog>` with `showModal()` and `close()`.

### Navbar (`navbar`)
- Classes: `navbar`, `navbar-start`, `navbar-center`, `navbar-end`
- Usage: Sticky header with responsive layout; hide elements on small screens.

### Form Elements
- Input: `input`, `input-primary`, etc., sizes `input-xs` to `input-xl`
- Select: `select`, `select-primary`, etc.
- Textarea: `textarea`, `textarea-primary`, etc.
- Checkbox: `checkbox`, `checkbox-primary`, etc.
- Radio: `radio`, `radio-primary`, etc.
- Range: `range`, `range-primary`, etc.

### Layout Components
- Card: `card`, `card-title`, `card-body`, `card-actions`
- Alert: `alert`, `alert-info`, etc.
- Badge: `badge`, `badge-primary`, etc.
- Avatar: `avatar`, `avatar-online`, etc.
- Loading: `loading`, `loading-spinner`, etc.

### Navigation
- Tabs: `tabs`, `tab`, `tab-active`
- Menu: `menu`, `menu-title`, `menu-dropdown`
- Breadcrumbs: `breadcrumbs`

### Other
- Drawer: `drawer`, `drawer-toggle`, `drawer-content`, `drawer-side`
- Dropdown: `dropdown`, `dropdown-content`
- Tooltip: `tooltip`
- Toast: `toast`
- Progress: `progress`
- Table: `table`, `table-zebra`

## Project-Specific Rules

- **Header**: Responsive navbar with logo/title on left, desktop nav in center, actions on right. On small screens, use modal menu for nav/admin/theme.
- **FaceCapture**: Optimize for performance on low-end devices; use modal for status if needed.
- **Theme Toggle**: Use `useTheme` context; icon changes based on `isDark`.
- **Icons**: Use `@iconify-icon/react` with hugeicons or mdi icons.
- **Responsive Breakpoints**:
  - `sm:` (640px+): Show title, theme toggle.
  - `md:` (768px+): Show desktop nav, admin link.
  - Below `md`: Modal menu for navigation.

## Config Example

```css
@import "tailwindcss";
@plugin "daisyui" {
  themes: light --default, dark --prefersdark;
  root: ":root";
  include: ;
  exclude: ;
  prefix: ;
  logs: true;
}
```

For custom themes, define CSS variables as per daisyUI docs.

## Best Practices

- Test responsiveness on various screen sizes.
- Ensure keyboard navigation for modals and dropdowns.
- Use `aria-label` for icon-only buttons.
- Keep bundle size in mind; lazy-load heavy components if possible.
- Follow React best practices: use hooks for state, refs for DOM access.

This design system ensures a cohesive, performant UI for the face recognition app. Refer to daisyUI docs for full component details.