# Design System Documentation

## Overview

This document describes the token-based design system that supports light and dark modes across the PEO application.

## Architecture

### CSS Tokens (`resources/css/theme-tokens.css`)
All colors, shadows, and other design values are defined as CSS custom properties (variables).

- **Light Mode**: Default browser colors or `:root` in normal environments
- **Dark Mode**: Activated via `@media (prefers-color-scheme: dark)` or `.dark` class
- **Role-Based Colors**: Specific colors for different user roles
- **Event-Type Colors**: Specific colors for different event types in the timeline

### Tailwind Integration (`tailwind.config.js`)
Extended Tailwind config with:
- CSS variable integration for Tailwind classes
- Dark mode support (`darkMode: 'class'`)
- Color definitions mapped to CSS variables
- Shadow tokens for consistent elevation

## Color Tokens

### Surface Colors
```css
--color-bg-primary      /* Main background */
--color-bg-secondary    /* Secondary background (cards, inputs) */
--color-bg-tertiary     /* Tertiary background (hover states) */
--color-bg-hover        /* Hover state background */
--color-bg-active       /* Active state background */
```

### Text Colors
```css
--color-text-primary    /* Main text */
--color-text-secondary  /* Secondary text (labels) */
--color-text-tertiary   /* Tertiary text (muted) */
--color-text-muted      /* Very muted text */
--color-text-inverse    /* Inverse text (on colored backgrounds) */
```

### Border Colors
```css
--color-border-primary   /* Main border */
--color-border-secondary /* Secondary border */
--color-border-light     /* Light border */
```

### Status Colors
Each status has 4 tokens: `bg`, `text`, `border`, `dot`

- `--color-success-*`
- `--color-error-*`
- `--color-warning-*`
- `--color-info-*`

### Action Colors
```css
--color-action-primary          /* Primary action button */
--color-action-primary-hover    /* Primary button hover */
--color-action-primary-active   /* Primary button active */
--color-action-secondary        /* Secondary button */
--color-action-secondary-hover  /* Secondary button hover */
--color-action-secondary-text   /* Secondary button text */
```

## Role-Based Tokens

```css
--role-admin-bg
--role-admin-text
--role-admin-dot

--role-contractor-bg
--role-contractor-text
--role-contractor-dot

--role-resident-engineer-bg
--role-resident-engineer-text
--role-resident-engineer-dot

--role-provincial-engineer-bg
--role-provincial-engineer-text
--role-provincial-engineer-dot

--role-mtqa-bg
--role-mtqa-text
--role-mtqa-dot
```

## Event-Type Tokens

Each event type has 3 tokens: `bg`, `text`, `dot`

- `--event-submitted-*`
- `--event-updated-*`
- `--event-deleted-*`
- `--event-assigned-*`
- `--event-re-reviewed-*`
- `--event-pe-noted-*`
- `--event-mtqa-decided-*`
- `--event-approved-*`
- `--event-disapproved-*`
- `--event-status-changed-*`

## Reusable Components

### Badge for Events
```blade
<x-badge-event :event="$log->event" label="Custom Label" />
```

### Badge for Roles
```blade
<x-badge-role :role="$log->actor_role" label="Custom Label" />
```

### Avatar
```blade
<x-avatar :name="$user->name" size="sm" />    {{-- sm, md, lg --}}
```

### Card
```blade
<x-card hoverable>
    Content here
</x-card>
```

### Stat Card
```blade
<x-stat-card 
    label="Total Events" 
    value="42" 
    type="default" 
/>
{{-- Types: default, success, error, warning, info --}}
```

### Button
```blade
<x-button variant="primary" size="md">
    Click Me
</x-button>
{{-- Variants: primary, secondary --}}
{{-- Sizes: sm, md, lg --}}
```

### Input Field
```blade
<x-input 
    name="search" 
    label="Search" 
    type="text"
    error="This field is required"
/>
```

### Select Field
```blade
<x-select name="role" label="Actor Role">
    <option value="admin">Admin</option>
    <option value="contractor">Contractor</option>
</x-select>
```

### Section Header
```blade
<x-section-header 
    title="Page Title" 
    subtitle="Breadcrumb > Section"
    :action="$actionButton"
/>
```

## Utility Classes

### Surface Classes
```html
<div class="surface-primary">   {{-- Primary background --}}</div>
<div class="surface-secondary"> {{-- Secondary background --}}</div>
<div class="surface-tertiary">  {{-- Tertiary background --}}</div>
```

### Text Classes
```html
<p class="text-primary">    {{-- Primary text --}}</p>
<p class="text-secondary">  {{-- Secondary text --}}</p>
<p class="text-tertiary">   {{-- Tertiary text --}}</p>
<p class="text-muted">      {{-- Muted text --}}</p>
```

### Border Classes
```html
<div class="border border-primary">     {{-- Primary border --}}</div>
<div class="border border-secondary">   {{-- Secondary border --}}</div>
```

### Card Classes
```html
<div class="card">          {{-- Basic card --}}</div>
<div class="card card-hover"> {{-- Hoverable card --}}</div>
```

### Badge Classes
```html
{{-- Event badges --}}
<span class="badge tag-event-approved">Approved</span>
<span class="badge tag-event-disapproved">Disapproved</span>

{{-- Role badges --}}
<span class="badge tag-role-admin">Admin</span>
<span class="badge tag-role-contractor">Contractor</span>
```

### Button Classes
```html
<button class="btn btn-primary">Primary Button</button>
<button class="btn btn-secondary">Secondary Button</button>
```

## Dark Mode Implementation

### Automatic (System Preference)
The app automatically detects OS dark mode preference using `@media (prefers-color-scheme: dark)`.

### Manual (Class-Based)
Add `.dark` class to `<html>` or any parent element:

```html
<!-- Enable dark mode -->
<html class="dark">

<!-- Disable dark mode -->
<html>
```

## Using CSS Variables in Custom Styles

```css
.my-custom-component {
    background-color: var(--color-bg-primary);
    color: var(--color-text-primary);
    border: 1px solid var(--color-border-primary);
    box-shadow: var(--shadow-sm);
}
```

## Using Tokens in Tailwind Classes

```html
<!-- Using extended color palette -->
<div class="bg-surface-primary text-text-primary border border-border-primary">
    Content
</div>

<!-- Using shadows -->
<div class="shadow-token-sm hover:shadow-token-lg">
    Hover me
</div>

<!-- Using rings -->
<div class="ring-token">Ring with token color</div>
```

## Migration Guide

When updating existing views:

### Before:
```blade
<div class="bg-white ring-1 ring-gray-200 shadow-sm">
    <p class="text-gray-900">Title</p>
    <p class="text-gray-500">Subtitle</p>
</div>
```

### After:
```blade
<x-card>
    <p class="text-text-primary">Title</p>
    <p class="text-text-secondary">Subtitle</p>
</x-card>
```

## Best Practices

1. **Always use tokens**: Never hardcode colors like `#fff` or `bg-white`
2. **Use components**: For common patterns, use reusable components
3. **CSS variables**: Use `var(--color-*)` in custom CSS
4. **Test dark mode**: Always preview changes in both light and dark modes
5. **Semantic naming**: Use semantic names (`primary`, `secondary`) instead of color names
6. **Consistency**: Keep consistent spacing, shadows, and typography using tokens

## Examples

### Activity Log Table
```blade
<div class="rounded-xl bg-surface-primary ring-1 ring-border-primary shadow-token-sm">
    <div class="border-b border-border-primary px-4 py-3">
        <h2 class="text-sm font-semibold text-text-primary">Activity Log</h2>
    </div>
    <table class="divide-y divide-border-primary">
        @foreach ($logs as $log)
            <tr class="hover:bg-surface-secondary">
                <td class="px-4 py-3 text-text-primary">
                    {{ $log->description }}
                </td>
            </tr>
        @endforeach
    </table>
</div>
```

### Status Badge
```blade
@php
    $statusBadgeClass = match($item->status) {
        'approved'    => 'tag-event-approved',
        'disapproved' => 'tag-event-disapproved',
        default       => 'tag-event-status-changed',
    };
@endphp

<span class="badge {{ $statusBadgeClass }}">
    {{ $item->status }}
</span>
```

### Form Section
```blade
<div class="rounded-xl bg-surface-primary ring-1 ring-border-primary shadow-token-sm p-4">
    <h3 class="text-lg font-semibold text-text-primary mb-4">Form Title</h3>
    
    <x-input name="field1" label="Field 1" />
    <x-select name="field2" label="Field 2">
        <option>Option 1</option>
    </x-select>
    
    <div class="flex gap-2 mt-6">
        <x-button variant="primary">Submit</x-button>
        <x-button variant="secondary">Cancel</x-button>
    </div>
</div>
```

## Testing Dark Mode

1. **System Setting**: Go to OS dark mode settings
2. **Browser DevTools**: 
   - Chrome/Edge: F12 → More tools → Rendering → Emulate CSS media feature prefers-color-scheme → select "dark"
3. **Manual Toggle**: Add to `<html>` element:
   ```html
   <html class="dark">
   ```

## Future Enhancements

- [ ] User preference storage (localStorage)
- [ ] Manual dark mode toggle UI
- [ ] Additional color schemes (high contrast, etc.)
- [ ] Animation/transition tokens
- [ ] Responsive typography tokens
- [ ] Spacing scale tokens
