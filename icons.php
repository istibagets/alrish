<?php
/**
 * Al Rish Al Khaleeji - Vector SVG Icon System
 * 100% Vector SVGs - Zero Emojis
 */

function icon(string $name, string $classes = 'w-5 h-5', string $extraAttrs = ''): string {
    // Custom Machinery & Material Icons (Adaptive Color Masking - Supports brand-500 orange, slate-400 gray, etc.)
    $customPngIcons = [
        'uv-printer' => 'icons/uv-printer.png',
        'laser-cutter' => 'icons/laser-cutter.png',
        'cnc-spindle' => 'icons/cnc-spindle.png',
        'vinyl-roll' => 'icons/vinyl-roll.png',
    ];

    if (isset($customPngIcons[$name])) {
        $maskPath = $customPngIcons[$name];
        return '<span class="' . $classes . ' inline-block bg-current shrink-0" style="-webkit-mask: url(\'' . $maskPath . '\') no-repeat center / contain; mask: url(\'' . $maskPath . '\') no-repeat center / contain;" ' . $extraAttrs . '></span>';
    }

    $icons = [
        // Navigation & Core
        'dashboard' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>',
        'cart' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
        'terminal' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>',
        'bell' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
        'shield' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
        'shield-alert' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
        'settings' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>',
        'history' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'ledger' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        
        // Actions & CRUD
        'search' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
        'plus' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
        'plus-circle' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',
        'edit' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
        'trash' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>',
        'save' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>',
        'refresh' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>',
        'download' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
        'filter' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>',
        'close' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        'check' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="20 6 9 17 4 12"/></svg>',
        'alert' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        'info' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
        'logout' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
        'database' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',

        // Transactions & Inbound/Outbound
        'inbound' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>',
        'outbound' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>',
        'adjust' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
        'gift' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>',

        // Objects, Machinery & Category Icons
        'box' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
        'zap' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
        'droplet' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>',
        'printer' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="6 9 6 2 18 2 18 9"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>',
        'cpu' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>',
        'tool' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
        'layers' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
        'scissors' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>',
        'disc' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>',
        'sliders' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>',
        'tag' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
        'truck' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        'activity' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
        'sparkles' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>',
        'archive' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>',
        
        'file-text' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        'calendar' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'user' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'building' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="12"/><line x1="15" y1="22" x2="15" y2="12"/><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>',

        // Chevrons & Arrows
        'chevron-down' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="6 9 12 15 18 9"/></svg>',
        'chevron-right' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="9 18 15 12 9 6"/></svg>',
        'chevron-up' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><polyline points="18 15 12 9 6 15"/></svg>',
        'arrow-left' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>',
        'arrow-right' => '<svg class="' . $classes . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" ' . $extraAttrs . '><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>',
    ];

    return $icons[$name] ?? $icons['box'];
}

function getAvailableCategoryIcons(): array {
    return [
        'uv-printer' => 'Laser',
        'laser-cutter' => 'Flatbed',
        'cnc-spindle' => 'CNC',
        'vinyl-roll' => 'Vinyl / Media Roll',
        'box' => 'Box / Package',
        'printer' => 'Printer / Plotter',
        'zap' => 'Laser / Electric',
        'droplet' => 'Ink / Liquid',
        'cpu' => 'CNC / Electronics',
        'tool' => 'Tools / Hardware',
        'layers' => 'Media / Acrylic / Rolls',
        'scissors' => 'Cutter / Plotter',
        'sliders' => 'Parts & Spare',
        'shield' => 'Guarantees / Safety',
        'tag' => 'Accessories / Vinyl',
        'truck' => 'Supply / Freight',
        'activity' => 'Sensors / Motors',
        'sparkles' => 'Special / Premium',
        'archive' => 'Bulk Storage',
        'cart' => 'POS / Sales'
    ];
}

function getCategoryIcon(string $iconCode, string $classes = 'w-5 h-5'): string {
    $normalized = strtolower(trim($iconCode));
    
    // Direct matches
    $available = getAvailableCategoryIcons();
    if (isset($available[$normalized])) {
        return icon($normalized, $classes);
    }
    
    // Keyword and legacy alias matches
    if (strpos($normalized, 'uv') !== false || strpos($normalized, 'dtf') !== false) return icon('uv-printer', $classes);
    if (strpos($normalized, 'laser') !== false || $iconCode === '⚡' || $iconCode === 'zap') return icon('laser-cutter', $classes);
    if (strpos($normalized, 'spindle') !== false || strpos($normalized, 'milling') !== false || strpos($normalized, 'router') !== false || strpos($normalized, 'cnc') !== false || $iconCode === '🗜️') return icon('cnc-spindle', $classes);
    if (strpos($normalized, 'vinyl') !== false || strpos($normalized, 'roll') !== false || strpos($normalized, 'film') !== false) return icon('vinyl-roll', $classes);
    if (strpos($normalized, 'ink') !== false || $iconCode === '🧪' || $iconCode === 'droplet') return icon('droplet', $classes);
    if (strpos($normalized, 'printer') !== false || strpos($normalized, 'plotter') !== false || strpos($normalized, 'eco') !== false || $iconCode === '⚙️') return icon('printer', $classes);
    if (strpos($normalized, 'lamination') !== false || strpos($normalized, 'heat') !== false) return icon('tool', $classes);
    if (strpos($normalized, 'media') !== false || strpos($normalized, 'sheet') !== false) return icon('vinyl-roll', $classes);
    if (strpos($normalized, 'cut') !== false) return icon('scissors', $classes);
    
    return icon('box', $classes);
}
