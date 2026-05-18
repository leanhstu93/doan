import './bootstrap';
import * as Tabler from '@tabler/core';

// Initialize Tabler
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdowns
    const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdowns.forEach(dropdown => {
        new Tabler.Dropdown(dropdown);
    });

    // Initialize modals
    const modals = document.querySelectorAll('[data-bs-toggle="modal"]');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target) {
                const modalInstance = new Tabler.Modal(target);
                modalInstance.show();
            }
        });
    });

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new Tabler.Tooltip(tooltip);
    });
});

// Export Tabler for use in other scripts
window.Tabler = Tabler;
