class CategoryForm {
    constructor() {
        this.form = document.getElementById('categoryForm');
        this.nameInput = document.getElementById('name');
        this.slugInput = document.getElementById('slug');
        this.imageInput = document.getElementById('image');
        this.imagePreview = document.getElementById('imagePreview');
        this.metaTitleInput = document.getElementById('meta_title');
        this.metaDescInput = document.getElementById('meta_description');
        this.colorInput = document.getElementById('color');
        this.colorPreview = document.getElementById('colorPreview');

        this.initializeEventListeners();
        this.initializeValidation();
        this.initializeImageUpload();
        this.initializeColorPicker();
    }

    initializeEventListeners() {
        // Auto-generate slug from name
        this.nameInput.addEventListener('input', () => {
            const slug = this.generateSlug(this.nameInput.value);
            this.slugInput.value = slug;
            this.updateMetaTitle();
        });

        // Character count for meta fields
        this.metaTitleInput.addEventListener('input', () => this.updateCharCount(this.metaTitleInput, 60));
        this.metaDescInput.addEventListener('input', () => this.updateCharCount(this.metaDescInput, 160));

        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Color picker changes
        this.colorInput.addEventListener('input', () => this.updateColorPreview());
    }

    initializeValidation() {
        // Client-side validation rules
        this.validationRules = {
            name: {
                required: true,
                minLength: 2,
                maxLength: 255
            },
            meta_title: {
                maxLength: 60
            },
            meta_description: {
                maxLength: 160
            },
            image: {
                maxSize: 2 * 1024 * 1024, // 2MB
                allowedTypes: ['image/jpeg', 'image/png', 'image/webp']
            }
        };
    }

    initializeImageUpload() {
        if (this.imageInput) {
            this.imageInput.addEventListener('change', (e) => this.handleImageUpload(e));

            // Drag and drop support
            const dropZone = document.getElementById('dropZone');
            if (dropZone) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, this.preventDefaults);
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.add('border-blue-500'));
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-blue-500'));
                });

                dropZone.addEventListener('drop', (e) => this.handleDrop(e));
            }
        }
    }

    initializeColorPicker() {
        if (this.colorInput) {
            this.updateColorPreview();
        }
    }

    // Utility Methods
    generateSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }

    updateCharCount(input, maxLength) {
        const charCount = input.value.length;
        const remainingChars = maxLength - charCount;
        const helpText = input.nextElementSibling;
        
        if (helpText) {
            helpText.textContent = `${remainingChars} characters remaining`;
            helpText.classList.toggle('text-yellow-600', remainingChars < 10);
            helpText.classList.toggle('text-red-600', remainingChars < 0);
        }
    }

    updateMetaTitle() {
        if (!this.metaTitleInput.value) {
            this.metaTitleInput.value = this.nameInput.value;
            this.updateCharCount(this.metaTitleInput, 60);
        }
    }

    updateColorPreview() {
        const color = this.colorInput.value;
        this.colorPreview.style.backgroundColor = color;
        
        // Update text color for contrast
        const rgb = this.hexToRgb(color);
        const brightness = this.calculateBrightness(rgb);
        this.colorPreview.style.color = brightness > 128 ? '#000000' : '#FFFFFF';
    }

    // Image Handling Methods
    handleImageUpload(e) {
        const file = e.target.files[0];
        if (file) {
            this.validateAndPreviewImage(file);
        }
    }

    handleDrop(e) {
        const file = e.dataTransfer.files[0];
        if (file) {
            this.validateAndPreviewImage(file);
        }
    }

    validateAndPreviewImage(file) {
        if (!this.validationRules.image.allowedTypes.includes(file.type)) {
            this.showError('Please upload a valid image file (JPEG, PNG, or WebP)');
            return;
        }

        if (file.size > this.validationRules.image.maxSize) {
            this.showError('Image size should be less than 2MB');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            this.imagePreview.src = e.target.result;
            this.imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // Form Submission
    async handleSubmit(e) {
        e.preventDefault();
        
        if (!this.validateForm()) {
            return;
        }

        try {
            const formData = new FormData(this.form);
            const response = await fetch(this.form.action, {
                method: this.form.method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                window.location.href = response.url;
            } else {
                const data = await response.json();
                this.showError(data.message || 'An error occurred while saving the category');
            }
        } catch (error) {
            this.showError('An unexpected error occurred');
        }
    }

    // Validation Methods
    validateForm() {
        let isValid = true;
        
        // Clear previous errors
        this.clearErrors();

        // Validate required fields
        Object.keys(this.validationRules).forEach(field => {
            const input = document.getElementById(field);
            if (!input) return;

            const rules = this.validationRules[field];

            if (rules.required && !input.value) {
                this.showFieldError(field, 'This field is required');
                isValid = false;
            }

            if (rules.minLength && input.value.length < rules.minLength) {
                this.showFieldError(field, `Minimum length is ${rules.minLength} characters`);
                isValid = false;
            }

            if (rules.maxLength && input.value.length > rules.maxLength) {
                this.showFieldError(field, `Maximum length is ${rules.maxLength} characters`);
                isValid = false;
            }
        });

        return isValid;
    }

    // Error Handling Methods
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4';
        errorDiv.textContent = message;
        this.form.insertBefore(errorDiv, this.form.firstChild);
    }

    showFieldError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.createElement('p');
        errorDiv.className = 'mt-2 text-sm text-red-600';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
        field.classList.add('border-red-500');
    }

    clearErrors() {
        const errorMessages = this.form.querySelectorAll('.text-red-600');
        errorMessages.forEach(error => error.remove());
        
        const errorFields = this.form.querySelectorAll('.border-red-500');
        errorFields.forEach(field => field.classList.remove('border-red-500'));
    }

    // Utility Methods
    hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    calculateBrightness({r, g, b}) {
        return (r * 299 + g * 587 + b * 114) / 1000;
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    new CategoryForm();
});