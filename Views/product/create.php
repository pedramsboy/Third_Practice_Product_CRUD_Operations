<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Product</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: block;
            padding: 12px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            text-align: center;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            border-color: #667eea;
            background: #e9ecef;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .file-preview {
            margin-top: 10px;
            text-align: center;
        }

        .file-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Create New Product</h1>

    <div class="form-container">
        <h2>Product Information</h2>

        <div id="message"></div>

        <form id="productForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter product description..."></textarea>
            </div>

            <div class="form-group">
                <label for="size">Size *</label>
                <input type="number" id="size" name="size" required min="1">
            </div>

            <div class="form-group">
                <label for="file">Product Image/File</label>
                <div class="file-input-wrapper">
                    <input type="file" id="file" name="file" accept="image/jpeg,image/png,image/gif,application/pdf">
                    <div class="file-input-label" id="fileLabel">
                        <i class="fas fa-cloud-upload-alt"></i> Choose file (JPEG, PNG, GIF, PDF - Max 1MB)
                    </div>
                </div>
                <div class="file-preview" id="filePreview"></div>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                Create Product
            </button>
        </form>
    </div>

    <div id="productsContainer" class="products-grid">
        <!-- Product cards will be displayed here -->
    </div>
</div>

<script>
    // File input preview
    document.getElementById('file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('filePreview');
        const fileLabel = document.getElementById('fileLabel');

        if (file) {
            fileLabel.textContent = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `<div>File: ${file.name} (${file.type})</div>`;
            }
        } else {
            fileLabel.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Choose file (JPEG, PNG, GIF, PDF - Max 1MB)';
            preview.innerHTML = '';
        }
    });

    // Form submission
    document.getElementById('productForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const messageDiv = document.getElementById('message');

        // Show loading state
        submitBtn.textContent = 'Creating Product...';
        submitBtn.classList.add('loading');

        try {
            const formData = new FormData(this);

            const response = await fetch('/api/products', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                // Success
                showMessage('Product created successfully!', 'success');
                this.reset();
                document.getElementById('filePreview').innerHTML = '';
                document.getElementById('fileLabel').innerHTML =
                    '<i class="fas fa-cloud-upload-alt"></i> Choose file (JPEG, PNG, GIF, PDF - Max 1MB)';

                // Load updated products list
                loadProducts();
            } else {
                // Error
                const errorMsg = result.errors ? Object.values(result.errors).flat().join(', ') :
                    result.error || 'Failed to create product';
                showMessage(errorMsg, 'error');
            }
        } catch (error) {
            showMessage('Network error: ' + error.message, 'error');
        } finally {
            // Reset button
            submitBtn.textContent = 'Create Product';
            submitBtn.classList.remove('loading');
        }
    });

    function showMessage(message, type) {
        const messageDiv = document.getElementById('message');
        messageDiv.innerHTML = `<div class="message ${type}">${message}</div>`;

        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }
    }

    // Load existing products
    async function loadProducts() {
        try {
            const response = await fetch('/api/products');
            const products = await response.json();

            const container = document.getElementById('productsContainer');

            if (products.length === 0) {
                container.innerHTML = '<div class="card">No products found. Create your first product!</div>';
                return;
            }

            container.innerHTML = products.map(product => `
                    <div class="card">
                        ${product.file_path && product.file_type && product.file_type.startsWith('image/') ?
                `<img src="${product.file_path}" alt="${product.name}" onerror="this.style.display='none'">` :
                product.file_path ?
                    `<div style="background: #f8f9fa; height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 15px;">
                                <div style="text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px;">📄</div>
                                    <div>${product.file_name || 'File'}</div>
                                </div>
                            </div>` :
                    `<div style="background: #f8f9fa; height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 15px;">
                                <div style="text-align: center; color: #6c757d;">
                                    <div style="font-size: 48px; margin-bottom: 10px;">📦</div>
                                    <div>No Image</div>
                                </div>
                            </div>`
            }
                        <h3>${product.name}</h3>
                        <p><strong>Description:</strong> ${product.description || 'No description'}</p>
                        <p><strong>Size:</strong> ${product.size}</p>
                        ${product.file_name ? `<p><strong>File:</strong> ${product.file_name}</p>` : ''}
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                            <small style="color: #666;">ID: ${product.id}</small>
                        </div>
                    </div>
                `).join('');
        } catch (error) {
            console.error('Error loading products:', error);
            document.getElementById('productsContainer').innerHTML =
                '<div class="card error">Error loading products</div>';
        }
    }

    // Load products when page loads
    document.addEventListener('DOMContentLoaded', loadProducts);
</script>
</body>
</html>