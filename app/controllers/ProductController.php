<?php
namespace App\Controllers;

use Core\Auth;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Category;

class ProductController {
    public function __construct() {
        Auth::requireLogin();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $categoryId = $_GET['category_id'] ?? null;
        $products = Product::all($search, $categoryId);
        $categories = Product::getCategories();
        
        view('products/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId
        ]);
    }

    public function form() {
        $id = $_GET['id'] ?? null;
        $product = null;
        $recipe = [];
        
        if ($id) {
            $product = Product::find($id);
            if (!$product) {
                Auth::setFlash('error', 'Producto no encontrado.');
                redirect('/products');
            }
            $recipe = Product::getRecipe($id);
        }

        $categories = Product::getCategories();
        $rawMaterials = RawMaterial::all();

        // format recipe for easier lookup in views: [material_id => quantity]
        $recipeMap = [];
        foreach ($recipe as $item) {
            $recipeMap[$item['raw_material_id']] = $item['quantity'];
        }

        view('products/form', [
            'product' => $product,
            'categories' => $categories,
            'rawMaterials' => $rawMaterials,
            'recipeMap' => $recipeMap
        ]);
    }

    public function save() {
        $id = $_POST['id'] ?? null;
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $categoryId = $_POST['category_id'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;
        $useRecipe = isset($_POST['use_recipe']) ? 1 : 0;
        $ingredients = $_POST['recipe'] ?? []; // Array: [material_id => qty]

        if (empty($name) || empty($code) || $price <= 0 || empty($categoryId)) {
            Auth::setFlash('error', 'Código, nombre, categoría y un precio válido son requeridos.');
            redirect($id ? "/products/edit?id=$id" : '/products/create');
        }

        // Handle Image Upload
        $imageName = $_POST['existing_image'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = __DIR__ . '/../../uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $imageName = $newFileName;
                    // Delete old image if updating
                    if ($id) {
                        $oldProd = Product::find($id);
                        if ($oldProd && !empty($oldProd['image']) && file_exists($uploadDir . $oldProd['image'])) {
                            @unlink($uploadDir . $oldProd['image']);
                        }
                    }
                }
            } else {
                Auth::setFlash('error', 'Formato de imagen inválido. Solo JPG, JPEG, PNG y WEBP.');
                redirect($id ? "/products/edit?id=$id" : '/products/create');
            }
        }

        $productData = [
            'category_id' => $categoryId,
            'code' => $code,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $imageName,
            'active' => $active,
            'use_recipe' => $useRecipe
        ];

        try {
            if ($id) {
                Product::update($id, $productData);
                $productId = $id;
                Auth::setFlash('success', 'Producto actualizado con éxito.');
            } else {
                $productId = Product::create($productData);
                Auth::setFlash('success', 'Producto registrado con éxito.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (usually duplicate entry)
                Auth::setFlash('error', 'El código de producto ingresado ya existe. Utilice uno único.');
            } else {
                Auth::setFlash('error', 'Error al guardar el producto: ' . $e->getMessage());
            }
            redirect($id ? "/products/edit?id=$id" : '/products/create');
        }

        // Save recipe
        if ($useRecipe) {
            Product::saveRecipe($productId, $ingredients);
        } else {
            // Delete recipe if not using recipes
            Product::saveRecipe($productId, []);
        }

        redirect('/products');
    }

    public function delete() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $product = Product::find($id);
            if ($product) {
                // Delete image file if exists
                if (!empty($product['image'])) {
                    $imagePath = __DIR__ . '/../../uploads/products/' . $product['image'];
                    if (file_exists($imagePath)) {
                        @unlink($imagePath);
                    }
                }
                Product::delete($id);
                Auth::setFlash('success', 'Producto eliminado.');
            }
        }
        redirect('/products');
    }

    /**
     * Show the dedicated Price History management page.
     * Lists all products with their price histories.
     */
    public function priceHistory() {
        $search = $_GET['search'] ?? '';
        $products = Product::all($search);
        $priceHistories = [];
        foreach ($products as $prod) {
            $priceHistories[$prod['id']] = Product::getPriceHistory((int)$prod['id']);
        }
        view('sales/price_history', [
            'products'      => $products,
            'priceHistories'=> $priceHistories,
            'search'        => $search
        ]);
    }

    /**
     * Add or update a price history record (AJAX/JSON).
     * POST: product_id, price, effective_date, [history_id] (for edit)
     */
    public function savePriceHistory() {
        header('Content-Type: application/json');
        $productId     = (int)($_POST['product_id'] ?? 0);
        $price         = (float)($_POST['price'] ?? 0);
        $effectiveDate = trim($_POST['effective_date'] ?? '');
        $historyId     = (int)($_POST['history_id'] ?? 0);

        if ($productId <= 0 || $price <= 0 || empty($effectiveDate)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos. Producto, precio y fecha son requeridos.']);
            exit;
        }

        if (!Product::find($productId)) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
            exit;
        }

        try {
            if ($historyId > 0) {
                // Edit existing
                $ok = Product::updatePriceHistory($historyId, $price, $effectiveDate);
                if (!$ok) {
                    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el precio histórico.']);
                    exit;
                }
                $msg = 'Precio histórico actualizado correctamente.';
            } else {
                // Add new
                Product::addPriceHistory($productId, $price, $effectiveDate);
                $msg = 'Precio histórico agregado correctamente.';
            }

            // Return updated history for this product
            $history = Product::getPriceHistory($productId);
            echo json_encode(['success' => true, 'message' => $msg, 'history' => $history]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Delete a price history record (AJAX/JSON).
     * POST: history_id, product_id
     */
    public function deletePriceHistory() {
        header('Content-Type: application/json');
        $historyId = (int)($_POST['history_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);

        if ($historyId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de registro inválido.']);
            exit;
        }

        $ok = Product::deletePriceHistory($historyId);
        if (!$ok) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar el precio base (primer precio registrado).']);
            exit;
        }

        $history = Product::getPriceHistory($productId);
        echo json_encode(['success' => true, 'message' => 'Precio eliminado.', 'history' => $history]);
        exit;
    }
}
