/**
 * Global Custom Dropdown System
 * Automatically converts all .form-select elements to custom dropdowns
 * 
 * This system:
 * 1. Automatically detects all .form-select elements on page load
 * 2. Converts them to beautiful card-style dropdowns
 * 3. Maintains full form compatibility with hidden inputs
 * 4. Supports keyboard navigation and accessibility
 * 5. Handles dynamic content updates via reinitializeCustomDropdowns()
 * 
 * Usage:
 * - Simply use <select class="form-select"> in your HTML
 * - The system automatically converts it to a custom dropdown
 * - All form functionality remains the same
 * - Call reinitializeCustomDropdowns() after adding new selects dynamically
 */

class CustomDropdown {
    constructor(selectElement) {
        this.select = selectElement;
        this.wrapper = null;
        this.toggle = null;
        this.menu = null;
        this.selectedText = null;
        this.arrow = null;
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        // Create wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'form-select-wrapper';
        
        // Create toggle button
        this.toggle = document.createElement('div');
        this.toggle.className = 'dropdown-toggle';
        this.toggle.setAttribute('tabindex', '0');
        this.toggle.setAttribute('role', 'combobox');
        this.toggle.setAttribute('aria-expanded', 'false');
        this.toggle.setAttribute('aria-haspopup', 'listbox');
        this.toggle.addEventListener('click', () => this.toggleDropdown());
        
        // Create selected text span
        this.selectedText = document.createElement('span');
        this.selectedText.className = 'selected-text';
        
        // Create arrow
        this.arrow = document.createElement('i');
        this.arrow.className = 'fas fa-chevron-down dropdown-arrow';
        
        // Create menu
        this.menu = document.createElement('div');
        this.menu.className = 'dropdown-menu';
        this.menu.setAttribute('role', 'listbox');
        this.menu.setAttribute('tabindex', '0');
        
        // Search functionality
        this.searchTerm = '';
        this.filteredOptions = [];
        
        // Assemble toggle
        this.toggle.appendChild(this.selectedText);
        this.toggle.appendChild(this.arrow);
        
        // Assemble wrapper
        this.wrapper.appendChild(this.toggle);
        this.wrapper.appendChild(this.menu);
        
        // Insert wrapper after select
        this.select.parentNode.insertBefore(this.wrapper, this.select.nextSibling);
        
        // Store instance reference on wrapper for later access
        this.wrapper.customDropdownInstance = this;
        
        // Hide original select
        this.select.style.display = 'none';
        
        // Copy classes from select to wrapper
        if (this.select.classList.contains('is-invalid')) {
            this.wrapper.classList.add('is-invalid');
        }
        
        // Populate options
        this.populateOptions();
        
        // Set initial value
        this.setInitialValue();
        
        // Add event listeners
        this.addEventListeners();
    }
    
    populateOptions() {
        this.menu.innerHTML = '';
        
        const options = this.select.querySelectorAll('option');
        options.forEach((option, index) => {
            const optionElement = document.createElement('div');
            optionElement.className = 'dropdown-option';
            optionElement.dataset.value = option.value;
            optionElement.textContent = option.textContent;
            
            if (option.selected) {
                optionElement.classList.add('selected');
                this.selectedText.textContent = option.textContent;
            }
            
            optionElement.addEventListener('click', () => {
                this.selectOption(option.value, option.textContent);
            });
            
            this.menu.appendChild(optionElement);
        });
    }
    
    setInitialValue() {
        const selectedOption = this.select.querySelector('option:checked');
        if (selectedOption && selectedOption.value !== '') {
            this.selectedText.textContent = selectedOption.textContent;
            this.toggle.classList.add('has-selection');
        } else {
            // Check if there's a data-title attribute for filter dropdowns
            const filterTitle = this.select.getAttribute('data-title');
            if (filterTitle) {
                this.selectedText.textContent = filterTitle;
                this.toggle.classList.remove('has-selection');
            } else {
                // Get the first option text as default
                const firstOption = this.select.querySelector('option');
                if (firstOption) {
                    this.selectedText.textContent = firstOption.textContent;
                } else {
                    this.selectedText.textContent = 'Select an option';
                }
                this.toggle.classList.remove('has-selection');
            }
        }
    }
    
    selectOption(value, text) {
        // Clear search timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
            this.searchTerm = '';
        }
        
        // Show all options again
        this.menu.querySelectorAll('.dropdown-option').forEach(option => {
            option.style.display = '';
        });
        
        // Update selected text
        this.selectedText.textContent = text;
        
        // Update original select
        this.select.value = value;
        
        // Update option selection
        this.menu.querySelectorAll('.dropdown-option').forEach(option => {
            option.classList.remove('selected');
            if (option.dataset.value === value) {
                option.classList.add('selected');
            }
        });
        
        // Add/remove has-selection class for filter rows
        if (value && value !== '') {
            this.toggle.classList.add('has-selection');
        } else {
            this.toggle.classList.remove('has-selection');
        }
        
        // Close dropdown
        this.closeDropdown();
        
        // Trigger change event
        const changeEvent = new Event('change', { bubbles: true });
        this.select.dispatchEvent(changeEvent);
    }
    
    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        // Close all other dropdowns
        document.querySelectorAll('.form-select-wrapper.open').forEach(dropdown => {
            dropdown.classList.remove('open');
        });
        
        this.wrapper.classList.add('open');
        this.toggle.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
    }
    
    closeDropdown() {
        this.wrapper.classList.remove('open');
        this.toggle.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }
    
    filterAndHighlightByTyping() {
        if (!this.isOpen) {
            this.openDropdown();
        }
        
        // Reset search timeout if exists
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Filter options based on search term
        const options = Array.from(this.menu.querySelectorAll('.dropdown-option'));
        const searchLower = this.searchTerm.toLowerCase();
        
        options.forEach(option => {
            const text = option.textContent.toLowerCase();
            if (searchLower && !text.includes(searchLower)) {
                option.style.display = 'none';
            } else {
                option.style.display = '';
                option.classList.remove('selected');
            }
        });
        
        // Highlight first visible option
        const visibleOptions = options.filter(opt => opt.style.display !== 'none');
        if (visibleOptions.length > 0) {
            visibleOptions[0].classList.add('selected');
            visibleOptions[0].scrollIntoView({ block: 'nearest' });
        }
        
        // Clear search term after 3 seconds of inactivity
        this.searchTimeout = setTimeout(() => {
            this.searchTerm = '';
            // Show all options again
            options.forEach(option => {
                option.style.display = '';
                option.classList.remove('selected');
            });
        }, 10000);
    }
    
    addEventListeners() {
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });
        
        // Handle keyboard navigation on toggle button
        this.toggle.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (this.isOpen) {
                    // If open, select the highlighted option
                    const options = Array.from(this.menu.querySelectorAll('.dropdown-option'));
                    const visibleOptions = options.filter(opt => opt.style.display !== 'none');
                    const selectedOpt = visibleOptions.find(opt => opt.classList.contains('selected'));
                    
                    if (selectedOpt) {
                        this.selectOption(selectedOpt.dataset.value, selectedOpt.textContent);
                    }
                } else {
                    // If closed, open the dropdown
                    this.toggleDropdown();
                }
            } else if (e.key === 'Escape') {
                this.closeDropdown();
            } else if (e.key === 'Tab') {
                // Close dropdown when tabbing away
                this.closeDropdown();
            } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!this.isOpen) {
                    this.openDropdown();
                }
                // For arrow keys, navigate options immediately
                const options = Array.from(this.menu.querySelectorAll('.dropdown-option'));
                const visibleOptions = options.filter(opt => opt.style.display !== 'none');
                
                if (visibleOptions.length > 0) {
                    const selectedOpt = visibleOptions.find(opt => opt.classList.contains('selected'));
                    const currentIndex = selectedOpt ? visibleOptions.indexOf(selectedOpt) : -1;
                    
                    if (e.key === 'ArrowDown') {
                        const nextIndex = (currentIndex + 1) % visibleOptions.length;
                        const nextOption = visibleOptions[nextIndex];
                        this.highlightOption(options.indexOf(nextOption));
                        nextOption.scrollIntoView({ block: 'nearest' });
                    } else if (e.key === 'ArrowUp') {
                        const prevIndex = currentIndex <= 0 ? visibleOptions.length - 1 : currentIndex - 1;
                        const prevOption = visibleOptions[prevIndex];
                        this.highlightOption(options.indexOf(prevOption));
                        prevOption.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
                // Typing a letter - start search
                e.preventDefault();
                this.searchTerm += e.key;
                this.filterAndHighlightByTyping();
            } else if (e.key === 'Backspace') {
                e.preventDefault();
                this.searchTerm = this.searchTerm.slice(0, -1);
                this.filterAndHighlightByTyping();
            }
        });
        
        // Add listener for active element to show focus
        this.toggle.addEventListener('focus', () => {
            this.toggle.classList.add('dropdown-toggle-focused');
        });
        
        this.toggle.addEventListener('blur', (e) => {
            // Don't blur when clicking on menu options
            if (!this.wrapper.contains(e.relatedTarget)) {
                this.toggle.classList.remove('dropdown-toggle-focused');
                // Reset search term when closing
                setTimeout(() => {
                    if (!this.isOpen) {
                        this.searchTerm = '';
                    }
                }, 100);
            }
        });
        
        // Handle option keyboard navigation (only for specific keys)
        this.menu.addEventListener('keydown', (e) => {
            // Only handle Enter and Escape in menu, let other keys go to toggle button
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                e.stopPropagation();
                const options = Array.from(this.menu.querySelectorAll('.dropdown-option'));
                const visibleOptions = options.filter(opt => opt.style.display !== 'none');
                const selectedOpt = visibleOptions.find(opt => opt.classList.contains('selected'));
                const currentIndex = selectedOpt ? visibleOptions.indexOf(selectedOpt) : -1;
                
                if (currentIndex >= 0 && visibleOptions[currentIndex]) {
                    const option = visibleOptions[currentIndex];
                    this.selectOption(option.dataset.value, option.textContent);
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                this.closeDropdown();
                this.toggle.focus();
            }
        });
    }
    
    highlightOption(index) {
        const options = this.menu.querySelectorAll('.dropdown-option');
        options.forEach((option, i) => {
            option.classList.toggle('selected', i === index);
        });
    }
    
    // Public method to update options dynamically
    updateOptions(options) {
        this.menu.innerHTML = '';
        options.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = 'dropdown-option';
            optionElement.dataset.value = option.value;
            optionElement.textContent = option.text;
            
            optionElement.addEventListener('click', () => {
                this.selectOption(option.value, option.text);
            });
            
            this.menu.appendChild(optionElement);
        });
    }
    
    // Public method to refresh options from the original select
    refreshOptions() {
        this.populateOptions();
    }
    
    // Public method to set value programmatically
    setValue(value) {
        const option = this.menu.querySelector(`[data-value="${value}"]`);
        if (option) {
            this.selectOption(value, option.textContent);
        }
    }
    
    // Public method to get current value
    getValue() {
        return this.select.value;
    }
}

// Initialize custom dropdowns when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCustomDropdowns();
});

// Function to initialize all custom dropdowns
function initializeCustomDropdowns() {
    const selectElements = document.querySelectorAll('.form-select');
    selectElements.forEach(select => {
        if (!select.dataset.customDropdownInitialized) {
            new CustomDropdown(select);
            select.dataset.customDropdownInitialized = 'true';
        }
    });
}

// Function to refresh existing dropdowns after dynamic content is updated
function refreshCustomDropdowns() {
    console.log('Refreshing custom dropdowns...');
    const selectElements = document.querySelectorAll('.form-select[data-custom-dropdown-initialized]');
    console.log('Found', selectElements.length, 'initialized dropdowns');
    
    selectElements.forEach(select => {
        // Find the wrapper and refresh its options
        const wrapper = select.nextElementSibling;
        if (wrapper && wrapper.classList.contains('form-select-wrapper')) {
            // Get the custom dropdown instance from the wrapper
            const customDropdown = wrapper.customDropdownInstance;
            if (customDropdown) {
                console.log('Refreshing dropdown for:', select.id || select.name);
                customDropdown.refreshOptions();
            }
        }
    });
}

// Function to reinitialize dropdowns after dynamic content is added
function reinitializeCustomDropdowns() {
    const selectElements = document.querySelectorAll('.form-select:not([data-custom-dropdown-initialized])');
    selectElements.forEach(select => {
        // Respect opt-out flag
        if (select.dataset.skipCustomDropdown === 'true') {
            return;
        }
        new CustomDropdown(select);
        select.dataset.customDropdownInitialized = 'true';
    });
}

// Export for global use
window.CustomDropdown = CustomDropdown;
window.initializeCustomDropdowns = initializeCustomDropdowns;
window.reinitializeCustomDropdowns = reinitializeCustomDropdowns;
window.refreshCustomDropdowns = refreshCustomDropdowns;
